<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model {
    use HasFactory;
    protected $guarded = [];

    public function packageFeatures() {
        return $this->belongsToMany(PackageFeature::class);
    }

    public function subscriptions() {
        return $this->hasMany(SubscriptionHistory::class);
    }
}
