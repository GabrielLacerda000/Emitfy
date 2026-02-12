<script setup lang="ts">
import { CheckCircle2, ArrowRight } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { register } from '@/routes';
import LpSection from './LpSection.vue';
</script>

<template>
    <LpSection
        spacing="spacious"
        class="relative overflow-hidden pt-20 pb-32 lg:pt-32"
    >
        <div
            class="absolute top-0 left-1/2 -z-10 h-full w-full -translate-x-1/2"
        >
            <div
                class="absolute top-[-10%] left-[-10%] h-[40%] w-[40%] rounded-full bg-primary/10 blur-[120px]"
            />
            <div
                class="absolute right-[-5%] bottom-[10%] h-[30%] w-[30%] rounded-full bg-indigo-500/10 blur-[100px]"
            />
        </div>

        <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-12">
            <div class="space-y-8 text-center lg:col-span-5 lg:text-left">
                <!-- TODO: Descomentar quando houver clientes -->
                <!-- <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-bold uppercase tracking-wider mx-auto lg:mx-0">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    Trusted by 2,000+ Freelancers
                </div> -->

                <h1
                    class="text-5xl leading-[1.2] font-extrabold tracking-tight sm:text-6xl lg:text-7xl"
                >
                    Invoice Like <br />
                    <span
                        class="bg-linear-to-r from-primary to-indigo-600 bg-clip-text pr-2 text-transparent italic"
                    >
                        a Pro </span
                    >, Get Paid
                    <span
                        class="underline decoration-primary/30 underline-offset-8"
                    >
                        Faster.
                    </span>
                </h1>

                <p
                    class="mx-auto max-w-xl text-xl leading-relaxed text-muted-foreground lg:mx-0"
                >
                    Stop wrestling with spreadsheets. Emitte streamlines your
                    billing so you can get back to what you do best.
                </p>

                <div
                    class="flex flex-col justify-center gap-4 sm:flex-row lg:justify-start"
                >
                    <Button
                        size="lg"
                        class="group h-14 px-8 text-lg shadow-lg shadow-primary/20"
                        as-child
                    >
                        <a :href="register().url">
                            Start Invoicing
                            <ArrowRight
                                class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1"
                            />
                        </a>
                    </Button>
                    <Button
                        variant="ghost"
                        size="lg"
                        class="h-14 px-8 text-lg"
                        as-child
                    >
                        <a href="#demo">Watch Demo</a>
                    </Button>
                </div>

                <div
                    class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 pt-4 lg:justify-start"
                >
                    <div
                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                    >
                        <CheckCircle2 class="h-4 w-4 text-primary" /> Easy to
                        use
                    </div>
                    <div
                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                    >
                        <CheckCircle2 class="h-4 w-4 text-primary" /> Setup in 2
                        mins
                    </div>
                </div>
            </div>

            <div class="group relative lg:col-span-7">
                <div
                    class="absolute -inset-1 rounded-2xl bg-linear-to-r from-primary/20 to-indigo-500/20 opacity-30 blur transition duration-1000 group-hover:opacity-50"
                ></div>

                <div
                    class="relative aspect-16/11 w-full overflow-hidden rounded-2xl border border-border/50 bg-card shadow-[0_0_50px_-12px_rgba(0,0,0,0.3)] dark:shadow-[0_0_50px_-12px_rgba(0,0,0,0.7)]"
                >
                    <div
                        class="flex h-10 items-center gap-1.5 border-b border-border/40 bg-muted/50 px-4"
                    >
                        <div class="h-3 w-3 rounded-full bg-red-400/20"></div>
                        <div class="h-3 w-3 rounded-full bg-amber-400/20"></div>
                        <div
                            class="h-3 w-3 rounded-full bg-emerald-400/20"
                        ></div>
                        <div
                            class="ml-4 h-5 w-full max-w-xs rounded-md border border-border/20 bg-background/50"
                        ></div>
                    </div>

                    <div class="relative w-full flex-1 bg-muted/20">
                        <img
                            src="/images/dash.png"
                            alt="Emitte dashboard preview"
                            class="h-full w-full object-contain transition-transform duration-700 group-hover:scale-[1.02]"
                        />
                        <div
                            class="animate-bounce-slow absolute bottom-8 left-8 hidden rounded-xl border border-border bg-background/90 p-4 shadow-xl backdrop-blur sm:block"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="rounded-lg bg-emerald-500/10 p-2 text-xl font-bold text-emerald-600"
                                >
                                    +$2,400
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    Payment received <br />
                                    <span class="font-bold text-foreground"
                                        >Just now</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </LpSection>
</template>

<style scoped>
.animate-bounce-slow {
    animation: bounce 3s infinite;
}

@keyframes bounce {
    0%,
    100% {
        transform: translateY(-5%);
        animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
    }
    50% {
        transform: translateY(0);
        animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
    }
}
</style>
