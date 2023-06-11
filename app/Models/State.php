<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class State extends Model {
    use HasFactory;
    protected $guarded = [];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }
}
