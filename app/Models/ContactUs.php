<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'message',
        'contacted',
    ];

    protected $casts = [
        'contacted' => 'boolean',
    ];
    public function scopeContacted($query, $value = true)
    {
        return $query->where('contacted', $value);
    }
    public function scopeSearch($query, $term)
    {
        $term = "%$term%";
        return $query->where(function ($query) use ($term) {
            $query->where('name', 'like', $term)
                ->orWhere('phone', 'like', $term);
        });
    }
}
