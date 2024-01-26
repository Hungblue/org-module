<?php

namespace KeyHoang\OrgModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;

class DepartmentNoSQL extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'departments';
    protected $primaryKey = '_id';
    protected $fillable   = ['*'];
}


