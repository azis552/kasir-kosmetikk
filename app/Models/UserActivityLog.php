<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'action', 'details'];

    public function userDetail()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}