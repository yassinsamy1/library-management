<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    public function getBorrowedAt()
    {
        return $this->attributes['borrowed_at'];
    }

    public function setBorrowedAt($value)
    {
        $this->attributes['borrowed_at'] = $value;
    }

    public function getDueAt()
    {
        return $this->attributes['due_at'];
    }

    public function setDueAt($value)
    {
        $this->attributes['due_at'] = $value;
    }

    public function getReturnedAt()
    {
        return $this->attributes['returned_at'];
    }

    public function setReturnedAt($value)
    {
        $this->attributes['returned_at'] = $value;
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
