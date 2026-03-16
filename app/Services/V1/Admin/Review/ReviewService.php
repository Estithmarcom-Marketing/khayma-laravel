<?php

namespace App\Services\V1\Admin\Review;

use App\Models\Review;

class ReviewService
{
    public function list()
    {
        return Review::with(['product' , 'user'])
        ->paginate();
    }
    
    public function delete($review)
    {
        Review::findOrFail($review)->delete();
    }
}
