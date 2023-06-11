<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model {
    use HasFactory;
    protected $guarded = [];

    public function purchase() {
        return $this->hasMany(Purchase::class);
    }

    public function incomeExpenses() {
        return $this->hasMany(IncomeExpense::class, 'expense_id', 'id');
    }
}
