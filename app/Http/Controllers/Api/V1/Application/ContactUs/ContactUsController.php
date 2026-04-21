<?php

namespace App\Http\Controllers\Api\V1\Application\ContactUs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Application\ContactUs\StoreContactUsMessageRequest;
use App\Services\V1\Website\ContactUs\ContactUsService;
use App\Traits\Response\ApiResponse;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

#[Group('Application ContactUs')]
class ContactUsController extends Controller
{
    public function __construct(public ContactUsService $service) {}
    public function __invoke(StoreContactUsMessageRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return ApiResponse::successResponse([], __('contact_us.stored_successfully'), Response::HTTP_CREATED);
        } catch (\Throwable $th) {
            Log::error('Failed to send contact us message ', ['error' => $th->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('contact_us.stored_failed'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
