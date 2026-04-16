<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { CheckCircle, Clock, Copy } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const props = defineProps<{
    pixCode: string | null;
    qrCodeBase64: string | null;
    expiresAt: string | null;
    status: string;
    amount: number;
}>();

const copied = ref(false);

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
</script>

<template>
    <Head title="Pagar com Pix" />

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

                <!-- Status / expiry -->
                <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-900 dark:bg-yellow-950/30">
                    <div class="flex items-center gap-2 text-sm font-medium text-yellow-800 dark:text-yellow-400">
                        <Clock class="h-4 w-4 shrink-0" />
                        <span>Aguardando pagamento...</span>
                    </div>
                    <p v-if="expiresAtFormatted" class="mt-1 text-xs text-yellow-700 dark:text-yellow-500">
                        QR Code válido até {{ expiresAtFormatted }}
                    </p>
                </div>

                <p class="text-center text-xs text-muted-foreground">
                    Após o pagamento, sua assinatura será ativada automaticamente.
                    <br />Você pode fechar esta página.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
