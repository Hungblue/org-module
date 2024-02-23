<?php

namespace KeyHoang\OrgModule\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
            'avatar',
            'unit',
            'unit_code',
            'department',
            'department_code'
        ];
}
