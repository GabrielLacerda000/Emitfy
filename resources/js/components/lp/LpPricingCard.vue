<script setup lang="ts">
import { Check, Info } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';

const { t } = useI18n();

defineProps<{
    name: string;
    price: string;
    description: string;
    features: Array<{ text: string; included: boolean; info?: string }>;
    cta: string;
    ctaHref: string;
    highlighted?: boolean;
    savings?: string;
}>();
</script>

<template>
    <Card
        :class="[
            'relative flex flex-col transition-all duration-300',
            highlighted
                ? 'border-primary shadow-2xl shadow-primary/10 ring-1 ring-primary scale-105 z-10'
                : 'border-border/60 opacity-90 lg:translate-y-4',
        ]"
    >
        <div v-if="highlighted || savings" class="absolute -top-4 left-0 right-0 flex justify-center">
            <span :class="[
                'rounded-full px-4 py-1 text-xs font-bold uppercase tracking-tighter shadow-sm',
                highlighted ? 'bg-primary text-primary-foreground' : 'bg-emerald-500 text-white'
            ]">
                {{ highlighted ? t('lp.pricing.mostPopular') : savings }}
            </span>
        </div>

        <CardHeader class="space-y-2 pb-8">
            <CardTitle class="text-xl font-bold">{{ name }}</CardTitle>
            <div class="flex items-baseline gap-1">
                <span class="text-4xl font-extrabold tracking-tight">{{ price }}</span>
                <span v-if="price !== 'Free'" class="text-muted-foreground font-medium text-sm">{{ t('lp.pricing.perMonth') }}</span>
            </div>
            <p class="text-sm text-muted-foreground leading-relaxed">{{ description }}</p>
        </CardHeader>

        <CardContent class="grow border-t border-border/40 pt-8">
            <ul class="space-y-4">
                <li v-for="(feature, index) in features" :key="index" class="flex items-start gap-3">
                    <div :class="['mt-1 rounded-full p-0.5', feature.included ? 'bg-primary/10 text-primary' : 'text-muted-foreground/30']">
                        <Check v-if="feature.included" class="h-3.5 w-3.5" />
                        <span v-else class="block w-3.5 h-px bg-current"></span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <span :class="['text-sm', feature.included ? 'text-foreground/90 font-medium' : 'text-muted-foreground/60 line-through decoration-1']">
                            {{ feature.text }}
                        </span>

                        <TooltipProvider v-if="feature.info">
                            <Tooltip>
                                <TooltipTrigger><Info class="h-3.5 w-3.5 text-muted-foreground/50" /></TooltipTrigger>
                                <TooltipContent>{{ feature.info }}</TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                </li>
            </ul>
        </CardContent>

        <CardFooter class="pt-6">
            <Button
                :variant="highlighted ? 'default' : 'secondary'"
                class="w-full h-12 text-md font-bold transition-all hover:gap-3 group"
                as-child
            >
                <a :href="ctaHref">
                    {{ cta }}
                    <span class="opacity-0 group-hover:opacity-100 transition-all">→</span>
                </a>
            </Button>
        </CardFooter>
    </Card>
</template>
