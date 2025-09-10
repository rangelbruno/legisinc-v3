<?php

namespace Database\Factories;

use App\Models\{Proposicao, Parlamentar, TipoProposicao};
use Illuminate\Database\Eloquent\Factories\Factory;

class ProposicaoFactory extends Factory
{
    protected $model = Proposicao::class;

    public function definition(): array
    {
        return [
            'parlamentar_id' => Parlamentar::factory(),
            'tipo_proposicao_id' => 1, // Assumindo que tipo 1 existe
            'ementa' => $this->faker->sentence(10),
            'texto' => $this->faker->paragraphs(3, true),
            'justificativa' => $this->faker->paragraphs(2, true),
            'status' => 'rascunho',
            'assinado' => false,
            'urgente' => false,
            'valor' => $this->faker->numberBetween(1000, 50000),
            'tags' => ['teste', 'factory'],
            'ano_legislativo' => date('Y'),
        ];
    }
}