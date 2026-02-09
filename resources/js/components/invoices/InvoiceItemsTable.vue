<script setup lang="ts">
import { Trash2, Plus } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { NumberField, NumberFieldContent, NumberFieldInput } from '@/components/ui/number-field';
import { formatBRL } from '@/lib/utils';
import type { InvoiceItem } from '@/types';

interface Props {
    modelValue: InvoiceItem[];
    errors?: Record<string, string>;
    readonly?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    readonly: false,
});

const emit = defineEmits<{
    'update:modelValue': [items: InvoiceItem[]];
}>();

function addItem() {
    const newItems = [
        ...props.modelValue,
        {
            description: '',
            quantity: 1,
            unit_price: 0,
            total: 0,
        },
    ];
    emit('update:modelValue', newItems);
}

function removeItem(index: number) {
    if (props.modelValue.length > 1) {
        const newItems = props.modelValue.filter((_, i) => i !== index);
        emit('update:modelValue', newItems);
    }
}

function updateItem(index: number, field: keyof InvoiceItem, value: string | number) {
    const newItems = [...props.modelValue];
    newItems[index] = { ...newItems[index], [field]: value };

    // Calculate total for this item
    if (field === 'quantity' || field === 'unit_price') {
        const quantity = newItems[index].quantity;
        const unitPrice = newItems[index].unit_price;
        newItems[index].total = Number((quantity * unitPrice).toFixed(2));
    }

    emit('update:modelValue', newItems);
}
</script>

<template>
    <div class="space-y-4">
        <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr
                            class="border-b border-sidebar-border/70 dark:border-sidebar-border"
                        >
                            <th
                                class="px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Description
                            </th>
                            <th
                                class="w-24 px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Qty
                            </th>
                            <th
                                class="w-32 px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Unit Price
                            </th>
                            <th
                                class="w-32 px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            >
                                Total
                            </th>
                            <th
                                v-if="!readonly"
                                class="w-16 px-4 py-3 text-left text-sm font-medium text-muted-foreground"
                            ></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in modelValue"
                            :key="index"
                            class="border-b border-sidebar-border/70 last:border-b-0 dark:border-sidebar-border"
                        >
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    <Input
                                        v-if="!readonly"
                                        :model-value="item.description"
                                        @update:model-value="(val) => updateItem(index, 'description', val)"
                                        placeholder="Item description"
                                        required
                                    />
                                    <span v-else class="text-sm">{{ item.description }}</span>
                                    <InputError
                                        :message="errors?.[`items.${index}.description`]"
                                    />
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    <NumberField
                                        v-if="!readonly"
                                        :model-value="item.quantity"
                                        @update:model-value="(val) => updateItem(index, 'quantity', val)"
                                        :min="1"
                                        :format-options="{ style: 'decimal' }"
                                    >
                                        <NumberFieldContent>
                                            <NumberFieldInput placeholder="1" />
                                        </NumberFieldContent>
                                    </NumberField>
                                    <span v-else class="text-sm">{{ item.quantity }}</span>
                                    <InputError
                                        :message="errors?.[`items.${index}.quantity`]"
                                    />
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="space-y-1">
                                    <NumberField
                                        v-if="!readonly"
                                        :model-value="item.unit_price"
                                        @update:model-value="(val) => updateItem(index, 'unit_price', val)"
                                        :min="0.01"
                                        :step="0.01"
                                        :format-options="{
                                            style: 'currency',
                                            currency: 'BRL',
                                        }"
                                    >
                                        <NumberFieldContent>
                                            <NumberFieldInput placeholder="R$ 0,00" />
                                        </NumberFieldContent>
                                    </NumberField>
                                    <span v-else class="text-sm">{{ formatBRL(item.unit_price) }}</span>
                                    <InputError
                                        :message="errors?.[`items.${index}.unit_price`]"
                                    />
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium">{{ formatBRL(item.total) }}</div>
                            </td>
                            <td v-if="!readonly" class="px-4 py-3">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    type="button"
                                    @click="removeItem(index)"
                                    :disabled="modelValue.length === 1"
                                >
                                    <Trash2 class="h-4 w-4 text-destructive" />
                                    <span class="sr-only">Remove item</span>
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Button
            v-if="!readonly && modelValue.length < 50"
            type="button"
            variant="outline"
            @click="addItem"
        >
            <Plus class="mr-2 h-4 w-4" />
            Add Item
        </Button>
    </div>
</template>
