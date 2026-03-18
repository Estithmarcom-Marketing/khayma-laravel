<?php

namespace App\Services\V1\Admin\Stats;

use App\Enums\Orders\OrderStatusEnum;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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

    public function getSalesChart()
    {
        $arabicMonths = [
            1 => 'يناير',  2 => 'فبراير', 3  => 'مارس',    4  => 'أبريل',
            5 => 'مايو',   6 => 'يونيو',  7  => 'يوليو',   8  => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];

        return Order::select([
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_price) as sales'),
            ])
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => (object) [
                'month' => $arabicMonths[$row->month],
                'sales' => (float) $row->sales,
            ]);
    }
}
