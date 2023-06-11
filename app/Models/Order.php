<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;
    protected $guarded = [];
    public function orderDetails() {
        return $this->hasMany(OrderDetails::class);
    }

    public function businessAccount() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function paymentType() {
        return $this->belongsTo(PaymentType::class);
    }

    public function income() {
        return $this->belongsTo(Income::class);
    }

    public function returnOrder() {
        return $this->hasMany(ReturnOrder::class);
    }
}
