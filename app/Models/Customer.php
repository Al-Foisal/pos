<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
    use HasFactory;
    protected $guarded = [];
    protected $dates   = ['modify_date'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function incomeExpenses() {
        return $this->hasMany(IncomeExpense::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function returnOrders() {
        return $this->hasMany(ReturnOrder::class);
    }

    public function transactionHistory() {
        return $this->hasMany(TransactionHistory::class);
    }

    public function group()
    {
        return $this->belongsTo(GroupInfo::class);
    }
}
