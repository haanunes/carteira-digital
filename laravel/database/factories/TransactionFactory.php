<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'payer_id' => null, 
            'payee_id' => null,
            'amount'   => $this->faker->randomFloat(2, 1, 1000),
            'type'     => 'deposit',
            'status'   => 'completed',
        ];
    }
}