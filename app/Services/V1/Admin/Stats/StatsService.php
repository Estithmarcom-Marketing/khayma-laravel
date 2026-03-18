<?php

namespace App\Services\V1\Admin\Stats;

use App\Enums\Orders\OrderStatusEnum;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class StatsService
{
    public function getCurrentMonthOrderCount(): array
    {
        $currentMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonthDate = now()->subMonth();
        $lastMonth = Order::whereMonth('created_at', $lastMonthDate->month)
            ->whereYear('created_at', $lastMonthDate->year)
            ->count();

        $percentageChange = $lastMonth > 0
            ? round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1)
            : 100;

        return [
            'total'             => $currentMonth,
            'percentage_change' => $percentageChange,
            'trend'             => $percentageChange >= 0 ? 'up' : 'down',
        ];
    }

    public function getCurrentMonthRevenue(): array
    {
        $currentMonth = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');

        $lastMonthDate = now()->subMonth();
        $lastMonth = Order::whereMonth('created_at', $lastMonthDate->month)
            ->whereYear('created_at', $lastMonthDate->year)
            ->sum('total_price');

        $percentageChange = $lastMonth > 0
            ? round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1)
            : 100;

        return [
            'total'             => $currentMonth,
            'percentage_change' => $percentageChange,
            'trend'             => $percentageChange >= 0 ? 'up' : 'down',
        ];
    }

    public function getProductsCount(): int
    {
        return Product::count();
    }

    public function getCurrentMonthCustomersCount(): array
    {
        $currentMonth = User::where('is_guest', false)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonthDate = now()->subMonth();
        $lastMonth = User::where('is_guest', false)
            ->whereMonth('created_at', $lastMonthDate->month)
            ->whereYear('created_at', $lastMonthDate->year)
            ->count();

        $percentageChange = $lastMonth > 0
            ? round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1)
            : 100;

        return [
            'total'             => $currentMonth,
            'percentage_change' => $percentageChange,
            'trend'             => $percentageChange >= 0 ? 'up' : 'down',
        ];
    }

    public function getTotalPendingOrders(): int
    {
        return Order::where('status', OrderStatusEnum::PENDING)->count();
    }
}
