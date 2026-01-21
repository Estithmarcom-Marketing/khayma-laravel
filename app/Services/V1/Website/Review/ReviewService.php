<?php

namespace App\Services\V1\Website\Review;

use App\Models\Product;
use App\Models\Review;

class ReviewService
{
    public function store(array $data)
    {
        $user = auth()->user();
        if ($user->reviews()->where('product_id', $data['product_id'])->exists()) {

            $review = $user->reviews()->where('product_id', $data['product_id'])->first();
            $review->update([
                'rating' => $data['rating'] ?? $review->rating,
                'comment' => $data['comment'] ?? $review->comment,
            ]);

            return $review->refresh();
        }

        return $user->reviews()->create([
            'product_id' => $data['product_id'],
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);
    }

    public function update(array $data, Review $review)
    {
        $review->update([
            'rating' => $data['rating'] ?? $review->rating,
            'comment' => $data['comment'] ?? $review->comment,
        ]);

        return $review->refresh();
    }

    public function destroy($review)
    {
        return $review->delete();
    }

    public function getMyReviews()
    {
        return auth()->user()->reviews()->with('product.media')->paginate(10);
    }

    public function getReviews(Product $product)
    {
        $user = auth()->user();

        $reviewsQuery = $product->reviews()->with(['user.media']);

        if ($user) {
            $reviewsQuery->orderByRaw('user_id = ? DESC', [$user->id]);
        }

        return $reviewsQuery->paginate(10);
    }
}
