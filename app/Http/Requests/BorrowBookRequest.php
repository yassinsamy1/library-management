<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BorrowBookRequest extends FormRequest
{
	public function rules()
	{
		return [
			'member_id' => [
				'required',
				'exists:members,id',
				function ($attribute, $value, $fail) {
					$member = \App\Models\Member::find($value);
					if ($member && $member->loans()->whereNull('returned_at')->count() >= 5) {
						$fail("The {$attribute} has reached the borrow limit of 5 books.");
					}
				}
			],
			'book_id' => [
				'required',
				'exists:books,id',
				function ($attribute, $value, $fail) {
					$book = \App\Models\Book::find($value);
					if ($book && $book->status->value !== 'available') {
						$fail("The {$attribute} is not available for borrowing.");
					}
				}
			],
		];
	}
}