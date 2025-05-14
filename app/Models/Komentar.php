<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    protected $fillable = [
        'id_aspirasi',
        'id_user',
        'image',
        'content',
        'is_anonymous',
        'is_approved',
    ];

    public function aspirasi()
    {
        return $this->belongsTo(Aspirasi::class, 'id_aspirasi');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function balasan()
    {
        return $this->hasMany(Balasan::class, 'id_komentar');
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
