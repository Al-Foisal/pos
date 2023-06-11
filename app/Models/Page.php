<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model {
    use HasFactory;
    protected $guarded = [];
    public function setEnNameAttribute($value) {
        $this->attributes['en_name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
}
