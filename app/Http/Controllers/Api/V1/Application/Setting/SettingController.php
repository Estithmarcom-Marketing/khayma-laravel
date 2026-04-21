<?php

namespace App\Http\Controllers\Api\V1\Application\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\Application\Setting\SettingResource;
use App\Services\V1\Website\Setting\SettingService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Application Settings')]
class SettingController extends Controller
{
    public function __construct(public SettingService $service) {}
    /**
     * @unauthenticated
     */
    public function __invoke()
    {
        try {
            $settings = $this->service->getSettings();
            $settings = SettingResource::make($settings);
            return ApiResponse::successResponse([
                'settings' => $settings
            ], __('setting.retrieved'), Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Failed to retrieve settings', ['error' => $th->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('setting.error_retrieved'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
