<?php

namespace App\Console\Commands;

use App\Services\SoldCountService;
use Illuminate\Console\Command;

class RecalculateSoldCountCommand extends Command
{
    protected $signature = 'products:recalculate-sold-count';

    protected $description = 'Tính lại products.sold_count từ order_items theo đơn hàng hợp lệ (confirmed/processing/shipping/delivered)';

    public function handle(SoldCountService $soldCountService): int
    {
        $this->info('Đang tính lại sold_count từ order_items...');

        $affected = $soldCountService->recalculateAll();

        $this->info("Hoàn tất. Đã cập nhật {$affected} sản phẩm.");

        return self::SUCCESS;
    }
}
