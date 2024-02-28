<?php

namespace KeyHoang\OrgModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class UserStatusNoSQL extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'user_status';
    protected $primaryKey = '_id';
    protected $fillable   = ['*'];
}


