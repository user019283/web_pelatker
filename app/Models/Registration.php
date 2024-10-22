<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    // Tentukan tabel jika nama tabel tidak mengikuti konvensi Laravel
    protected $table = 'registrations';

    // Kolom yang boleh diisi (mass assignable)
    protected $fillable = [
        'user_id',
        'training_id',
        'status', // Contoh tambahan kolom, bisa disesuaikan dengan skema
    ];

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke model Training
    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
