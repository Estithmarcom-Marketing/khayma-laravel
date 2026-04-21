<?php

namespace App\Http\Controllers\Api\V1\Admin\Fcm;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fcm\SendNotificationToManyRequest;
use App\Http\Requests\Fcm\SendNotificationToTopicRequest;
use App\Services\V1\Admin\Firebase\SendNotificationsService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Admin FCM')]
class SendNotificationsWithFcmController extends Controller
{
    use ApiResponse;
    public function __construct(public SendNotificationsService $service) {}
    public function sendToMany(SendNotificationToManyRequest $request)
    {
        try {
            $this->service->sendToMany($request->validated());
            return ApiResponse::successResponse([], __('notification.sent_successfully'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to send notification', ['error' => $e->getMessage(), 'request' => $request->validated(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('notification.sent_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function sendToTopic(SendNotificationToTopicRequest $request)
    {
        try {
            $this->service->sendToTopic($request->validated());
            return ApiResponse::successResponse([], __('notification.sent_successfully'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Failed to send notification', ['error' => $e->getMessage(), 'request' => $request->validated(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('notification.sent_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
