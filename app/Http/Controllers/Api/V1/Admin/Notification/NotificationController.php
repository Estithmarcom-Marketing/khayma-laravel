<?php

namespace App\Http\Controllers\Api\V1\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notification\NotificationResource;
use App\Services\V1\Admin\Notification\NotificationService;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index()
    {
        try {
            $admin = auth('admin')->user();
            $notifications = $this->notificationService->getAll($admin);

            return ApiResponse::successResponse([
                'notifications' => NotificationResource::collection($notifications)
            ], __('notification.list_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('notification.list_failed'), ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('notification.list_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUnReadNotifications()
    {
        try {
            $admin = auth('admin')->user();
            $notifications = $this->notificationService->getUnread($admin);

            return ApiResponse::successResponse([
                'notifications' => NotificationResource::collection($notifications)
            ], __('notification.unread_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('notification.unread_failed'), ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('notification.unread_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function markAllAsRead()
    {
        try {
            $admin = auth('admin')->user();
            $this->notificationService->markAllAsRead($admin);

            return ApiResponse::successResponse(null, __('notification.mark_read_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('notification.mark_read_failed'), ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('notification.mark_read_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
