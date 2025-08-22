<?php

class SimpleLibraryManager
{
    private $books = [];
    private $members = [];
    private $loans = [];

    public function addBook($id, $title, $author, $isbn)
    {
        $this->books[$id] = [
            'id' => $id,
            'title' => $title,
            'author' => $author,
            'isbn' => $isbn,
            'status' => 'available'
        ];
        return "Book added: {$title}";
    }

    public function addMember($id, $name, $email)
    {
        $this->members[$id] = [
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'loans' => []
        ];
        return "Member added: {$name}";
    }

    public function borrowBook($memberId, $bookId)
    {
        if (!isset($this->books[$bookId])) {
            return "Book not found";
        }

        if (!isset($this->members[$memberId])) {
            return "Member not found";
        }

        if ($this->books[$bookId]['status'] !== 'available') {
            return "Book is not available";
        }

        if (count($this->members[$memberId]['loans']) >= 5) {
            return "Member has reached the borrow limit of 5 books";
        }

        $loanId = uniqid();
        $borrowDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime('+14 days'));

        $this->loans[$loanId] = [
            'id' => $loanId,
            'member_id' => $memberId,
            'book_id' => $bookId,
            'borrowed_at' => $borrowDate,
            'due_at' => $dueDate,
            'returned_at' => null
        ];

        $this->books[$bookId]['status'] = 'borrowed';
        $this->members[$memberId]['loans'][] = $loanId;

        return "Book borrowed successfully. Due date: {$dueDate}";
    }

    public function returnBook($memberId, $bookId)
    {
        $loan = null;
        foreach ($this->loans as $loanId => $loanData) {
            if ($loanData['member_id'] == $memberId && 
                $loanData['book_id'] == $bookId && 
                $loanData['returned_at'] === null) {
                $loan = $loanData;
                break;
            }
        }

        if (!$loan) {
            return "No active loan found for this member and book";
        }

        $this->loans[$loan['id']]['returned_at'] = date('Y-m-d');
        $this->books[$bookId]['status'] = 'available';

        $memberLoans = array_filter($this->members[$memberId]['loans'], 
            function($id) use ($loan) { return $id !== $loan['id']; });
        $this->members[$memberId]['loans'] = array_values($memberLoans);

        return "Book returned successfully";
    }

    public function checkOverdueBooks()
    {
        $today = date('Y-m-d');
        $overdue = [];
        $dueSoon = [];

        foreach ($this->loans as $loan) {
            if ($loan['returned_at'] === null) {
                if ($loan['due_at'] < $today) {
                    $overdue[] = [
                        'member' => $this->members[$loan['member_id']]['name'],
                        'book' => $this->books[$loan['book_id']]['title'],
                        'due_date' => date('d/m/Y', strtotime($loan['due_at'])),
                        'status' => 'OVERDUE'
                    ];
                } elseif ($loan['due_at'] <= date('Y-m-d', strtotime('+2 days'))) {
                    $dueSoon[] = [
                        'member' => $this->members[$loan['member_id']]['name'],
                        'book' => $this->books[$loan['book_id']]['title'],
                        'due_date' => date('d/m/Y', strtotime($loan['due_at'])),
                        'status' => 'DUE SOON'
                    ];
                }
            }
        }

        return [
            'overdue' => $overdue,
            'due_soon' => $dueSoon
        ];
    }

    public function getMemberBorrowLimit($memberId)
    {
        if (!isset($this->members[$memberId])) {
            return "Member not found";
        }

        $currentLoans = count($this->members[$memberId]['loans']);
        return [
            'can_borrow' => $currentLoans < 5,
            'current_loans' => $currentLoans,
            'remaining_slots' => 5 - $currentLoans
        ];
    }

    public function getBookAvailability($bookId)
    {
        if (!isset($this->books[$bookId])) {
            return "Book not found";
        }

        return [
            'is_available' => $this->books[$bookId]['status'] === 'available',
            'status' => $this->books[$bookId]['status'],
            'book_title' => $this->books[$bookId]['title']
        ];
    }
}

$library = new SimpleLibraryManager();

echo $library->addBook(1, "Laravel Guide", "John Smith", "978-1234567890") . "\n";
echo $library->addBook(2, "PHP Basics", "Jane Doe", "978-0987654321") . "\n";
echo $library->addMember(1, "Alice Johnson", "alice@email.com") . "\n";
echo $library->addMember(2, "Bob Wilson", "bob@email.com") . "\n";

echo $library->borrowBook(1, 1) . "\n";
echo $library->borrowBook(1, 2) . "\n";

print_r($library->getMemberBorrowLimit(1));

print_r($library->getBookAvailability(1));

print_r($library->checkOverdueBooks());

echo $library->returnBook(1, 1) . "\n";

echo "Library Management System Logic Demo Complete!\n";
