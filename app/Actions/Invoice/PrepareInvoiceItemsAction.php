<?php

namespace App\Actions\Invoice;

class PrepareInvoiceItemsAction
{
    /**
     * Prepare invoice items by adding calculated total to each item.
     */
    public function __invoke(array $items): array
    {
        return array_map(function ($item) {
            $item['total'] = round($item['quantity'] * $item['unit_price'], 2);

            return $item;
        }, $items);
    }
}
