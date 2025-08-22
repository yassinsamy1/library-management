<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LibraryController;

Route::post('/books', [LibraryController::class, 'addBook']);
Route::get('/books', [LibraryController::class, 'getAllBooks']);
Route::get('/book/{id}/availability', [LibraryController::class, 'checkBookAvailability']);
Route::put('/book/{id}/status/{status}', [LibraryController::class, 'updateBookAvailability']);

Route::post('/members', [LibraryController::class, 'addMember']);
Route::get('/members', [LibraryController::class, 'getAllMembers']);
Route::put('/members/{id}', [LibraryController::class, 'updateMember']);
Route::delete('/members/{id}', [LibraryController::class, 'deleteMember']);
Route::get('/member/{id}/borrow-limit', [LibraryController::class, 'checkMemberBorrowLimit']);

Route::post('/borrow', [LibraryController::class, 'borrowBook']);
Route::post('/return', [LibraryController::class, 'returnBook']);
Route::get('/loans', [LibraryController::class, 'getAllLoans']);
Route::get('/loans/due-dates', [LibraryController::class, 'checkDueDates']);
Route::get('/check-overdue', [LibraryController::class, 'checkOverdueBooks']);
