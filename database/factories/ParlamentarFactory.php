<?php

namespace Database\Factories;

use App\Models\{Parlamentar, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class ParlamentarFactory extends Factory
{
    protected $model = Parlamentar::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->name(),
            'nome_politico' => $this->faker->firstName(),
            'partido' => $this->faker->randomElement(['PT', 'PSDB', 'MDB', 'PP', 'PDT']),
            'cargo' => 'Vereador',
            'status' => 'ativo',
            'telefone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'profissao' => $this->faker->jobTitle(),
            'user_id' => User::factory(),
        ];
    }
}