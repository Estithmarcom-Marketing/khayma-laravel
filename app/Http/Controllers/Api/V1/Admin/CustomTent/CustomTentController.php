<?php

namespace App\Http\Controllers\Api\V1\Admin\CustomTent;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomTent\ChangeCustomTentStatusRequest;
use App\Http\Requests\CustomTent\ListCustomTentsRequest;
use App\Http\Resources\Dashboard\CustomTent\CustomTentResource;
use App\Models\CustomTent;
use App\Services\V1\Admin\CustomTent\CustomTentService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Admin Custom Tent')]
class CustomTentController extends Controller
{
    public function __construct(private CustomTentService $customTentService) {}

    public function index(ListCustomTentsRequest $request)
    {
        try {
            $customTents = $this->customTentService->list($request->validated());
            $data = CustomTentResource::collection($customTents)->response()->getData(true);

            return ApiResponse::successResponse([
                'custom_tents' => $data['data'],
                'meta' => $data['meta'],
                'links' => $data['links']
            ], __('custom_tent.fetched'), status: Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('custom_tent.error_fetch'), ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('custom_tent.error_fetch'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(CustomTent $customTent)
    {
        try {
            $customTent = $this->customTentService->show($customTent);

            return ApiResponse::successResponse(
                CustomTentResource::make($customTent),
                __('custom_tent.shown'),
                status: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error(__('custom_tent.error_show'), ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('custom_tent.error_show'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus(CustomTent $customTent, ChangeCustomTentStatusRequest $request)
    {
        try {
            $customTent = $this->customTentService->changeStatus($customTent, $request->validated());

            return ApiResponse::successResponse(
                CustomTentResource::make($customTent),
                __('custom_tent.status_updated'),
                status: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error(__('custom_tent.error_status_update'), ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('custom_tent.error_status_update'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy(CustomTent $customTent)
    {
        try {
            $this->customTentService->delete($customTent);

            return ApiResponse::successResponse(null, __('custom_tent.deleted'), status: Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            Log::error(__('custom_tent.error_delete'), ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('custom_tent.error_delete'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
