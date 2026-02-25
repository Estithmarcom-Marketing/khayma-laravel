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

    public function getStatistics($id)
    {
        $product = Product::findOrFail($id);

        $stats = $product->reviews()
            ->selectRaw('
            COUNT(*) as total_reviews,
            AVG(rating) as average_rating,
            SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
            SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
            SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
            SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
            SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
        ')
            ->first();

        $totalReviews = $stats->total_reviews ?? 0;

        return [
            'totalReviews' => $totalReviews,
            'averageRating' => round($stats->average_rating ?? 0, 1),
            'ratingDistribution' => [
                5 => $totalReviews > 0 ? round(($stats->five_star / $totalReviews) * 100, 1) : 0,
                4 => $totalReviews > 0 ? round(($stats->four_star / $totalReviews) * 100, 1) : 0,
                3 => $totalReviews > 0 ? round(($stats->three_star / $totalReviews) * 100, 1) : 0,
                2 => $totalReviews > 0 ? round(($stats->two_star / $totalReviews) * 100, 1) : 0,
                1 => $totalReviews > 0 ? round(($stats->one_star / $totalReviews) * 100, 1) : 0,
            ],
        ];
    }
}
