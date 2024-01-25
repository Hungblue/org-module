<?php

namespace src\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';
    protected $fillable
                          = [
            'sso_id',
            'username',
            'full_name',
            'email',
            'user_status',
            'phone_number',
            'staff_code',
            'position',
            'unit',
            'department'
        ];
}


