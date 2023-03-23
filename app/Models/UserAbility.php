<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAbility extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
