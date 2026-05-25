<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { CheckCircle, Clock, Copy, XCircle } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
    paymentId: number;
    pixCode: string | null;
    qrCodeBase64: string | null;
    expiresAt: string | null;
    status: string;
    amount: number;
}>();

const copied = ref(false);
const currentStatus = ref(props.status);
const intervalId = ref<ReturnType<typeof setInterval> | null>(null);
const timeoutId = ref<ReturnType<typeof setTimeout> | null>(null);

const formattedAmount = computed(() =>
    new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(props.amount),
);

const expiresAtFormatted = computed(() => {
    if (!props.expiresAt) return null;
    return new Date(props.expiresAt).toLocaleString('pt-BR');
});

function copyPixCode(): void {
    if (!props.pixCode) return;
    navigator.clipboard.writeText(props.pixCode).then(() => {
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    });
}

function stopPolling(): void {
    if (intervalId.value) {
        clearInterval(intervalId.value);
        intervalId.value = null;
    }
    if (timeoutId.value) {
        clearTimeout(timeoutId.value);
        timeoutId.value = null;
    }
}

async function pollStatus(): Promise<void> {
    try {
        const res = await fetch(`/checkout/pix/${props.paymentId}/status`);
        if (!res.ok) return;
        const { status } = await res.json();
        currentStatus.value = status;

        if (status === 'paid' || status === 'expired' || status === 'failed') {
            stopPolling();
            if (status === 'paid') {
                setTimeout(() => router.visit('/dashboard'), 3000);
            }
        }
    } catch {
        // network error — retry on next interval
    }
}

onMounted(() => {
    if (currentStatus.value === 'pending') {
        intervalId.value = setInterval(pollStatus, 3000);
        timeoutId.value = setTimeout(stopPolling, 15 * 60 * 1000);
    }
});

onUnmounted(stopPolling);
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-muted/40 p-4">
        <Card class="w-full max-w-md shadow-xl">
            <CardHeader class="text-center">
                <CardTitle class="text-2xl font-bold">Pagar com Pix</CardTitle>
                <p class="text-muted-foreground">
                    Valor: <span class="font-semibold text-foreground">{{ formattedAmount }}</span>
                </p>
            </CardHeader>

            <CardContent class="space-y-6">
                <!-- QR Code -->
                <div class="flex justify-center">
                    <div
                        v-if="qrCodeBase64"
                        class="rounded-2xl border-2 border-primary/20 bg-white p-4 shadow-inner"
                    >
                        <img
                            :src="`data:image/png;base64,${qrCodeBase64}`"
                            alt="QR Code Pix"
                            class="h-52 w-52"
                        />
                    </div>
                    <div
                        v-else
                        class="flex h-60 w-60 items-center justify-center rounded-2xl border-2 border-dashed border-muted-foreground/30"
                    >
                        <p class="text-sm text-muted-foreground">QR Code indisponível</p>
                    </div>
                </div>

                <!-- Pix copy-paste -->
                <div v-if="pixCode" class="space-y-2">
                    <p class="text-center text-sm font-medium text-muted-foreground">
                        Ou copie o código Pix:
                    </p>
                    <div class="flex gap-2">
                        <div
                            class="min-w-0 flex-1 truncate rounded-lg border bg-muted px-3 py-2 font-mono text-xs"
                        >
                            {{ pixCode }}
                        </div>
                        <Button
                            variant="outline"
                            size="icon"
                            class="shrink-0"
                            @click="copyPixCode"
                        >
                            <CheckCircle v-if="copied" class="h-4 w-4 text-green-500" />
                            <Copy v-else class="h-4 w-4" />
                        </Button>
                    </div>
                </div>

                <!-- Status banner -->
                <div
                    v-if="currentStatus === 'pending'"
                    class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900 dark:bg-yellow-950/30"
                >
                    <div class="flex items-center gap-2 text-sm font-medium text-yellow-800 dark:text-yellow-400">
                        <Clock class="h-4 w-4 shrink-0 animate-pulse" />
                        <span>Aguardando pagamento...</span>
                    </div>
                    <p v-if="expiresAtFormatted" class="mt-1 text-xs text-yellow-700 dark:text-yellow-500">
                        QR Code válido até {{ expiresAtFormatted }}
                    </p>
                </div>

                <div
                    v-else-if="currentStatus === 'paid'"
                    class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900 dark:bg-green-950/30"
                >
                    <div class="flex items-center gap-2 text-sm font-medium text-green-800 dark:text-green-400">
                        <CheckCircle class="h-4 w-4 shrink-0" />
                        <span>Pagamento confirmado! Redirecionando...</span>
                    </div>
                </div>

                <div
                    v-else-if="currentStatus === 'expired' || currentStatus === 'failed'"
                    class="rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900 dark:bg-red-950/30"
                >
                    <div class="flex items-center gap-2 text-sm font-medium text-red-800 dark:text-red-400">
                        <XCircle class="h-4 w-4 shrink-0" />
                        <span>{{ currentStatus === 'expired' ? 'QR Code expirado.' : 'Pagamento não concluído.' }}</span>
                    </div>
                </div>

                <p v-if="currentStatus === 'pending'" class="text-center text-xs text-muted-foreground">
                    Após o pagamento, sua assinatura será ativada automaticamente.
                    <br />Você pode fechar esta página.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
