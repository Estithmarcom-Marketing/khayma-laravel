<?php

namespace App\Http\Controllers\Api\V1\User\Favourite;

use App\Http\Controllers\Controller;
use App\Http\Resources\Favourite\FavouriteResource;
use App\Models\Favourite;
use App\Models\Product;
use App\Services\V1\User\Favourite\FavouriteService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class FavouriteController extends Controller
{
    public function __construct(protected FavouriteService $service) {}

    public function index()
    {
        try {
            $favourites = $this->service->list();
            $favourites = FavouriteResource::collection($favourites)->response()->getData(true);

            return ApiResponse::successResponse([
                'favourites' => $favourites['data'],
                'meta' => $favourites['meta'],
                'links' => $favourites['links'],
            ], 'Favourites retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch favourites', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch favourites', Response::HTTP_INTERNAL_SERVER_ERROR
            );

        }
    }

    public function store(Product $product)
    {
        try {
            $favourite = $this->service->store($product);
            $favourite = FavouriteResource::make($favourite);

            return ApiResponse::successResponse([
                'favourite' => $favourite,
            ], 'Favourite created successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create favourite', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to create favourite', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Favourite $favourite)
    {
        try {
            $this->service->deleteProductFromFavourite($favourite);

            return ApiResponse::successResponse([], 'Favourite deleted successfully', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete favourite', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to delete favourite', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Favourite $favourite)
    {
        try {

            $favourite = $this->service->show($favourite);
            $favourite = FavouriteResource::make($favourite);

            return ApiResponse::successResponse([
                'favourite' => $favourite,
            ], 'Favourite retrieved successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch favourite', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse('Failed to fetch favourite', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
