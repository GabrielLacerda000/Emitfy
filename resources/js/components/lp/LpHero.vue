<script setup lang="ts">
import { CheckCircle2, ArrowRight } from 'lucide-vue-next';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { Button } from '@/components/ui/button';
import { register } from '@/routes';
import LpSection from './LpSection.vue';
</script>

<template>
    <LpSection spacing="spacious" class="relative overflow-hidden pt-20 pb-32 lg:pt-32">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/10 blur-[120px] rounded-full" />
            <div class="absolute bottom-[10%] right-[-5%] w-[30%] h-[30%] bg-indigo-500/10 blur-[100px] rounded-full" />
        </div>

        <div class="grid grid-cols-1 gap-16 lg:grid-cols-12 items-center">
            
            <div class="lg:col-span-5 space-y-8 text-center lg:text-left">
                <!-- TODO: Descomentar quando houver clientes -->
                <!-- <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-bold uppercase tracking-wider mx-auto lg:mx-0">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    Trusted by 2,000+ Freelancers
                </div> -->

                <h1 class="text-5xl font-extrabold tracking-tight sm:text-6xl lg:text-7xl leading-[1.1]">
                    Invoice Like <br />
                    <span class="bg-linear-to-r from-primary to-indigo-600 bg-clip-text text-transparent italic">a Pro</span>, 
                    Get Paid <span class="underline decoration-primary/30 underline-offset-8">Faster.</span>
                </h1>

                <p class="text-xl text-muted-foreground leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Stop wrestling with spreadsheets. Emitte streamlines your billing so you can get back to what you do best.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <Button size="lg" class="h-14 px-8 text-lg shadow-lg shadow-primary/20 group" as-child>
                        <a :href="register().url">
                            Start Invoicing
                            <ArrowRight class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1" />
                        </a>
                    </Button>
                    <Button variant="ghost" size="lg" class="h-14 px-8 text-lg" as-child>
                        <a href="#demo">Watch Demo</a>
                    </Button>
                </div>

                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-x-6 gap-y-2 pt-4">
                    <div class="flex items-center gap-2 text-sm text-muted-foreground font-medium">
                        <CheckCircle2 class="h-4 w-4 text-primary" /> Easy to use
                    </div>
                    <div class="flex items-center gap-2 text-sm text-muted-foreground font-medium">
                        <CheckCircle2 class="h-4 w-4 text-primary" /> Setup in 2 mins
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 relative group">
                <div class="absolute -inset-1 bg-linear-to-r from-primary/20 to-indigo-500/20 rounded-2xl blur opacity-30 group-hover:opacity-50 transition duration-1000"></div>
                
                <div class="relative w-full aspect-16/11 rounded-2xl overflow-hidden border border-border/50 bg-card shadow-[0_0_50px_-12px_rgba(0,0,0,0.3)] dark:shadow-[0_0_50px_-12px_rgba(0,0,0,0.7)]">
                    <div class="h-10 border-b border-border/40 bg-muted/50 flex items-center px-4 gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-red-400/20"></div>
                        <div class="w-3 h-3 rounded-full bg-amber-400/20"></div>
                        <div class="w-3 h-3 rounded-full bg-emerald-400/20"></div>
                        <div class="ml-4 h-5 w-full max-w-xs bg-background/50 rounded-md border border-border/20"></div>
                    </div>

                    <div class="relative h-full w-full bg-muted/20">
                        <PlaceholderPattern />
                        <div class="absolute bottom-8 left-8 p-4 bg-background/90 backdrop-blur border border-border shadow-xl rounded-xl hidden sm:block animate-bounce-slow">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-600 font-bold text-xl">
                                    +$2,400
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    Payment received <br /> <span class="font-bold text-foreground">Just now</span>
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
    0%, 100% { transform: translateY(-5%); animation-timing-function: cubic-bezier(0.8, 0, 1, 1); }
    50% { transform: translateY(0); animation-timing-function: cubic-bezier(0, 0, 0.2, 1); }
}
</style>