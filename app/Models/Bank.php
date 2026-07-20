<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Bank extends Model
{
    use HasFactory;

    // Nonaktifkan auto increment karena pakai UUID
    public $incrementing = false;

    // Tipe primary key UUID
    protected $keyType = 'string';

    // Kalau kamu mau isi kolom lewat mass assignment
    protected $fillable = [
        'id',
        'name',
        'code',
    ];

    // Generate UUID otomatis setiap kali membuat record baru
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}