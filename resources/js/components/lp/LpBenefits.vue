<script setup lang="ts">
import { Clock, TrendingUp, Search, Award, Zap } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import LpFeatureCard from './LpFeatureCard.vue';
import LpSection from './LpSection.vue';

const { t } = useI18n();
</script>

<template>
    <LpSection background="muted">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl mb-4">
                {{ t('lp.benefits.h2') }}
            </h2>
            <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                {{ t('lp.benefits.subtitle') }}
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <LpFeatureCard
                :icon="Clock"
                :title="t('lp.benefits.card1.title')"
                :description="t('lp.benefits.card1.desc')"
            />
            <LpFeatureCard
                :icon="TrendingUp"
                :title="t('lp.benefits.card2.title')"
                :description="t('lp.benefits.card2.desc')"
            />
            <LpFeatureCard
                :icon="Search"
                :title="t('lp.benefits.card3.title')"
                :description="t('lp.benefits.card3.desc')"
            />
            <LpFeatureCard
                :icon="Award"
                :title="t('lp.benefits.card4.title')"
                :description="t('lp.benefits.card4.desc')"
            />
            <LpFeatureCard
                :icon="Zap"
                :title="t('lp.benefits.card5.title')"
                :description="t('lp.benefits.card5.desc')"
            />
            <LpFeatureCard
                :icon="TrendingUp"
                :title="t('lp.benefits.card6.title')"
                :description="t('lp.benefits.card6.desc')"
            />
        </div>
    </LpSection>
</template>
