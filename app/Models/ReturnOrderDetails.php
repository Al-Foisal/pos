<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrderDetails extends Model {
    use HasFactory;
    protected $guarded = [];
    public function order() {
        return $this->belongsTo(ReturnOrder::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
