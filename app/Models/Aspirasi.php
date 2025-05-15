<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspirasi extends Model
{
    protected $fillable = [
        'id_user',
        'category',
        'image',
        'content',
        'is_anonymous',
        'is_approved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function komentar()
    {
        return $this->hasMany(Komentar::class, 'id_aspirasi');
    }


    protected $casts = [
        'is_anonymous' => 'boolean',
        'is_approved' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->is_approved = false;
        });
    }

}
