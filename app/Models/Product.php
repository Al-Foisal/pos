<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    use HasFactory;
    protected $guarded = [];

    public function purchaseDetails() {
        return $this->hasMany(PurchaseDetails::class);
    }

    public function returnPurchaseDetails() {
        return $this->hasMany(ReturnPurchaseDetails::class);
    }

}
