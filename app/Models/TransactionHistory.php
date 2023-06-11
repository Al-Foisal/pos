<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionHistory extends Model {
    use HasFactory;
    protected $guarded = [];
    protected $dates   = ['c_date'];
    public function businessAccount() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function purchase() {
        return $this->belongsTo(Purchase::class);
    }

    public function purchaseDiscount() {
        return $this->belongsTo(Purchase::class, 'purchase_discount_id', 'id');
    }

    public function purchaseVat() {
        return $this->belongsTo(Purchase::class, 'purchase_vat_id', 'id');
    }

    public function income_fc() {
        return $this->belongsTo(IncomeExpense::class, 'income_id', 'id');
    }

    public function expense_ts() {
        return $this->belongsTo(IncomeExpense::class, 'expense_id', 'id');
    }

    public function returnOrder() {
        return $this->belongsTo(ReturnOrder::class);
    }

    public function returnPurchase() {
        return $this->belongsTo(ReturnPurchase::class);
    }
}
