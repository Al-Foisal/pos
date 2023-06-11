<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteOrderDetails extends Model {
    use HasFactory;
    protected $guarded = [];
    public function favoriteOrder() {
        return $this->belongsTo(FavoriteOrder::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
