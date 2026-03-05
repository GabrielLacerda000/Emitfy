<script setup lang="ts">
import { Check, Info, Sparkles } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';

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
    <div class="group relative mx-auto max-w-md">
        <div
            class="absolute -inset-1 rounded-4xl bg-linear-to-r from-primary/50 to-purple-600/30 opacity-25 blur-xl transition duration-1000 group-hover:opacity-50"
        ></div>

        <Card
            :class="[
                'relative flex flex-col overflow-hidden border-primary/20 bg-card/80 shadow-2xl backdrop-blur-sm transition-all duration-500',
                'hover:-translate-y-1 hover:border-primary/50',
                'rounded-3xl border-2 ring-1 ring-primary/5',
            ]"
        >
            <div
                v-if="highlighted || savings"
                class="absolute -top-px right-0 left-0 flex justify-center"
            >
                <div
                    class="rounded-b-xl bg-linear-to-r from-primary to-purple-600 px-6 py-1.5 shadow-lg shadow-primary/20"
                >
                    <span
                        class="flex items-center gap-1.5 text-[10px] font-black tracking-[0.15em] text-white uppercase"
                    >
                        <Sparkles class="h-3 w-3" />
                        {{
                            highlighted ? t('lp.pricing.mostPopular') : savings
                        }}
                    </span>
                </div>
            </div>

            <CardHeader class="space-y-4 pt-10 pb-8 text-center">
                <div class="space-y-1">
                    <CardTitle
                        class="text-2xl font-black tracking-tight text-primary uppercase"
                        >{{ name }}</CardTitle
                    >
                    <p
                        class="px-4 text-sm leading-relaxed text-muted-foreground"
                    >
                        {{ description }}
                    </p>
                </div>

                <div class="flex flex-col items-center gap-0">
                    <div class="flex items-baseline gap-1">
                        <span
                            class="bg-linear-to-b from-foreground to-foreground/70 bg-clip-text text-5xl font-black tracking-tighter text-transparent"
                        >
                            {{ price }}
                        </span>
                        <span
                            v-if="price !== 'Free'"
                            class="text-sm font-semibold text-muted-foreground"
                        >
                            /{{ t('lp.pricing.perMonth') }}
                        </span>
                    </div>
                </div>
            </CardHeader>

            <CardContent class="grow px-8">
                <div class="mb-6 flex items-center gap-4">
                    <div
                        class="h-px grow bg-linear-to-r from-transparent via-border to-transparent"
                    ></div>
                    <span
                        class="text-[10px] font-bold tracking-widest text-muted-foreground/60 uppercase"
                        >O que está incluso</span
                    >
                    <div
                        class="h-px grow bg-linear-to-r from-transparent via-border to-transparent"
                    ></div>
                </div>

                <ul class="space-y-4">
                    <li
                        v-for="(feature, index) in features"
                        :key="index"
                        class="group/item flex items-start gap-3"
                    >
                        <div
                            :class="[
                                'mt-1 rounded-full p-1 transition-colors',
                                feature.included
                                    ? 'bg-primary/10 text-primary group-hover/item:bg-primary group-hover/item:text-white'
                                    : 'text-muted-foreground/30',
                            ]"
                        >
                            <Check
                                v-if="feature.included"
                                class="h-3 w-3 stroke-[3px]"
                            />
                            <span
                                v-else
                                class="block h-px w-3 bg-current"
                            ></span>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <span
                                :class="[
                                    'text-[15px]',
                                    feature.included
                                        ? 'font-medium text-foreground/90'
                                        : 'text-muted-foreground/50 line-through decoration-1',
                                ]"
                            >
                                {{ feature.text }}
                            </span>

                            <TooltipProvider v-if="feature.info">
                                <Tooltip>
                                    <TooltipTrigger>
                                        <Info
                                            class="h-3.5 w-3.5 text-muted-foreground/40 transition-colors hover:text-primary"
                                        />
                                    </TooltipTrigger>
                                    <TooltipContent
                                        class="border-primary/20 bg-popover"
                                        >{{ feature.info }}</TooltipContent
                                    >
                                </Tooltip>
                            </TooltipProvider>
                        </div>
                    </li>
                </ul>
            </CardContent>

            <CardFooter class="p-8">
                <Button
                    size="lg"
                    class="group/btn relative h-14 w-full overflow-hidden text-lg shadow-xl shadow-primary/20 transition-all duration-300 hover:scale-[1.02]"
                    as-child
                >
                    <a :href="ctaHref">
                        <span class="relative z-10 flex items-center gap-2">
                            {{ cta }}
                            <span
                                class="transition-transform group-hover/btn:translate-x-1"
                                >→</span
                            >
                        </span>
                        <div
                            class="absolute inset-0 bg-linear-to-r from-primary via-white/20 to-primary opacity-0 transition-opacity duration-500 group-hover/btn:opacity-100"
                        ></div>
                    </a>
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>
