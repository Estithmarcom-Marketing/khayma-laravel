<?php

namespace App\Http\Controllers\Api\V1\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Http\Resources\Notifications\AdminNotificationResource;
use App\Models\AdminNotification;
use App\Services\V1\Admin\Notification\NotificationService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Admin Notification')]
class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index()
    {
        try {
            $notifications = $this->notificationService->getAll();
            $notifications = AdminNotificationResource::collection($notifications)->response()->getData(true);
            return ApiResponse::successResponse([
                'notifications' => $notifications['data'],
                'meta' => $notifications['meta'],
                'links' => $notifications['links'],
            ], __('notification.list_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('notification.list_failed'), ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('notification.list_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUnReadNotifications()
    {
        try {

            $notifications = $this->notificationService->getUnread();
            $notifications = AdminNotificationResource::collection($notifications)->response()->getData(true);

            return ApiResponse::successResponse([
                'notifications' => $notifications['data'],
                'meta' => $notifications['meta'],
                'links' => $notifications['links']
            ], __('notification.unread_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('notification.unread_failed'), ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('notification.unread_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function markAllAsRead()
    {
        try {

            $this->notificationService->markAllAsRead();

            return ApiResponse::successResponse(null, __('notification.mark_all_read_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('notification.mark_read_failed'), ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('notification.mark_all_read_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function markAsRead(AdminNotification $notification)
    {
        try {
            $this->notificationService->markAsRead($notification);
            return ApiResponse::successResponse(null, __('notification.mark_read_success'), Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error(__('notification.mark_read_failed'), ['error' => $e->getMessage(), 'method' => __METHOD__]);

            return ApiResponse::errorResponse(__('notification.mark_read_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
