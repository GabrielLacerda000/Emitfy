<script setup lang="ts">
import { ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';

defineProps<{
    question: string;
    answer: string;
}>();

const isOpen = ref(false);
</script>

<template>
    <Collapsible 
        v-model:open="isOpen" 
        class="group border-b border-border/50 transition-all duration-300"
    >
        <CollapsibleTrigger
            class="flex w-full items-center justify-between py-6 text-left font-semibold text-foreground/90 transition-colors hover:text-primary focus:outline-none"
        >
            <span class="text-base sm:text-lg leading-tight tracking-tight">
                {{ question }}
            </span>
            
            <div :class="[
                'ml-4 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-border transition-all duration-300 group-hover:border-primary/30 group-hover:bg-primary/5',
                isOpen && 'bg-primary border-primary group-hover:bg-primary group-hover:border-primary'
            ]">
                <ChevronDown
                    :class="[
                        'h-4 w-4 transition-transform duration-500 ease-in-out',
                        isOpen ? 'rotate-180 text-primary-foreground' : 'text-muted-foreground group-hover:text-primary',
                    ]"
                />
            </div>
        </CollapsibleTrigger>

        <CollapsibleContent class="overflow-hidden transition-all duration-300 ease-in-out data-[state=closed]:animate-collapse-up data-[state=open]:animate-collapse-down">
            <div class="pb-6 pr-12">
                <p class="text-base leading-relaxed text-muted-foreground">
                    {{ answer }}
                </p>
            </div>
        </CollapsibleContent>
    </Collapsible>
</template>

<style scoped>
/* Adicionamos animações personalizadas se não estiverem no seu tailwind.config.js */
.animate-collapse-down {
  animation: collapse-down 0.3s ease-out;
}
.animate-collapse-up {
  animation: collapse-up 0.3s ease-out;
}

@keyframes collapse-down {
  from { height: 0; opacity: 0; }
  to { height: var(--radix-collapsible-content-height); opacity: 1; }
}

@keyframes collapse-up {
  from { height: var(--radix-collapsible-content-height); opacity: 1; }
  to { height: 0; opacity: 0; }
}
</style>