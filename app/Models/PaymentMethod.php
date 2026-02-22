<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PaymentMethod extends Model
{
    protected $fillable = ['user_id', 'type', 'encrypted_details'];

    protected $casts = [
        'details' => 'array',
    ];

    public function setDetailsAttribute($value)
    {
        $this->attributes['encrypted_details'] = Crypt::encryptString(json_encode($value));
    }

    public function getDetailsAttribute()
    {
        return json_decode(Crypt::decryptString($this->attributes['encrypted_details']), true);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}