<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model {
    use HasFactory;
    protected $guarded = [];
    protected $dates   = ['validity_from', 'validity_to'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function subscriptionReminder() {
        return $this->hasOne(SubscriptionReminder::class);
    }

    public function package() {
        return $this->belongsTo(Package::class);
    }
}
