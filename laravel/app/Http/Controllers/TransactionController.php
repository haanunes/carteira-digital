<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use App\Models\Transaction;
use App\Models\User;
use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Transações",
 *     description="Operações de depósito, transferência, histórico e reversão"
 * )
 */
class TransactionController extends Controller
{
    public function __construct(private TransactionService $service)
    {
       
    }

    /**
     * Executa um depósito.
     *
     * @OA\Post(
     *     path="/api/deposit",
     *     tags={"Transações"},
     *     summary="Depósito na carteira",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/DepositRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Depósito realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Depósito realizado com sucesso"),
     *             @OA\Property(property="transactionId", type="integer", example=10),
     *             @OA\Property(property="transaction", ref="#/components/schemas/TransactionResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação ou saldo inválido"
     *     )
     * )
     */
    public function deposit(DepositRequest $request)
    {
        $user   = $request->user();
        $amount = $request->validated()['amount'];

        $tx = $this->service->deposit($user, $amount);

        return response()->json([
            'message'       => 'Depósito realizado com sucesso',
            'transactionId' => $tx->id,
            'transaction'   => $tx,
        ], 201);
    }

    /**
     * Executa uma transferência.
     *
     * @OA\Post(
     *     path="/api/transfer",
     *     tags={"Transações"},
     *     summary="Transferência de saldo",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TransferRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transferência realizada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Transferência realizada com sucesso"),
     *             @OA\Property(property="transactionId", type="integer", example=11),
     *             @OA\Property(property="transaction", ref="#/components/schemas/TransactionResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Saldo insuficiente ou erro de validação"
     *     )
     * )
     */
    public function transfer(TransferRequest $request)
    {
        $payer  = $request->user();
        $payee  = User::findOrFail($request->validated()['payee_id']);
        $amount = $request->validated()['amount'];

        if ($payer->wallet->balance < $amount) {
            return response()->json(['message' => 'Saldo insuficiente'], 422);
        }

        $tx = $this->service->transfer($payer, $payee, $amount);

        return response()->json([
            'message'       => 'Transferência realizada com sucesso',
            'transactionId' => $tx->id,
            'transaction'   => $tx,
        ], 201);
    }

    /**
     * Lista o histórico de transações.
     *
     * @OA\Get(
     *     path="/api/transactions",
     *     tags={"Transações"},
     *     summary="Histórico de transações",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de transações",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/TransactionResource")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $txs = $this->service->history($user);

        return TransactionResource::collection($txs);
    }

    /**
     * Reverte uma transação via route model binding.
     *
     * @OA\Post(
     *     path="/api/reverse/{transaction}",
     *     tags={"Transações"},
     *     summary="Reversão de transação",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="transaction",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID da transação a ser revertida"
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Transação revertida com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Transação revertida com sucesso"),
     *             @OA\Property(property="transactionId", type="integer", example=12),
     *             @OA\Property(property="reversal", ref="#/components/schemas/TransactionResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Operação não permitida"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Transação já revertida ou erro de validação"
     *     )
     * )
     */
    public function reverse(Request $request, Transaction $transaction)
    {
        $userId = $request->user()->id;

        if ($transaction->payer_id !== $userId && $transaction->payee_id !== $userId) {
            return response()->json(['message' => 'Operação não permitida'], 403);
        }

        if ($transaction->status === 'reversed') {
            return response()->json(['message' => 'Transação já foi revertida'], 422);
        }

        $reverseTx = $this->service->reverse($transaction);

        return response()->json([
            'message'       => 'Transação revertida com sucesso',
            'transactionId' => $reverseTx->id,
            'reversal'      => $reverseTx,
        ], 201);
    }
}