<script setup lang="ts">
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { register } from '@/routes';
import LpPricingCard from './LpPricingCard.vue';
import LpSection from './LpSection.vue';

const props = defineProps<{
    planId: number | null;
}>();

const { t } = useI18n();
const page = usePage();

const modalOpen = ref(false);

const form = useForm({
    plan_id: props.planId,
    cpf: '',
});

const features = computed(() => [
    { text: t('lp.pricing.features.unlimitedClients'), included: true },
    { text: t('lp.pricing.features.unlimitedInvoices'), included: true },
    { text: t('lp.pricing.features.recurringInvoices'), included: true },
    { text: t('lp.pricing.features.automaticReminders'), included: true },
    { text: t('lp.pricing.features.fullDashboard'), included: true },
    { text: t('lp.pricing.features.emailSupport'), included: true },
]);

function handleCtaClick(): void {
    if (page.props.auth.user) {
        modalOpen.value = true;
    } else {
        router.visit(register().url);
    }
}

function submitCheckout(): void {
    form.post('/checkout/pix', {
        onSuccess: () => {
            modalOpen.value = false;
        },
    });
}

function formatCpf(value: string): string {
    const digits = value.replace(/\D/g, '').slice(0, 11);
    return digits
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d)/, '$1.$2')
        .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
}

function onCpfInput(event: Event): void {
    const input = event.target as HTMLInputElement;
    form.cpf = formatCpf(input.value);
}
</script>

<template>
    <LpSection background="muted" id="pricing" class="py-24">
        <div class="mb-20 text-center">
            <h2 class="mb-4 text-4xl font-extrabold tracking-tight sm:text-5xl">
                {{ t('lp.pricing.h2') }}
            </h2>
            <p class="mx-auto max-w-2xl text-lg text-muted-foreground">
                {{ t('lp.pricing.subtitle') }}
            </p>
        </div>

        <div class="mx-auto max-w-xl px-4">
            <LpPricingCard
                :name="t('lp.pricing.plan.name')"
                :price="t('lp.pricing.plan.price')"
                :description="t('lp.pricing.plan.desc')"
                :features="features"
                :cta="t('lp.pricing.plan.cta')"
                @cta-click="handleCtaClick"
            />
        </div>
    </LpSection>

    <Dialog v-model:open="modalOpen">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ t('lp.pricing.plan.name') }}</DialogTitle>
                <DialogDescription>
                    Informe seu CPF para gerar o QR Code Pix e ativar sua assinatura.
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitCheckout" class="space-y-4 py-2">
                <div class="space-y-2">
                    <Label for="cpf">CPF</Label>
                    <Input
                        id="cpf"
                        :value="form.cpf"
                        @input="onCpfInput"
                        placeholder="000.000.000-00"
                        maxlength="14"
                        autocomplete="off"
                        :disabled="form.processing"
                    />
                    <p v-if="form.errors.cpf" class="text-sm text-destructive">
                        {{ form.errors.cpf }}
                    </p>
                </div>

                <DialogFooter>
                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="form.processing || form.cpf.replace(/\D/g, '').length < 11"
                    >
                        {{ form.processing ? 'Gerando QR Code...' : 'Gerar QR Code Pix' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
