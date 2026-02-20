<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import LpFaqItem from './LpFaqItem.vue';
import LpSection from './LpSection.vue';

const { t } = useI18n();
</script>

<template>
    <LpSection id="faq">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl mb-4">
                {{ t('lp.faq.h2') }}
            </h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                {{ t('lp.faq.subtitle') }}
            </p>
        </div>

        <div class="max-w-3xl mx-auto">
            <LpFaqItem :question="t('lp.faq.q1')" :answer="t('lp.faq.a1')" />
            <LpFaqItem :question="t('lp.faq.q2')" :answer="t('lp.faq.a2')" />
            <LpFaqItem :question="t('lp.faq.q3')" :answer="t('lp.faq.a3')" />
            <LpFaqItem :question="t('lp.faq.q4')" :answer="t('lp.faq.a4')" />
            <LpFaqItem :question="t('lp.faq.q5')" :answer="t('lp.faq.a5')" />
        </div>
    </LpSection>
</template>
