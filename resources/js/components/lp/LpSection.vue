<script setup lang="ts">
withDefaults(
    defineProps<{
        background?: 'default' | 'muted' | 'primary';
        spacing?: 'default' | 'compact' | 'spacious';
        containerSize?: 'default' | 'narrow' | 'wide';
    }>(),
    {
        background: 'default',
        spacing: 'default',
        containerSize: 'default',
    },
);

const backgroundClasses = {
    default: 'bg-background',
    muted: 'bg-muted',
    primary: 'bg-primary text-primary-foreground',
};

const spacingClasses = {
    compact: 'py-12 md:py-16 lg:py-20',
    default: 'py-16 md:py-24 lg:py-32',
    spacious: 'py-20 md:py-32 lg:py-40',
};

const containerClasses = {
    narrow: 'max-w-4xl',
    default: 'max-w-7xl',
    wide: 'max-w-[90rem]',
};
</script>

<template>
    <section :class="[backgroundClasses[background], spacingClasses[spacing]]">
        <div
            :class="[
                'mx-auto px-4 sm:px-6 lg:px-8',
                containerClasses[containerSize],
            ]"
        >
            <slot />
        </div>
    </section>
</template>
