<?php

namespace KeyHoang\OrgModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class UserNoSQL extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'users';
    protected $primaryKey = '_id';
    protected $fillable   = ['*'];
}


