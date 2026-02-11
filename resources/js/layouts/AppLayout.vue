<script setup lang="ts">
 import 'vue-sonner/style.css'
 import { onMounted } from 'vue'
 import { Toaster } from '@/components/ui/sonner'
 import { useFlashToast } from '@/composables/useFlashToast'
 import AppLayout from '@/layouts/app/AppSidebarLayout.vue'
 import type { BreadcrumbItem } from '@/types'

 type Props = {
     breadcrumbs?: BreadcrumbItem[]
 }

 withDefaults(defineProps<Props>(), {
     breadcrumbs: () => [],
 })

 // Automatically show toasts for flash messages after mount
 onMounted(() => {
     useFlashToast()
 })
 </script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
    <Toaster rich-colors />
</template>
