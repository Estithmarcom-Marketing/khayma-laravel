<?php

namespace App\Services\V1\Admin\Customer;

use App\Models\User;

class CustomerService
{

    public function getCustomers($filters)
    {
        $query = User::query();
        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }
        $query->withCount('orders')
        ->latest();
        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function deleteCustomer($customerId)
    {
        $customer = User::findOrFail($customerId);
        $customer->reviews()->delete();
        $customer->favourites()->delete();
        $customer->productReminders()->delete();
        $customer->notifications()->delete();
        $customer->cart()->delete();
        $customer->addresses()->delete();
        $customer->delete();
    }
}
