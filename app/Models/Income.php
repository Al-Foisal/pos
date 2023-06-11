<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model {
    use HasFactory;
    protected $guarded = [];

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function incomeExpenses() {
        return $this->hasMany(IncomeExpense::class, 'income_id', 'id');
    }

    public function incomeExpensesByDate($new_date) {
        return $this->hasMany(IncomeExpense::class, 'income_id', 'id')->whereDate('created_at', $new_date);
    }
}
