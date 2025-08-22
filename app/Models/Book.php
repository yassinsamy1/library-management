<?php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Enums\BookStatus;

class Book extends Model
{
    public function getTitle()
    {
        return $this->attributes['title'];
    }

    public function setTitle($value)
    {
        $this->attributes['title'] = $value;
    }

    public function getAuthor()
    {
        return $this->attributes['author'];
    }

    public function setAuthor($value)
    {
        $this->attributes['author'] = $value;
    }

    public function getIsbn()
    {
        return $this->attributes['isbn'];
    }

    public function setIsbn($value)
    {
        $this->attributes['isbn'] = $value;
    }

    public function getStatusAttribute($value)
    {
        return BookStatus::from($value);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value instanceof BookStatus ? $value->value : $value;
    }

    public function currentLoan()
    {
        return $this->hasOne(Loan::class)->whereNull('returned_at');
    }
}
