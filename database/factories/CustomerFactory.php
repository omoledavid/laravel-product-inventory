<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'discount_percent' => null,
        ];
    }

    public function special(float $percent = 10): self
    {
        return $this->state(fn () => ['discount_percent' => $percent]);
    }
}
