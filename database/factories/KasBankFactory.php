<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class KasBankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        
        return [
            'nama' => $this->faker->name(),
            'no_akun' => $this->faker->randomNumber(4),
            'tipe' => $this->faker->randomElement(['Kas', 'Bank']),
            'saldo_awal' => $this->faker->numberBetween(100, 10000000),
            'bank' => $this->faker->randomElement(['BNI', 'Mandiri', 'BCA', 'BSI']),
            'no_rek' => $this->faker->numberBetween(100, 1000000),
            'rek_nama' => $this->faker->name(),
            'cabang' => $this->faker->randomElement(['Surabaya', 'Jakarta', 'Medan', 'Sidoarjo']),
            'is_aktif' => 'Y',
        ];
    }

    
}
