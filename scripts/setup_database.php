<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/../database/library.sqlite',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$dbPath = __DIR__ . '/../database/library.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
}

echo "Setting up Library Management Database...\n";

Capsule::schema()->dropIfExists('books');
Capsule::schema()->create('books', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('author');
    $table->string('isbn')->unique();
    $table->enum('status', ['available', 'borrowed'])->default('available');
    $table->timestamps();
});

Capsule::schema()->dropIfExists('members');
Capsule::schema()->create('members', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamps();
});

Capsule::schema()->dropIfExists('loans');
Capsule::schema()->create('loans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
    $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
    $table->timestamp('borrowed_at');
    $table->timestamp('due_at');
    $table->timestamp('returned_at')->nullable();
    $table->timestamps();
});

echo "✅ Database tables created successfully!\n";

use Carbon\Carbon;

Capsule::table('books')->insert([
    [
        'title' => 'Software Engineering',
        'author' => 'Dr. Ahmed Hassan',
        'isbn' => '978-0123456789',
        'status' => 'available',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'title' => 'Data Structure',
        'author' => 'Mohamed Ali',
        'isbn' => '978-0987654321',
        'status' => 'borrowed',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'title' => 'Database',
        'author' => 'Sarah Ahmed',
        'isbn' => '978-0555666777',
        'status' => 'available',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
]);

Capsule::table('members')->insert([
    [
        'name' => 'Ahmed Mohamed',
        'email' => 'ahmed.mohamed@university.edu',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'Fatima Hassan',
        'email' => 'fatima.hassan@university.edu',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'name' => 'Omar Ali',
        'email' => 'omar.ali@university.edu',
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
]);

Capsule::table('loans')->insert([
    [
        'member_id' => 2,
        'book_id' => 2,
        'borrowed_at' => Carbon::now()->subDays(20),
        'due_at' => Carbon::now()->subDays(6),
        'returned_at' => null,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
    [
        'member_id' => 3,
        'book_id' => 1,
        'borrowed_at' => Carbon::now()->subDays(30),
        'due_at' => Carbon::now()->subDays(16),
        'returned_at' => Carbon::now()->subDays(14),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ],
]);

echo "✅ Sample data inserted successfully!\n";
echo "🎯 Database setup complete!\n";
echo "📍 Database location: " . $dbPath . "\n";
echo "📊 Ready to use with your Laravel Library Management System!\n";
