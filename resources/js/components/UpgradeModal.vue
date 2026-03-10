<script setup lang="ts">
import { Crown, Send, FileText, ArrowLeftRight } from 'lucide-vue-next';
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

const { t } = useI18n();

const open = defineModel<boolean>('open');
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="rounded-2xl sm:max-w-md">
            <DialogHeader>
                <div
                    class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10"
                >
                    <Crown class="h-8 w-8 text-primary" />
                </div>
                <DialogTitle class="text-center text-xl font-black">
                    {{ t('upgradeModal.title') }}
                </DialogTitle>
                <DialogDescription class="text-center text-sm">
                    {{ t('upgradeModal.description') }}
                </DialogDescription>
            </DialogHeader>

            <div class="my-2 space-y-2 rounded-xl border border-border/50 bg-muted/30 p-4">
                <div class="flex items-center gap-3 text-sm">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                        <Send class="h-3.5 w-3.5 text-primary" />
                    </div>
                    <span class="font-medium">{{ t('upgradeModal.features.send') }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                        <FileText class="h-3.5 w-3.5 text-primary" />
                    </div>
                    <span class="font-medium">{{ t('upgradeModal.features.pdf') }}</span>
                </div>
                <div class="flex items-center gap-3 text-sm">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                        <ArrowLeftRight class="h-3.5 w-3.5 text-primary" />
                    </div>
                    <span class="font-medium">{{ t('upgradeModal.features.status') }}</span>
                </div>
            </div>

            <p class="text-center text-xs font-medium text-muted-foreground italic">
                {{ t('upgradeModal.comingSoon') }}
            </p>

            <DialogFooter>
                <Button
                    class="w-full rounded-xl font-bold"
                    @click="open = false"
                >
                    {{ t('upgradeModal.close') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
