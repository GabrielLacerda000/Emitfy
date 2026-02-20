<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import 'vue-sonner/style.css';
import { Toaster } from '@/components/ui/sonner';
type Props = {
    title?: string;
};

defineProps<Props>();
</script>

<template>
    <div class="min-h-screen bg-muted/30">
        <Head :title="title" />

        <!-- Simple Header -->
        <header class="border-b bg-background">
            <div class="mx-auto max-w-5xl px-4 py-4">
                <h1 class="text-xl font-semibold">Emitfy</h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="mx-auto max-w-5xl px-4 py-8">
            <div class="rounded-lg border bg-card p-6 shadow-sm">
                <slot />
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-auto border-t bg-background py-6">
            <div
                class="mx-auto max-w-5xl px-4 text-center text-sm text-muted-foreground"
            >
                <p>Invoice powered by Emitfy</p>
            </div>
        </footer>
    </div>
    <Toaster rich-colors />
</template>
