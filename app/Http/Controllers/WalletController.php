<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string',
            'type' => 'required|string',
        ]);

        $wallet = Wallet::create([
            'user_id' => $validatedData['user_id'],
            'name' => $validatedData['name'],
            'type' => $validatedData['type'],
            'balance' => 0.00,
        ]);

        return response()->json(['message' => 'Wallet created successfully', 'wallet' => $wallet], 201);
    }

    public function getDetails($walletId)
    {
        $wallet = Wallet::findOrFail($walletId);
        return response()->json(['name' => $wallet->name, 'type' => $wallet->type, 'balance' => $wallet->balance]);
    }

    public function getAllWallets()
    {
        $wallets = Wallet::all(['name', 'type', 'balance']);
        return response()->json($wallets);
    }
    public function sendMoney(Request $request)
    {
        $validatedData = $request->validate([
            'sender_id' => 'required|exists:wallets,id',
            'recipient_id' => 'required|exists:wallets,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $senderWallet = Wallet::findOrFail($validatedData['sender_id']);
        $recipientWallet = Wallet::findOrFail($validatedData['recipient_id']);

        if ($senderWallet->balance < $validatedData['amount']) {
            return response()->json(['message' => 'Insufficient funds'], 400);
        }

        $senderWallet->decrement('balance', $validatedData['amount']);
        $recipientWallet->increment('balance', $validatedData['amount']);

        return response()->json(['message' => 'Money sent successfully'], 200);
    }
}

