<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { type NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup class="px-4 py-2">
        <SidebarGroupLabel
            class="mb-4 px-2 text-[10px] font-black tracking-[0.2em] text-muted-foreground/50 uppercase"
        >
            Platform
        </SidebarGroupLabel>

        <SidebarMenu class="gap-1.5">
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    class="group relative h-10 overflow-hidden rounded-xl transition-all duration-300"
                    :class="[
                        isCurrentUrl(item.href)
                            ? 'bg-primary/10 text-primary'
                            : 'text-muted-foreground hover:bg-muted/60 hover:text-foreground',
                    ]"
                >
                    <Link
                        :href="item.href"
                        class="flex items-center gap-3 px-3"
                    >
                        <div
                            v-if="isCurrentUrl(item.href)"
                            class="absolute top-1/2 left-0 h-5 w-1 -translate-y-1/2 rounded-r-full bg-primary"
                        />

                        <component
                            :is="item.icon"
                            :class="[
                                'h-4 w-4 shrink-0 transition-transform duration-300 group-hover:scale-110',
                                isCurrentUrl(item.href)
                                    ? 'text-primary'
                                    : 'text-muted-foreground/70 group-hover:text-foreground',
                            ]"
                        />

                        <span>
                            {{ item.title }}
                        </span>

                        <div
                            v-if="isCurrentUrl(item.href)"
                            class="pointer-events-none absolute inset-0 bg-linear-to-r from-primary/5 to-transparent"
                        />
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
