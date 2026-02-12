<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Check, X } from 'lucide-vue-next';

defineProps<{
    name: string;
    price: string;
    description: string;
    features: Array<{ text: string; included: boolean }>;
    cta: string;
    ctaHref: string;
    highlighted?: boolean;
}>();
</script>

<template>
    <Card
        :class="[
            'relative h-full transition-shadow hover:shadow-lg',
            highlighted
                ? 'border-primary/50 shadow-md ring-2 ring-primary/20'
                : 'border-border/40',
        ]"
    >
        <div
            v-if="highlighted"
            class="absolute -top-4 left-1/2 -translate-x-1/2"
        >
            <span
                class="inline-block rounded-full bg-primary px-4 py-1 text-xs font-semibold text-primary-foreground"
            >
                Most Popular
            </span>
        </div>
        <CardHeader class="text-center">
            <CardTitle class="text-2xl">{{ name }}</CardTitle>
            <div class="mt-4">
                <span class="text-4xl font-bold">{{ price }}</span>
                <span
                    v-if="price !== 'Free'"
                    class="text-muted-foreground"
                    >/month</span
                >
            </div>
            <CardDescription class="mt-2">{{ description }}</CardDescription>
        </CardHeader>
        <CardContent>
            <ul class="space-y-3">
                <li
                    v-for="(feature, index) in features"
                    :key="index"
                    class="flex items-start gap-3"
                >
                    <component
                        :is="feature.included ? Check : X"
                        :class="[
                            'mt-0.5 h-5 w-5 flex-shrink-0',
                            feature.included
                                ? 'text-primary'
                                : 'text-muted-foreground/40',
                        ]"
                    />
                    <span
                        :class="[
                            'text-sm',
                            feature.included
                                ? 'text-foreground'
                                : 'text-muted-foreground/60 line-through',
                        ]"
                    >
                        {{ feature.text }}
                    </span>
                </li>
            </ul>
        </CardContent>
        <CardFooter>
            <Button
                :variant="highlighted ? 'default' : 'outline'"
                class="w-full"
                as-child
            >
                <a :href="ctaHref">{{ cta }}</a>
            </Button>
        </CardFooter>
    </Card>
</template>
