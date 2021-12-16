<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Blog extends Model
{
    use HasFactory;
    use HasApiTokens;

    public $fillable = [
        'user_id',
        'title',
        'description'
    ];
}
