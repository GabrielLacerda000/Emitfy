<script setup lang="ts">
import { Trash2, Plus, Hash, Tag, DollarSign } from 'lucide-vue-next';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    NumberField,
    NumberFieldContent,
    NumberFieldInput,
} from '@/components/ui/number-field';
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
        { description: '', quantity: 1, unit_price: 0, total: 0 },
    ];
    emit('update:modelValue', newItems);
}

function removeItem(index: number) {
    if (props.modelValue.length > 1) {
        const newItems = props.modelValue.filter((_, i) => i !== index);
        emit('update:modelValue', newItems);
    }
}

function updateItem(
    index: number,
    field: keyof InvoiceItem,
    value: string | number,
) {
    const newItems = [...props.modelValue];
    newItems[index] = { ...newItems[index], [field]: value };

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
        <div
            :class="[
                'overflow-hidden transition-all',
                readonly
                    ? 'rounded-none border-none'
                    : 'rounded-2xl border border-border/60 bg-background shadow-sm',
            ]"
        >
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr
                            :class="[
                                'transition-colors',
                                readonly
                                    ? 'border-b-2 border-foreground/5 bg-transparent'
                                    : 'border-b border-border/60 bg-muted/30',
                            ]"
                        >
                            <th
                                class="px-6 py-4 text-left text-[10px] font-black tracking-[0.2em] text-muted-foreground/80 uppercase"
                            >
                                <div class="flex items-center gap-2">
                                    <Tag class="h-3 w-3" /> Description
                                </div>
                            </th>
                            <th
                                class="w-24 px-6 py-4 text-center text-[10px] font-black tracking-[0.2em] text-muted-foreground/80 uppercase"
                            >
                                <div
                                    class="flex items-center justify-center gap-2"
                                >
                                    <Hash class="h-3 w-3" /> Qty
                                </div>
                            </th>
                            <th
                                class="w-40 px-6 py-4 text-right text-[10px] font-black tracking-[0.2em] text-muted-foreground/80 uppercase"
                            >
                                <div
                                    class="flex items-center justify-end gap-2"
                                >
                                    <DollarSign class="h-3 w-3" /> Unit Price
                                </div>
                            </th>
                            <th
                                class="w-40 px-6 py-4 text-right text-[10px] font-black tracking-[0.2em] text-muted-foreground/80 uppercase"
                            >
                                Total
                            </th>
                            <th v-if="!readonly" class="w-16 px-6 py-4"></th>
                        </tr>
                    </thead>
                    <tbody :class="{ 'divide-y divide-border/40': !readonly }">
                        <tr
                            v-for="(item, index) in modelValue"
                            :key="index"
                            class="group transition-colors hover:bg-muted/5"
                        >
                            <td class="px-6 py-4 align-top">
                                <div v-if="!readonly" class="space-y-1.5">
                                    <Input
                                        :model-value="item.description"
                                        @update:model-value="
                                            (val) =>
                                                updateItem(
                                                    index,
                                                    'description',
                                                    val,
                                                )
                                        "
                                        placeholder="e.g. Brand Identity Design"
                                        class="h-10 rounded-xl border-border/40 bg-muted/20 transition-all focus-visible:bg-background"
                                        required
                                    />
                                    <InputError
                                        :message="
                                            errors?.[
                                                `items.${index}.description`
                                            ]
                                        "
                                    />
                                </div>
                                <div v-else class="flex flex-col gap-0.5">
                                    <span
                                        class="text-sm leading-relaxed font-bold text-foreground"
                                        >{{ item.description }}</span
                                    >
                                    <span
                                        class="text-[10px] font-medium tracking-wide text-muted-foreground uppercase"
                                        >Service Item</span
                                    >
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center align-top">
                                <div
                                    v-if="!readonly"
                                    class="mx-auto space-y-1.5"
                                >
                                    <NumberField
                                        :model-value="item.quantity"
                                        @update:model-value="
                                            (val) =>
                                                updateItem(
                                                    index,
                                                    'quantity',
                                                    val,
                                                )
                                        "
                                        :min="1"
                                    >
                                        <NumberFieldContent>
                                            <NumberFieldInput
                                                class="h-10 rounded-xl border-border/40 bg-muted/20 text-center focus-visible:bg-background"
                                            />
                                        </NumberFieldContent>
                                    </NumberField>
                                    <InputError
                                        :message="
                                            errors?.[`items.${index}.quantity`]
                                        "
                                    />
                                </div>
                                <span
                                    v-else
                                    class="text-sm font-black text-muted-foreground/80 italic"
                                >
                                    {{ item.quantity }}x
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right align-top">
                                <div v-if="!readonly" class="space-y-1.5">
                                    <NumberField
                                        :model-value="item.unit_price"
                                        @update:model-value="
                                            (val) =>
                                                updateItem(
                                                    index,
                                                    'unit_price',
                                                    val,
                                                )
                                        "
                                        :min="0"
                                        :step="0.01"
                                        :format-options="{
                                            style: 'currency',
                                            currency: 'BRL',
                                        }"
                                    >
                                        <NumberFieldContent>
                                            <NumberFieldInput
                                                class="h-10 rounded-xl border-border/40 bg-muted/20 text-right focus-visible:bg-background"
                                            />
                                        </NumberFieldContent>
                                    </NumberField>
                                    <InputError
                                        :message="
                                            errors?.[
                                                `items.${index}.unit_price`
                                            ]
                                        "
                                    />
                                </div>
                                <span
                                    v-else
                                    class="text-sm font-medium text-muted-foreground"
                                >
                                    {{ formatBRL(item.unit_price) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right align-top">
                                <div
                                    :class="[
                                        'text-sm tracking-tight transition-all',
                                        readonly
                                            ? 'text-md font-black text-foreground'
                                            : 'pt-2 font-bold text-primary',
                                    ]"
                                >
                                    {{ formatBRL(item.total) }}
                                </div>
                            </td>

                            <td v-if="!readonly" class="px-6 py-4 align-top">
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    type="button"
                                    class="h-10 w-10 rounded-xl text-muted-foreground transition-colors hover:bg-destructive/10 hover:text-destructive"
                                    @click="removeItem(index)"
                                    :disabled="modelValue.length === 1"
                                >
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div
            v-if="!readonly && modelValue.length < 50"
            class="flex justify-center pt-2"
        >
            <Button
                type="button"
                variant="outline"
                class="group h-12 rounded-2xl border-2 border-dashed px-8 transition-all hover:border-primary hover:bg-primary/5 hover:text-primary"
                @click="addItem"
            >
                <Plus
                    class="mr-2 h-4 w-4 transition-transform group-hover:rotate-90"
                />
                Add Line Item
            </Button>
        </div>
    </div>
</template>
