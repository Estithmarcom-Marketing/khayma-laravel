<?php

namespace App\Http\Controllers\Api\V1\Application\Favourite;

use App\Http\Controllers\Controller;
use App\Http\Resources\Favourite\FavouriteResource;
use App\Models\Favourite;
use App\Models\Product;
use App\Services\V1\Website\Favourite\FavouriteService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Dedoc\Scramble\Attributes\Group;
#[Group('Application Favourite')]
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
            ], __('favourite.list_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch favourites', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('favourite.list_failed'), Response::HTTP_INTERNAL_SERVER_ERROR
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
            ], __('favourite.store_success'), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Failed to create favourite', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('favourite.store_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(Product $product)
    {
        try {
            $this->service->deleteProductFromFavourite($product);

            return ApiResponse::successResponse([],
                __('favourite.delete_success'), Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error('Failed to delete favourite', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('favourite.delete_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(Favourite $favourite)
    {
        try {

            $favourite = $this->service->show($favourite);
            $favourite = FavouriteResource::make($favourite);

            return ApiResponse::successResponse([
                'favourite' => $favourite,
            ], __('favourite.show_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to fetch favourite', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('favourite.show_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
