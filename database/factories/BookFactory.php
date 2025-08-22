<?php

namespace Database\Factories;

use App\Models\Book;
use App\Enums\BookStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->randomElement([
                'Software Engineering',
                'Data Structure', 
                'Database',
                'Algorithm Analysis',
                'Computer Networks',
                'Operating Systems'
            ]),
            'author' => $this->faker->randomElement([
                'Dr. Ahmed Hassan',
                'Mohamed Ali',
                'Sarah Ahmed',
                'Omar Mahmoud',
                'Fatima Ibrahim'
            ]),
            'isbn' => $this->faker->unique()->isbn13(),
            'status' => $this->faker->randomElement(['available', 'borrowed']),
        ];
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
        ]);
    }

    public function borrowed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'borrowed',
        ]);
    }

    public function softwareEngineering(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Software Engineering',
            'author' => 'Dr. Ahmed Hassan',
            'isbn' => '978-0123456789',
            'status' => 'available',
        ]);
    }

    public function dataStructure(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Data Structure',
            'author' => 'Mohamed Ali',
            'isbn' => '978-0987654321',
            'status' => 'available',
        ]);
    }

    public function database(): static
    {
        return $this->state(fn (array $attributes) => [
            'title' => 'Database',
            'author' => 'Sarah Ahmed',
            'isbn' => '978-0555666777',
            'status' => 'available',
        ]);
    }
}
