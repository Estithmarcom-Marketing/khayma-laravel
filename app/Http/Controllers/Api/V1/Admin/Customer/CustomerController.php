<?php

namespace App\Http\Controllers\Api\V1\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\GetCustomerRequest;
use App\Http\Resources\User\UserResource;
use App\Services\V1\Admin\Customer\CustomerService;
use Illuminate\Http\Request;
use App\Traits\Response\ApiResponse;
use Log;
use Symfony\Component\HttpFoundation\Response;


class CustomerController extends Controller
{
    public function __construct(private CustomerService $customerService){}

    public function index(GetCustomerRequest $request)
    {
        $data= $request->validated();
        $customers = $this->customerService->getCustomers($data);
        $data = UserResource::collection($customers)->response()->getData(true);
        return ApiResponse::successResponse([
                'customers' => $data['data'],
                'meta' => $data['meta'],
                'links' => $data['links'],
            ],
            __('customer.customers_retrieved_successfully'),
            status: Response::HTTP_OK);
    }

    public function destroy($customerId)
    {
        try {
            $this->customerService->deleteCustomer($customerId);
            return ApiResponse::successResponse(null, __('customer.deleted_successfully'), status: Response::HTTP_OK);
        } catch (\Throwable $th) {
            Log::error('Failed to delete customer', ['error' => $th->getMessage(), 'method' => __METHOD__]);
            return ApiResponse::errorResponse('Failed to delete customer', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
       
    }
}
