<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceTransfer extends Model {
    use HasFactory;
    protected $guarded = [];

    public function sender() {
        return $this->belongsTo(PaymentType::class, 'sender_account', 'id');
    }

    public function reciver() {
        return $this->belongsTo(PaymentType::class, 'receiver_account', 'id');
    }
}
