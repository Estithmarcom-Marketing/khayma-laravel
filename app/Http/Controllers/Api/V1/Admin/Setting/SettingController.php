<?php

namespace App\Http\Controllers\Api\V1\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingRequest;
use App\Http\Resources\Setting\SettingResource;
use App\Services\V1\Admin\Setting\SettingService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Admin Setting')]
class SettingController extends Controller
{
    public function __construct(protected SettingService $settingService) {}

    public function index()
    {
        try {
            $setting = $this->settingService->getSettings();
            return ApiResponse::successResponse($setting ? new SettingResource($setting) : null, __('setting.retrieved'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('setting.error_retrieved'), [$e->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('setting.error_retrieved'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function createOrUpdate(SettingRequest $request)
    {
        try {
            $setting = $this->settingService->updateOrCreate($request->validated());
            return ApiResponse::successResponse(new SettingResource($setting), __('setting.update_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('setting.error_retrieved'), [$e->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('setting.error_retrieved'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
