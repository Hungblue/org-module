<?php

namespace KeyHoang\OrgModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departments';
    protected $fillable
                     = [
            'name',
            'code',
            'level',
            'department_head_id',
            'assistant_id',
            'parent_id',
            'is_unit',
            'unit_id',
            'is_department',
            'department_id',
            'is_group',
            'abbreviated_name',
            'uuid'
        ];
}
