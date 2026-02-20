<script setup lang="ts">
import { Users, FileText, Send, DollarSign } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import LpSection from './LpSection.vue';
import LpStepCard from './LpStepCard.vue';

const { t } = useI18n();
</script>

<template>
    <LpSection id="how-it-works" class="relative overflow-hidden py-24 lg:py-32">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-primary/5 blur-[120px] rounded-full -z-10" />

        <div class="text-center mb-20">
            <h2 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl mb-6 bg-linear-to-b from-foreground to-foreground/70 bg-clip-text text-transparent">
                {{ t('lp.howItWorks.h2') }}
            </h2>
            <p class="text-xl text-muted-foreground max-w-3xl mx-auto leading-relaxed">
                {{ t('lp.howItWorks.subtitle') }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-16 px-4">
            <LpStepCard
                :step="1"
                :icon="Users"
                :title="t('lp.howItWorks.step1.title')"
                :description="t('lp.howItWorks.step1.desc')"
            />
            <LpStepCard
                :step="2"
                :icon="FileText"
                :title="t('lp.howItWorks.step2.title')"
                :description="t('lp.howItWorks.step2.desc')"
            />
            <LpStepCard
                :step="3"
                :icon="Send"
                :title="t('lp.howItWorks.step3.title')"
                :description="t('lp.howItWorks.step3.desc')"
            />
            <LpStepCard
                :step="4"
                :icon="DollarSign"
                :title="t('lp.howItWorks.step4.title')"
                :description="t('lp.howItWorks.step4.desc')"
                :is-last="true"
            />
        </div>
    </LpSection>
</template>
