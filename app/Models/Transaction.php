<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
        protected $fillable = [
        'type',
        'amount',
        'date',
        'description',
        'category_id',
        'receipt_path',
        'user_id',
    ];

     protected $dates = ['date'];

    public function user() {
    return $this->belongsTo(User::class);
}

public function category() {
    return $this->belongsTo(Category::class);
}

}
