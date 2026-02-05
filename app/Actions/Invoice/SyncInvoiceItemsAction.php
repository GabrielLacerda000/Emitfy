<?php

namespace App\Actions\Invoice;

use App\Models\Invoice;

class SyncInvoiceItemsAction
{
    /**
     * Sync invoice items (update existing, create new, delete removed).
     */
    public function __invoke(Invoice $invoice, array $items): void
    {
        $existingItemIds = $invoice->items()->pluck('id')->toArray();
        $submittedItemIds = [];

        foreach ($items as $itemData) {
            // Calculate item total
            $itemData['total'] = round($itemData['quantity'] * $itemData['unit_price'], 2);

            if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {
                // Update existing item
                $invoice->items()->where('id', $itemData['id'])->update($itemData);
                $submittedItemIds[] = $itemData['id'];
            } else {
                // Create new item
                $newItem = $invoice->items()->create($itemData);
                $submittedItemIds[] = $newItem->id;
            }
        }

        // Delete items that were not in the submitted data
        $itemsToDelete = array_diff($existingItemIds, $submittedItemIds);
        if (! empty($itemsToDelete)) {
            $invoice->items()->whereIn('id', $itemsToDelete)->delete();
        }
    }
}
