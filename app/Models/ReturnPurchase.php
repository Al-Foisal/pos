<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnPurchase extends Model {
    use HasFactory;
    protected $guarded = [];
    public function returnPurchaseDetails() {
        return $this->hasMany(ReturnPurchaseDetails::class);
    }

    public function businessAccount() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }

    public function paymentType() {
        return $this->belongsTo(PaymentType::class);
    }
}
