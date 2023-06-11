<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model {
    use HasFactory;
    protected $guarded = [];
    public function order() {
        return $this->hasMany(Order::class)->where('user_id', auth()->user()->user_id);
    }

    public function income() {
        return $this->hasMany(IncomeExpense::class)->where('user_id', auth()->user()->user_id)->whereNotNull('income_id');
    }

    public function purchase() {
        return $this->hasMany(Purchase::class)->where('user_id', auth()->user()->user_id);
    }

    public function expense() {
        return $this->hasMany(IncomeExpense::class)->where('user_id', auth()->user()->user_id)->whereNotNull('expense_id');
    }

    public function senderBalanceTransfer() {
        return $this->hasMany(BalanceTransfer::class, 'sender_account', 'id')->where('user_id', auth()->user()->user_id);
    }

    public function receiverBalanceAccept() {
        return $this->hasMany(BalanceTransfer::class, 'receiver_account', 'id')->where('user_id', auth()->user()->user_id);
    }

    public function returnOrder() {
        return $this->hasMany(ReturnOrder::class)->where('user_id', auth()->user()->user_id);
    }

    public function returnPurchase() {
        return $this->hasMany(ReturnPurchase::class)->where('user_id', auth()->user()->user_id);
    }

    public function previousCashinHand() {
        return $this->hasMany(PreviousCashinHand::class);
    }
}
