<?php
namespace App\Http\Controllers;

use App\Http\Requests\BorrowBookRequest;
use App\Models\Book;
use App\Models\Member;
use App\Models\Loan;
use App\Services\LibraryService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LibraryController extends Controller
{
    protected $libraryService;

    public function __construct(LibraryService $libraryService)
    {
        $this->libraryService = $libraryService;
    }

    public function addBook(Request $request)
    {
        $book = new Book();
        $book->setTitle($request->input('title'));
        $book->setAuthor($request->input('author'));
        $book->setIsbn($request->input('isbn'));
        $book->status = 'available';
        $book->save();

        return response()->json([
            'message' => "Book added: " . $book->getTitle(),
            'book' => [
                'id' => $book->id,
                'title' => $book->getTitle(),
                'author' => $book->getAuthor(),
                'isbn' => $book->getIsbn(),
                'status' => $book->status
            ]
        ]);
    }

    public function addMember(Request $request)
    {
        $member = new Member();
        $member->setName($request->input('name'));
        $member->setEmail($request->input('email'));
        $member->save();

        return response()->json([
            'message' => "Member added: " . $member->getName(),
            'member' => [
                'id' => $member->id,
                'name' => $member->getName(),
                'email' => $member->getEmail()
            ]
        ]);
    }

    public function updateMember(Request $request, $memberId)
    {
        $member = Member::find($memberId);
        
        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }

        if ($request->has('name')) {
            $member->setName($request->input('name'));
        }
        
        if ($request->has('email')) {
            $member->setEmail($request->input('email'));
        }

        $member->save();

        return response()->json([
            'message' => "Member updated successfully",
            'member' => [
                'id' => $member->id,
                'name' => $member->getName(),
                'email' => $member->getEmail()
            ]
        ]);
    }

    public function deleteMember($memberId)
    {
        $member = Member::find($memberId);
        
        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }

        $activeLoans = $member->loans()->whereNull('returned_at')->count();
        
        if ($activeLoans > 0) {
            return response()->json([
                'error' => 'Cannot delete member with active loans',
                'active_loans' => $activeLoans
            ], 400);
        }

        $memberName = $member->getName();
        $member->delete();

        return response()->json([
            'message' => "Member '{$memberName}' deleted successfully"
        ]);
    }

    public function borrowBook(BorrowBookRequest $request)
    {
        $member = Member::find($request->input('member_id'));
        $book = Book::find($request->input('book_id'));
        
        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }
        
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $result = $this->libraryService->borrowBook($member, $book);
        
        return response()->json(['message' => 'Book borrowed successfully.', 'loan' => $result]);
    }

    public function returnBook(Request $request)
    {
        $member = Member::find($request->input('member_id'));
        $book = Book::find($request->input('book_id'));
        
        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }
        
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $this->libraryService->returnBook($member, $book);
        
        return response()->json(['message' => 'Book returned successfully.']);
    }

    public function checkMemberBorrowLimit($memberId)
    {
        $member = Member::find($memberId);
        
        if (!$member) {
            return response()->json(['error' => 'Member not found'], 404);
        }

        $currentLoans = $member->loans()->whereNull('returned_at')->count();
        $canBorrow = $currentLoans < 5;
        
        return response()->json([
            'can_borrow' => $canBorrow,
            'current_loans' => $currentLoans,
            'remaining_slots' => 5 - $currentLoans,
            'member_name' => $member->getName()
        ]);
    }

    public function checkBookAvailability($bookId)
    {
        $book = Book::find($bookId);
        
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $isAvailable = $book->status === 'available';
        
        return response()->json([
            'is_available' => $isAvailable,
            'status' => $book->status,
            'book_title' => $book->getTitle(),
            'author' => $book->getAuthor()
        ]);
    }

    public function updateBookAvailability($bookId, $status)
    {
        $book = Book::find($bookId);
        
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        $book->status = $status;
        $book->save();
        
        return response()->json([
            'message' => 'Book availability updated',
            'book_id' => $bookId,
            'new_status' => $status,
            'book_title' => $book->getTitle()
        ]);
    }

    public function checkDueDates()
    {
        $overdueLoans = Loan::whereNull('returned_at')
            ->where('due_at', '<', now())
            ->with(['member', 'book'])
            ->get();

        $upcomingDue = Loan::whereNull('returned_at')
            ->whereBetween('due_at', [now(), now()->addDays(3)])
            ->with(['member', 'book'])
            ->get();

        return response()->json([
            'overdue_loans' => $overdueLoans->map(function($loan) {
                return [
                    'loan_id' => $loan->id,
                    'member_name' => $loan->member->getName(),
                    'book_title' => $loan->book->getTitle(),
                    'due_date' => $loan->due_at->format('d/m/Y'),
                    'days_overdue' => now()->diffInDays($loan->due_at)
                ];
            }),
            'upcoming_due' => $upcomingDue->map(function($loan) {
                return [
                    'loan_id' => $loan->id,
                    'member_name' => $loan->member->getName(),
                    'book_title' => $loan->book->getTitle(),
                    'due_date' => $loan->due_at->format('d/m/Y'),
                    'days_remaining' => $loan->due_at->diffInDays(now())
                ];
            })
        ]);
    }

    public function checkOverdueBooks()
    {
        $overdueBooks = Loan::whereNull('returned_at')
            ->where('due_at', '<', now())
            ->with(['member', 'book'])
            ->get();

        $dueSoonBooks = Loan::whereNull('returned_at')
            ->whereBetween('due_at', [now(), now()->addDays(2)])
            ->with(['member', 'book'])
            ->get();

        return response()->json([
            'overdue' => $overdueBooks->map(function($loan) {
                return [
                    'loan_id' => $loan->id,
                    'member' => $loan->member->getName(),
                    'book' => $loan->book->getTitle(),
                    'due_date' => $loan->due_at->format('d/m/Y'),
                    'status' => 'OVERDUE',
                    'days_overdue' => now()->diffInDays($loan->due_at)
                ];
            }),
            'due_soon' => $dueSoonBooks->map(function($loan) {
                return [
                    'loan_id' => $loan->id,
                    'member' => $loan->member->getName(),
                    'book' => $loan->book->getTitle(),
                    'due_date' => $loan->due_at->format('d/m/Y'),
                    'status' => 'DUE SOON',
                    'days_remaining' => $loan->due_at->diffInDays(now())
                ];
            })
        ]);
    }

    public function getAllBooks()
    {
        $books = Book::all();
        
        return response()->json([
            'books' => $books->map(function($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->getTitle(),
                    'author' => $book->getAuthor(),
                    'isbn' => $book->getIsbn(),
                    'status' => $book->status
                ];
            })
        ]);
    }

    public function getAllMembers()
    {
        $members = Member::with('loans')->get();
        
        return response()->json([
            'members' => $members->map(function($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->getName(),
                    'email' => $member->getEmail(),
                    'active_loans' => $member->loans()->whereNull('returned_at')->count()
                ];
            })
        ]);
    }

    public function getAllLoans()
    {
        $loans = Loan::with(['member', 'book'])->get();
        
        return response()->json([
            'loans' => $loans->map(function($loan) {
                return [
                    'id' => $loan->id,
                    'member' => $loan->member->getName(),
                    'book' => $loan->book->getTitle(),
                    'borrowed_at' => $loan->borrowed_at->format('d/m/Y'),
                    'due_at' => $loan->due_at->format('d/m/Y'),
                    'returned_at' => $loan->returned_at ? $loan->returned_at->format('d/m/Y') : null,
                    'status' => $loan->returned_at ? 'RETURNED' : ($loan->due_at < now() ? 'OVERDUE' : 'ACTIVE')
                ];
            })
        ]);
    }
}
