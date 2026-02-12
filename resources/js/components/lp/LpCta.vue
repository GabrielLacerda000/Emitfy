<script setup lang="ts">
import { ArrowRight, Star } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { register } from '@/routes';
import LpSection from './LpSection.vue';
</script>

<template>
    <LpSection spacing="compact" class="relative overflow-hidden py-20">
        <div class="absolute inset-0 bg-primary rounded-[3rem] mx-4 lg:mx-8">
            <div class="absolute inset-0 opacity-10 mask-[linear-gradient(to_bottom,white,transparent)]" 
                 style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 30px 30px;">
            </div>
            
            <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-96 h-96 bg-white/20 blur-[100px] rounded-full"></div>
            <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-96 h-96 bg-indigo-500/30 blur-[100px] rounded-full"></div>
        </div>

        <div class="relative z-10 text-center max-w-4xl mx-auto px-6 py-12">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-sm font-medium mb-8 backdrop-blur-md">
                <Star class="h-4 w-4 fill-yellow-400 text-yellow-400" />
                <span>The #1 choice for freelancers in 2026</span>
            </div>

            <h2 class="text-4xl font-black tracking-tight sm:text-5xl lg:text-6xl text-white mb-6 leading-[1.1]">
                Stop chasing, <br class="hidden sm:block" />
                <span class="text-indigo-200 italic">start growing.</span>
            </h2>
            
            <p class="text-xl mb-10 text-indigo-50/90 max-w-2xl mx-auto leading-relaxed">
                Join 2,000+ professionals who save an average of 40 hours a year on billing. 
                Your first 10 invoices are on us.
            </p>

            <div class="flex flex-col items-center gap-6">
                <Button
                    size="lg"
                    variant="secondary"
                    class="group text-xl px-10 py-8 h-auto bg-white text-primary hover:bg-indigo-50 shadow-2xl hover:shadow-white/20 transition-all duration-300 hover:-translate-y-1"
                    as-child
                >
                    <a :href="register().url">
                        Create My Free Account
                        <ArrowRight class="ml-2 h-6 w-6 transition-transform group-hover:translate-x-2" />
                    </a>
                </Button>

                <!-- <div class="flex flex-wrap justify-center gap-x-8 gap-y-4">
                    <div class="flex items-center gap-2 text-indigo-100/80 text-sm">
                        <ShieldCheck class="h-4 w-4" />
                        No credit card required
                    </div>
                    <div class="flex items-center gap-2 text-indigo-100/80 text-sm">
                        <Zap class="h-4 w-4" />
                        Setup in 60 seconds
                    </div>
                </div> -->
            </div>
        </div>
    </LpSection>
</template>

<style scoped>
/* Efeito de levitação suave para o card inteiro se desejar */
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}
</style>