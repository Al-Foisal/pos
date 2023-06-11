<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrder extends Model {
    use HasFactory;
    protected $guarded = [];
    public function orderDetails() {
        return $this->hasMany(ReturnOrderDetails::class);
    }

    public function businessAccount() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function paymentType() {
        return $this->belongsTo(PaymentType::class);
    }
}
