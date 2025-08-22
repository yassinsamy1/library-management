<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    public function getName()
    {
        return $this->attributes['name'];
    }

    public function setName($value)
    {
        $this->attributes['name'] = $value;
    }

    public function getEmail()
    {
        return $this->attributes['email'];
    }

    public function setEmail($value)
    {
        $this->attributes['email'] = $value;
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
