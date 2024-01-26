<?php

namespace KeyHoang\OrgModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class UserNoSQL extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'users';
    protected $primaryKey = '_id';
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


