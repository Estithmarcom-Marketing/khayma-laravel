<?php

namespace App\Http\Controllers\Api\V1\Application\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notifications\NotificationResource;
use App\Services\V1\Website\Notification\NotificationService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Dedoc\Scramble\Attributes\Group;
#[Group('Application Notification')]
class NotificationController extends Controller
{
    public function __construct(protected NotificationService $service) {}

    public function index()
    {
        try {
            $notification = $this->service->index();
            $notification = NotificationResource::collection($notification)->response()->getData(true);

            return ApiResponse::successResponse([
                'notifications' => $notification['data'],
                'meta' => $notification['meta'],
                'links' => $notification['links'],
            ], __('notification.list_success'), Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Failed to fetch notifications', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('notification.list_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUnReadNotifications()
    {
        try {
            $notification = $this->service->getUnReadNotifications();
            $notification = NotificationResource::collection($notification)->response()->getData(true);

            return ApiResponse::successResponse([
                'notifications' => $notification['data'],
                'meta' => $notification['meta'],
                'links' => $notification['links'],
            ], __('notification.list_success'), Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Failed to fetch notifications', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('notification.list_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function markAllAsRead()
    {
        try {
            $this->service->markAllAsRead();

            return ApiResponse::successResponse([], __('notification.mark_read_success'), Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error('Failed to mark notifications as read', ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('notification.mark_read_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
