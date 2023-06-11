<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model {
    use HasFactory;
    protected $guarded = [];
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function userRoleAccess() {
        return $this->hasMany(UserRoleAccess::class);
    }
}
