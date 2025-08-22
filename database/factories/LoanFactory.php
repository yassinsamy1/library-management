<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\Book;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        $borrowedAt = $this->faker->dateTimeBetween('-30 days', 'now');
        $borrowedAtCarbon = Carbon::instance($borrowedAt);
        
        return [
            'member_id' => Member::factory(),
            'book_id' => Book::factory(),
            'borrowed_at' => $borrowedAtCarbon,
            'due_at' => $borrowedAtCarbon->copy()->addDays(14),
            'returned_at' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'returned_at' => null,
        ]);
    }

    public function returned(): static
    {
        return $this->state(function (array $attributes) {
            $borrowedAt = Carbon::parse($attributes['borrowed_at']);
            $dueAt = Carbon::parse($attributes['due_at']);
            
            $returnedAt = $this->faker->dateTimeBetween($borrowedAt, $dueAt);
            
            return [
                'returned_at' => $returnedAt,
            ];
        });
    }

    public function overdue(): static
    {
        return $this->state(function (array $attributes) {
            $borrowedAt = $this->faker->dateTimeBetween('-25 days', '-15 days');
            $borrowedAtCarbon = Carbon::instance($borrowedAt);
            
            return [
                'borrowed_at' => $borrowedAtCarbon,
                'due_at' => $borrowedAtCarbon->copy()->addDays(14),
                'returned_at' => null,
            ];
        });
    }

    public function dueSoon(): static
    {
        return $this->state(function (array $attributes) {
            $borrowedAt = $this->faker->dateTimeBetween('-12 days', '-10 days');
            $borrowedAtCarbon = Carbon::instance($borrowedAt);
            
            return [
                'borrowed_at' => $borrowedAtCarbon,
                'due_at' => $borrowedAtCarbon->copy()->addDays(14),
                'returned_at' => null,
            ];
        });
    }

    public function forMemberAndBook(Member $member, Book $book): static
    {
        return $this->state(fn (array $attributes) => [
            'member_id' => $member->id,
            'book_id' => $book->id,
        ]);
    }
}
