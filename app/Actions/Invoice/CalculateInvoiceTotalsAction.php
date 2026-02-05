<?php

namespace App\Actions\Invoice;

class CalculateInvoiceTotalsAction
{
    /**
     * Calculate invoice subtotal and total from items and tax.
     *
     * @return array{subtotal: float, total: float}
     */
    public function __invoke(array $items, float $tax): array
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $subtotal = round($subtotal, 2);
        $total = round($subtotal + $tax, 2);

        return [
            'subtotal' => $subtotal,
            'total' => $total,
        ];
    }
}
