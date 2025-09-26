<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificaton extends Model
{
    use HasFactory;

    // Custom table name
    protected $table = 'notificaton';

    // Allow mass assignment for text
    protected $fillable = ['text','is_seen'];
}
