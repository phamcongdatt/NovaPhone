<?php

namespace App\Services;

use App\Models\ReturnRequest;
use Barryvdh\DomPDF\Facade\Pdf;

class RefundReceiptService
{
    public function pdf(ReturnRequest $returnRequest): string
    {
        $returnRequest->loadMissing(['order', 'user', 'items.orderItem']);

        return Pdf::loadView('returns.receipt', compact('returnRequest'))
            ->setPaper('a4')
            ->output();
    }
}
