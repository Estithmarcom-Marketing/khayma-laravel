<?php

namespace App\Http\Controllers\Api\V1\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\GetCustomerRequest;
use App\Http\Resources\User\UserResource;
use App\Services\V1\Admin\Customer\CustomerService;
use Illuminate\Http\Request;
use App\Traits\Response\ApiResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Dedoc\Scramble\Attributes\Group;

#[Group('Admin Customer')]
class CustomerController extends Controller
{
    public function __construct(private CustomerService $customerService) {}

    public function index(GetCustomerRequest $request)
    {
        try {
            $data = $request->validated();
            $customers = $this->customerService->getCustomers($data);
            $data = UserResource::collection($customers)->response()->getData(true);
            return ApiResponse::successResponse(
                [
                    'customers' => $data['data'],
                    'meta' => $data['meta'],
                    'links' => $data['links'],
                ],
                __('customer.fetched'),
                status: Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            Log::error('Failed to fetch customers', ['error' => $th->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('customer.error_fetch'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function show($customerId)
    {
        try {
            $customer = $this->customerService->getCustomerById($customerId);
            return ApiResponse::successResponse(new UserResource($customer), __('customer.fetched'), status: Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Failed to fetch customer', ['error' => $th->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('customer.error_fetch'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($customerId)
    {
        try {
            $this->customerService->deleteCustomer($customerId);
            return ApiResponse::successResponse(null, __('customer.deleted'), status: Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Failed to delete customer', ['error' => $th->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse(__('customer.error_delete'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
