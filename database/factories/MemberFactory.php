<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Ahmed Mohamed',
                'Fatima Hassan',
                'Omar Ali',
                'Sarah Ibrahim',
                'Mohamed Mahmoud',
                'Nour Ahmed',
                'Yassin Othman',
                'Samy Hassan'
            ]),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }

    public function ahmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Ahmed Mohamed',
            'email' => 'ahmed.mohamed@university.edu',
        ]);
    }

    public function fatima(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Fatima Hassan',
            'email' => 'fatima.hassan@university.edu',
        ]);
    }

    public function omar(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Omar Ali',
            'email' => 'omar.ali@university.edu',
        ]);
    }

    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => $this->faker->unique()->userName() . '@university.edu',
        ]);
    }
}
