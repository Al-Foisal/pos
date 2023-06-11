<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model {
    use HasFactory;
    protected $guarded = [];
    public function purchaseDetails() {
        return $this->hasMany(PurchaseDetails::class);
    }

    public function businessAccount() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function expense() {
        return $this->belongsTo(Expense::class);
    }

    public function paymentType() {
        return $this->belongsTo(PaymentType::class);
    }

    public function returnPurchase() {
        return $this->hasMany(ReturnPurchase::class);
    }
}
