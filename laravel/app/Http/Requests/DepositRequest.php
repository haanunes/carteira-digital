<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
/**
 * @OA\Schema(
 *   schema="DepositRequest",
 *   type="object",
 *   required={"amount"},
 *   @OA\Property(
 *       property="amount",
 *       type="number",
 *       format="float",
 *       example=100.00,
 *       description="Valor a ser depositado, maior que zero"
 *   )
 * )
 */
class DepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:0.01',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'O valor do depósito é obrigatório.',
            'amount.numeric'  => 'O valor do depósito deve ser numérico.',
            'amount.min'      => 'O valor do depósito deve ser no mínimo 0,01.',
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
