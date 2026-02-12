<script setup lang="ts">
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    question: string;
    answer: string;
}>();

const isOpen = ref(false);
</script>

<template>
    <Collapsible v-model:open="isOpen" class="border-b border-border">
        <CollapsibleTrigger
            class="flex w-full items-center justify-between py-4 text-left font-medium transition-colors hover:text-foreground/80"
        >
            {{ question }}
            <ChevronDown
                :class="[
                    'h-5 w-5 flex-shrink-0 text-muted-foreground transition-transform duration-200',
                    isOpen && 'rotate-180',
                ]"
            />
        </CollapsibleTrigger>
        <CollapsibleContent class="pb-4 pt-1 text-sm text-muted-foreground">
            {{ answer }}
        </CollapsibleContent>
    </Collapsible>
</template>
