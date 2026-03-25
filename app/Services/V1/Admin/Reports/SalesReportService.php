<?php

namespace App\Services\V1\Admin\Reports;

use App\Enums\Orders\OrderStatusEnum;
use App\Models\Order;
use App\Models\PaymentMethod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    private const ARABIC_MONTHS = [
        1  => 'يناير',  2  => 'فبراير', 3  => 'مارس',    4  => 'أبريل',
        5  => 'مايو',   6  => 'يونيو',  7  => 'يوليو',   8  => 'أغسطس',
        9  => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر',  12 => 'ديسمبر',
    ];

    private const COMPLETED_STATUSES = [OrderStatusEnum::SHIPPED, OrderStatusEnum::DELIVERED];

    private function periodConfig(string $period): array
    {
        return match ($period) {
            'day'  => ['start' => now()->subDays(30)->startOfDay(), 'group' => 'DATE(created_at)'],
            'year' => ['start' => now()->subYears(5)->startOfYear(), 'group' => 'YEAR(created_at)'],
            default => ['start' => now()->startOfYear(), 'group' => 'MONTH(created_at)'],
        };
    }

    private function quantityPerOrderSubquery()
    {
        return DB::table('order_products')
            ->selectRaw('order_id, SUM(quantity) as quantity_sum')
            ->groupBy('order_id');
    }

    public function getSalesByDate(string $period): \Illuminate\Support\Collection
    {
        ['start' => $startDate, 'group' => $groupExpr] = $this->periodConfig($period);

        return Order::where('created_at', '>=', $startDate)
            ->whereIn('status', self::COMPLETED_STATUSES)
            ->leftJoinSub($this->quantityPerOrderSubquery(), 'op_agg', 'op_agg.order_id', '=', 'orders.id')
            ->selectRaw("
                {$groupExpr}                                   as period_group,
                COUNT(orders.id)                               as orders_count,
                COALESCE(SUM(orders.total_price), 0)           as total_revenue,
                COALESCE(SUM(op_agg.quantity_sum), 0)          as products_count
            ")
            ->groupByRaw($groupExpr)
            ->orderByRaw($groupExpr)
            ->get()
            ->map(fn ($row) => [
                'period'         => $this->formatPeriodGroup($period, $row->period_group),
                'orders_count'   => (int)   $row->orders_count,
                'total_revenue'  => (float) $row->total_revenue,
                'products_count' => (int)   $row->products_count,
            ]);
    }

    private function formatPeriodGroup(string $period, string|int $value): string
    {
        return match ($period) {
            'year'  => (string) $value,
            'day'   => Carbon::parse($value)->format('d/m/Y'),
            default => self::ARABIC_MONTHS[(int) $value] ?? (string) $value,
        };
    }

    public function getSalesByPaymentMethod(string $period)
    {
        $startDate = $this->periodConfig($period)['start'];

        return PaymentMethod::select([
                'payment_methods.id',
                'payment_methods.name_ar',
                'payment_methods.name_en',
                'payment_methods.type',
                DB::raw('COALESCE(SUM(orders.total_price), 0) as total_revenue'),
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('COALESCE(SUM(op_agg.quantity_sum), 0) as products_count'),
            ])
            ->leftJoin('orders', function ($join) use ($startDate) {
                $join->on('orders.payment_method_id', '=', 'payment_methods.id')
                    ->where('orders.created_at', '>=', $startDate)
                    ->whereNull('orders.deleted_at');
            })
            ->leftJoinSub($this->quantityPerOrderSubquery(), 'op_agg', 'op_agg.order_id', '=', 'orders.id')
            ->groupBy('payment_methods.id', 'payment_methods.name_ar', 'payment_methods.name_en', 'payment_methods.type')
            ->get();
    }
}
