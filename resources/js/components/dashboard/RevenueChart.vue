<script setup lang="ts">
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Filler,
    Legend,
    type ChartOptions,
    type ChartData,
} from 'chart.js';
import { computed } from 'vue';
import { Line } from 'vue-chartjs';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { formatBRL } from '@/lib/utils';
import type { MonthlyRevenueData } from '@/types';

// Register Chart.js components
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Filler,
    Legend
);

interface Props {
    data: MonthlyRevenueData;
}

const props = defineProps<Props>();

// Helper to format large numbers (e.g., "R$ 5k", "R$ 1.2M")
const formatCurrencyAbbreviated = (value: number): string => {
    if (value >= 1000000) {
        return `R$ ${(value / 1000000).toFixed(1)}M`;
    }
    if (value >= 1000) {
        return `R$ ${(value / 1000).toFixed(1)}k`;
    }
    return formatBRL(value);
};

// Check if dark mode is active
const isDark = computed(() => {
    return document.documentElement.classList.contains('dark');
});

// Chart data
const chartData = computed<ChartData<'line'>>(() => {
    const primaryColor = getComputedStyle(document.documentElement)
        .getPropertyValue('--primary')
        .trim();

    return {
        labels: props.data.labels,
        datasets: [
            {
                label: 'Revenue',
                data: props.data.data,
                borderColor: `hsl(${primaryColor})`,
                backgroundColor: `hsl(${primaryColor} / 0.1)`,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: `hsl(${primaryColor})`,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            },
        ],
    };
});

// Chart options
const chartOptions = computed<ChartOptions<'line'>>(() => {
    const mutedColor = isDark.value
        ? 'hsl(240 5% 64.9%)'
        : 'hsl(240 3.8% 46.1%)';
    const gridColor = isDark.value
        ? 'hsl(240 3.7% 15.9% / 0.3)'
        : 'hsl(240 5.9% 90% / 0.5)';

    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            },
            tooltip: {
                callbacks: {
                    label: (context) => {
                        return `Revenue: ${formatBRL(context.parsed.y ?? 0)}`;
                    },
                },
                backgroundColor: isDark.value
                    ? 'hsl(240 10% 3.9%)'
                    : 'hsl(0 0% 100%)',
                titleColor: isDark.value
                    ? 'hsl(0 0% 98%)'
                    : 'hsl(240 10% 3.9%)',
                bodyColor: isDark.value
                    ? 'hsl(0 0% 98%)'
                    : 'hsl(240 10% 3.9%)',
                borderColor: gridColor,
                borderWidth: 1,
            },
        },
        scales: {
            x: {
                grid: {
                    color: gridColor,
                    display: false,
                },
                ticks: {
                    color: mutedColor,
                },
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: gridColor,
                },
                ticks: {
                    color: mutedColor,
                    callback: (value) => {
                        return formatCurrencyAbbreviated(Number(value));
                    },
                },
            },
        },
    };
});
</script>

<template>
    <Card class="shadow-sm">
        <CardHeader>
            <CardTitle>Revenue Overview</CardTitle>
            <CardDescription>Monthly revenue trends over the last 6 months</CardDescription>
        </CardHeader>
        <CardContent>
            <div class="h-[300px]">
                <Line :data="chartData" :options="chartOptions" />
            </div>
        </CardContent>
    </Card>
</template>
