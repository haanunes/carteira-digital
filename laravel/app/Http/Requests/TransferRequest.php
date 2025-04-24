<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *   schema="TransferRequest",
 *   type="object",
 *   required={"payee_id","amount"},
 *   @OA\Property(
 *       property="payee_id",
 *       type="integer",
 *       example=2,
 *       description="ID do usuário destinatário"
 *   ),
 *   @OA\Property(
 *       property="amount",
 *       type="number",
 *       format="float",
 *       example=150.00,
 *       description="Valor a ser transferido, maior que zero"
 *   )
 * )
 */
class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'payee_id' => [
                'required',
                'integer',
                'exists:users,id',
                "not_in:{$userId}",
            ],
            'amount' => [
                'required',
                'numeric',
                'gt:0',  
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'payee_id.required' => 'O destinatário é obrigatório.',
            'payee_id.integer'  => 'O destinatário deve ser um ID numérico.',
            'payee_id.exists'   => 'O usuário destinatário não existe.',
            'payee_id.not_in'   => 'Você não pode transferir para si mesmo.',

            'amount.required'   => 'O valor da transferência é obrigatório.',
            'amount.numeric'    => 'O valor da transferência deve ser numérico.',
            'amount.gt'         => 'O valor da transferência deve ser maior que zero.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Erro de validação',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
