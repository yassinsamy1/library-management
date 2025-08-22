<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Member;
use App\Models\Loan;
use Carbon\Carbon;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $book1 = Book::factory()->softwareEngineering()->create();
        $book2 = Book::factory()->dataStructure()->borrowed()->create();
        $book3 = Book::factory()->database()->create();

        $member1 = Member::factory()->ahmed()->create();
        $member2 = Member::factory()->fatima()->create();
        $member3 = Member::factory()->omar()->create();

        $loan1 = Loan::factory()
            ->forMemberAndBook($member1, $book2)
            ->active()
            ->create([
                'borrowed_at' => Carbon::now()->subDays(5),
                'due_at' => Carbon::now()->subDays(5)->addDays(14),
            ]);

        $loan2 = Loan::factory()
            ->forMemberAndBook($member2, $book3)
            ->overdue()
            ->create();
        
        $book3->status = 'borrowed';
        $book3->save();

        $loan3 = Loan::factory()
            ->forMemberAndBook($member3, $book1)
            ->returned()
            ->create([
                'borrowed_at' => Carbon::now()->subDays(30),
                'due_at' => Carbon::now()->subDays(30)->addDays(14),
                'returned_at' => Carbon::now()->subDays(14),
            ]);
    }
}
