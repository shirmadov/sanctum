<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use MohamedGaber\SanctumRefreshToken\Traits\HasApiTokens;

class MobileUser extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'id','phone','verify_code',
    ];

    protected $dates = [
        'deleted_at',
    ];
}
