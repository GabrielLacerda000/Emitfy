<script setup lang="ts">
import { FileChartColumn } from 'lucide-vue-next';
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-primary text-sidebar-primary-foreground"
    >
        <FileChartColumn class="size-5 text-accent" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold"
            >Emitte</span
        >
    </div>
</template>
