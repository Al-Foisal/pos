<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeExpense extends Model {
    use HasFactory;
    protected $guarded = [];

    public function businessAccount() {
        return $this->belongsTo(User::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function income() {
        return $this->belongsTo(Income::class);
    }

    public function expense() {
        return $this->belongsTo(Expense::class);
    }

    public function purpose() {
        return $this->belongsTo(Purpose::class);
    }

    public function expensePurposes() {
        return $this->belongsTo(ExpensePurpose::class);
    }

    public function paymentType() {
        return $this->belongsTo(PaymentType::class);
    }
}
