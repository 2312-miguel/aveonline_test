<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the users, paginated and ordered by creation date descending.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Paginate users, ordered by created_at desc
        $users = User::orderBy('created_at', 'desc')->paginate(10);

        // Return to the view user.index
        return view('users.index', compact('users'));
    }

    /**
     * Get user details and their transactions via API.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserDetailsApi($userId) 
    {
        // 1. Validate the token and check if the user matches.
        // $currentUser = Auth::user();
        // if ($currentUser->id != $userId) {
        //     return response()->json(['error' => 'You do not have permission.'], 403);
        // }

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'message' => 'Target user not found.',
            ], 404);
        }

        // 2. Load the user and their transactions
        $user = User::with(['accounts.transactions'])->findOrFail($userId);

        // If each user has 1 account, use with('account.transactions')

        // 3. Return data
        return response()->json([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email
            ],
            'transactions' => $user->accounts->flatMap->transactions
        ]);
    }
}
