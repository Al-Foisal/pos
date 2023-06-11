<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model {
    use HasFactory;
    protected $guarded = [];
    protected $dates   = ['modify_date'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function incomeExpenses() {
        return $this->hasMany(IncomeExpense::class);
    }

    public function purchase() {
        return $this->hasMany(Purchase::class);
    }

    public function returnPurchase() {
        return $this->hasMany(ReturnPurchase::class);
    }

    public function group()
    {
        return $this->belongsTo(GroupInfo::class);
    }
}
