<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * Executa um depósito na carteira do usuário.
     *
     * @param User  $user
     * @param float $amount
     * @return Transaction
     */
    public function deposit(User $user, float $amount): Transaction
    {
        return DB::transaction(function() use ($user, $amount) {
            $tx = Transaction::create([
                'payer_id' => null,
                'payee_id' => $user->id,
                'amount'   => $amount,
                'type'     => 'deposit',
                'status'   => 'completed',
            ]);

            $user->wallet()->increment('balance', $amount);

            return $tx;
        });
    }

    /**
     * Executa uma transferência entre dois usuários.
     *
     * @param User  $payer
     * @param User  $payee
     * @param float $amount
     * @return Transaction
     */
    public function transfer(User $payer, User $payee, float $amount): Transaction
    {
        return DB::transaction(function() use ($payer, $payee, $amount) {
            $tx = Transaction::create([
                'payer_id' => $payer->id,
                'payee_id' => $payee->id,
                'amount'   => $amount,
                'type'     => 'transfer',
                'status'   => 'completed',
            ]);

            $payer->wallet()->decrement('balance', $amount);
            $payee->wallet()->increment('balance', $amount);

            return $tx;
        });
    }

    /**
     * Reverte uma transação já existente.
     *
     * @param Transaction $transaction
     * @return Transaction
     */
    public function reverse(Transaction $transaction): Transaction
    {
        return DB::transaction(function() use ($transaction) {
            $transaction->update(['status' => 'reversed']);

            $reverseTx = Transaction::create([
                'payer_id' => $transaction->payee_id,
                'payee_id' => $transaction->payer_id,
                'amount'   => $transaction->amount,
                'type'     => 'reversal',
                'status'   => 'completed',
            ]);

            if ($transaction->type === 'deposit') {
                Wallet::where('user_id', $transaction->payee_id)
                      ->decrement('balance', $transaction->amount);
            } else {
                Wallet::where('user_id', $transaction->payee_id)
                      ->decrement('balance', $transaction->amount);
                Wallet::where('user_id', $transaction->payer_id)
                      ->increment('balance', $transaction->amount);
            }

            return $reverseTx;
        });
    }

    /**
     * Retorna o histórico de transações de um usuário.
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function history(User $user)
    {
        return Transaction::where('payer_id', $user->id)
            ->orWhere('payee_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
