<?php

namespace App\Services\V1\Admin\Customer;

use App\Models\User;

class CustomerService
{

    public function getCustomers($filters)
    {
        $per_page = (int) ($filters['per_page'] ?? 10);
        return User::query()
            ->when(
                $filters['search'] ?? null,
                fn($query, $search) =>
                $query->search($search)
            )
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->latest('id')
            ->paginate($per_page);
    }

    public function getCustomerById($customerId)
    {
        return User::with('orders')
            ->withCount('orders')
            ->withSum('orders', 'total_price')
            ->findOrFail($customerId);
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
