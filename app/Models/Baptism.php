<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Baptism extends Model
{
    use HasFactory;

    protected $table = 'baptisms';

    protected $fillable = [
        'category',
        'book_number',
        'page_number',
        'line_number',
        'candidate_name',
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'father_name',
        'mother_name',
        'mother_maiden_name',
        'birth_date',
        'birth_place',
        'father_birthplace',
        'mother_birthplace',
        'baptism_date',
        'minister_name',
        'godfather',
        'godmother',
        'legitimacy',
        'residence',
        'remarks',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'baptism_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}