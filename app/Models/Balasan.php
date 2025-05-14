<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balasan extends Model
{
    protected $fillable = [
        'id_komentar',
        'id_user',
        'image',
        'content',
        'is_anonymous',
        'is_approved',
    ];

    public function komentar()
    {
        return $this->belongsTo(Komentar::class, 'id_komentar');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_approved' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->is_approved = true;
        });
    }

}
