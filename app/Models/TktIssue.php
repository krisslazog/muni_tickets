<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TktIssue extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected $table = 'tkt_issues';
}