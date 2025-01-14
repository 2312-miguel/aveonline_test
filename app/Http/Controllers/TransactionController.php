<?php

namespace App\Http\Controllers;

use App\Interfaces\AccountRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $accountRepo;
    protected $transactionRepo;

    public function __construct(
        AccountRepositoryInterface $accountRepo,
        TransactionRepositoryInterface $transactionRepo
    ) {
        $this->accountRepo     = $accountRepo;
        $this->transactionRepo = $transactionRepo;
    }

    /**
     * Display the transactions of a user in a view (HTML).
     * Used in /users/{userId}/transactions
     *
     * @param int $userId
     * @return \Illuminate\Contracts\View\View
     */
    public function showUserTransactions($userId)
    {
        $user = User::findOrFail($userId);

        // Session-based authentication: Only the owner or Admin
        if (Auth::id() !== $user->id) {
            return redirect()->back()->withErrors(['error' => 'You do not have permission.']);
        }

        $account = $this->accountRepo->getUserAccount($user);
        if (!$account) {
            // Instead of a simple array, use collect([]) to use methods
            $transactions = collect([]);
            $message = 'The user does not have a registered account.';

            return view('transactions.index', compact('transactions', 'message'));
        }

        $transactions = $account->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $message = '';
        if ($transactions->count() === 0) {
            $message = 'This user has no registered transactions.';
        }

        return view('transactions.index', compact('transactions', 'message'));
    }

    /**
     * Create transactions (deposit, withdraw) via API.
     * Endpoint: POST /api/transactions
     * JSON: { "type": "deposit", "amount": 100, "user_id": 1 }
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $this->validateTransactionRequest($request);
        
        $authenticatedUser = Auth::user();
        
        $targetUser = $this->findTargetUser($request->input('user_id'));
        if (!$targetUser['success']) {
            return response()->json(['error' => $targetUser['message']], $targetUser['code']);
        }
        
        $sourceAccount = $this->getSourceAccount($authenticatedUser);
        if (!$sourceAccount['success']) {
            return response()->json(['error' => $sourceAccount['message']], $sourceAccount['code']);
        }
        
        $transactionType = $request->input('type');
        $amount = $request->input('amount');

        if ($transactionType === 'transfer') {
            return $this->handleTransfer($authenticatedUser, $targetUser['data'], $sourceAccount['data'], $amount);
        }

        if ($transactionType === 'withdraw') {
            $validationResult = $this->validateWithdrawal($authenticatedUser, $targetUser['data'], $sourceAccount['data'], $amount);
            if (!$validationResult['success']) {
                return response()->json(['error' => $validationResult['message']], $validationResult['code']);
            }
        }

        return $this->processTransaction($sourceAccount['data'], $transactionType, $amount);
    }

    private function validateTransactionRequest(Request $request): array
    {
        return $request->validate([
            'type'    => 'required|in:deposit,withdraw,transfer',
            'amount'  => 'required|numeric|min:1',
            'user_id' => 'required|integer'
        ]);
    }

    private function findTargetUser(int $userId): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Target user not found.',
                'code' => 404
            ];
        }
        return [
            'success' => true,
            'data' => $user
        ];
    }

    private function getSourceAccount(User $user): array
    {
        $account = $this->accountRepo->getUserAccount($user);
        if (!$account) {
            return [
                'success' => false,
                'message' => 'Your account not found.',
                'code' => 404
            ];
        }
        return [
            'success' => true,
            'data' => $account
        ];
    }

    private function handleTransfer(User $authenticatedUser, User $targetUser, $sourceAccount, float $amount)
    {
        if ($authenticatedUser->id === $targetUser->id) {
            return response()->json(['error' => 'Cannot transfer to your own account.'], 400);
        }

        $targetAccount = $this->accountRepo->getUserAccount($targetUser);
        if (!$targetAccount) {
            return response()->json(['error' => 'Target account not found.'], 404);
        }

        if ($sourceAccount->balance < $amount) {
            return response()->json(['error' => 'Insufficient balance for transfer.'], 400);
        }

        try {
            $withdrawalTransaction = $this->createWithdrawalTransaction($sourceAccount, $amount, $targetUser);
            $depositTransaction = $this->createDepositTransaction($targetAccount, $amount, $authenticatedUser);
            
            $this->accountRepo->withdrawBalance($sourceAccount, $amount);
            $this->accountRepo->addBalance($targetAccount, $amount);

            return response()->json([
                'message'     => 'Transfer successful.',
                'transaction' => $withdrawalTransaction,
                'balance'     => $sourceAccount->balance
            ]);
        } catch (\Exception $e) {
            $this->revertTransactions($withdrawalTransaction ?? null, $depositTransaction ?? null);
            return response()->json(['error' => 'Transaction failed.'], 400);
        }
    }

    private function validateWithdrawal(User $authenticatedUser, User $targetUser, $sourceAccount, float $amount): array
    {
        if ($authenticatedUser->id !== $targetUser->id) {
            return [
                'success' => false,
                'message' => 'You can only withdraw from your own account.',
                'code' => 403
            ];
        }

        if ($sourceAccount->balance < $amount) {
            return [
                'success' => false,
                'message' => 'Insufficient balance for withdrawal.',
                'code' => 400
            ];
        }

        return ['success' => true];
    }

    private function processTransaction($sourceAccount, string $type, float $amount)
    {
        try {
            $transaction = $this->transactionRepo->createTransaction($sourceAccount, [
                'amount' => $amount,
                'type'   => $type,
            ]);

            if ($type === 'deposit') {
                $this->accountRepo->addBalance($sourceAccount, $amount);
            } else {
                $this->accountRepo->withdrawBalance($sourceAccount, $amount);
            }

            return response()->json([
                'message'     => 'Transaction successful.',
                'transaction' => $transaction,
                'balance'     => $sourceAccount->balance
            ]);
        } catch (\Exception $e) {
            if (isset($transaction)) {
                $transaction->delete();
            }
            return response()->json(['error' => 'Transaction failed.'], 400);
        }
    }

    private function createWithdrawalTransaction($account, float $amount, User $targetUser)
    {
        return $this->transactionRepo->createTransaction($account, [
            'amount' => $amount,
            'type'   => 'withdraw',
            'notes'  => "Transfer to user {$targetUser->id}"
        ]);
    }

    private function createDepositTransaction($account, float $amount, User $sourceUser)
    {
        return $this->transactionRepo->createTransaction($account, [
            'amount' => $amount,
            'type'   => 'deposit',
            'notes'  => "Transfer from user {$sourceUser->id}"
        ]);
    }

    private function revertTransactions($withdrawal = null, $deposit = null): void
    {
        if ($withdrawal) $withdrawal->delete();
        if ($deposit) $deposit->delete();
    }

    /**
     * Get the balance of the user's account via API.
     * Endpoint: GET /api/users/{userId}/balance
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBalanceApi($userId)
    {
        // auth()->user() is the one with the token
        $userFromToken = Auth::user($userId);

        // Verify that the userId in the route matches the token
        if ($userFromToken->id != $userId) {   
            return response()->json(['error' => 'You do not have permission to view this balance.'], 403);
        }

        // Now, find the account
        $account = $this->accountRepo->getUserAccount($userFromToken);
        if (!$account) {
            return response()->json(['error' => 'Account not found.'], 404);
        }

        return response()->json(['balance' => $account->balance]);
    }

    /**
     * Add balance to the user's account via API.
     * Endpoint: POST /api/users/{userId}/balance
     * JSON: { "amount": 50 }
     *
     * @param \Illuminate\Http\Request $request
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function addBalanceApi(Request $request, $userId)
    {
        $userFromToken = Auth::user($userId);

        // Verify that the userId in the route matches the token
        if ($userFromToken->id != $userId) {
            return response()->json(['error' => 'You do not have permission to add balance to this account.'], 403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $account = $this->accountRepo->getUserAccount($user);
        if (!$account) {
            return response()->json(['error' => 'Account not found.'], 404);
        }

        $this->accountRepo->addBalance($account, $request->input('amount'));

        return response()->json([
            'message' => 'Balance added successfully.',
            'balance' => $account->balance
        ]);
    }

    /**
     * Get a transaction by its number via API.
     * Endpoint: GET /api/transactions/{transactionNumber}
     *
     * @param string $transactionNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTransactionByNumberApi($transactionNumber)
    {
        $transaction = $this->transactionRepo->findByTransactionNumber($transactionNumber);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found.'], 404);
        }

        return response()->json([
            'transaction' => $transaction
        ]);
    }
}
