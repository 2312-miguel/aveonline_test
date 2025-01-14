<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    /**
     * Shows a summary of a user's balance:
     * - total deposits
     * - total withdrawals
     * - current balance
     */
    public function getBalanceSummary($userId)
    {
        // 1. Get the user with token
        //$currentUser = Auth::user();

        // 2. Verify permissions (must be the same user or an admin)
        // if ($currentUser->id != $userId /* && !$currentUser->isAdmin() */) {
            // return response()->json(['error' => 'You do not have permission.'], 403);
        // }

        // 3. Find the user in the database
        $user = User::findOrFail($userId);

        // 4. Get the user's account(s).
        //    If the user only has 1 account, suppose:
        $account = $user->accounts->first();
        // Or if your relationship is called 'account' and is hasOne, use $user->account

        if (!$account) {
            return response()->json(['error' => 'This user does not have an account.'], 404);
        }

        // 5. Calculate totals
        $totalDeposits = $account->transactions()
            ->where('type', 'deposit')
            ->sum('amount');

        $totalWithdraw = $account->transactions()
            ->where('type', 'withdraw')
            ->sum('amount');

        // Current balance => if you handle it in the 'balance' column
        $currentBalance = $account->balance;

        // 6. Return the data in JSON
        return response()->json([
            'user_id'         => $user->id,
            'total_deposits'  => $totalDeposits,
            'total_withdraw'  => $totalWithdraw,
            'current_balance' => $currentBalance,
            'message'         => 'Balance summary'
        ], 200);
    }
}
