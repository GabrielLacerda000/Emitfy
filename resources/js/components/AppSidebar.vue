<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, Users, FileText } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
// import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useLocale } from '@/composables/useLocale';
import { dashboard } from '@/routes';
import { index as clientsIndex } from '@/routes/clients';
import { index as invoicesIndex } from '@/routes/invoices';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const { t } = useI18n();
const { locale, setLocale } = useLocale();

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: t('nav.dashboard'),
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: t('nav.clients'),
        href: clientsIndex(),
        icon: Users,
    },
    {
        title: t('nav.invoices'),
        href: invoicesIndex(),
        icon: FileText,
    },
]);

// const footerNavItems: NavItem[] = [
//     {
//         title: 'Github Repo',
//         href: 'https://github.com/laravel/vue-starter-kit',
//         icon: Folder,
//     },
//     {
//         title: 'Documentation',
//         href: 'https://laravel.com/docs/starter-kits#vue',
//         icon: BookOpen,
//     },
// ];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <!-- Language Toggle -->
        <div class="px-4 py-2 group-data-[collapsible=icon]:hidden">
            <div
                class="flex items-center gap-1 rounded-xl border border-border/40 bg-muted/40 p-1"
            >
                <button
                    @click="setLocale('en')"
                    :class="[
                        'flex-1 rounded-lg py-1.5 text-xs font-bold transition-all',
                        locale === 'en'
                            ? 'bg-background text-primary shadow-sm'
                            : 'text-muted-foreground hover:text-foreground',
                    ]"
                >
                    EN
                </button>
                <button
                    @click="setLocale('pt-BR')"
                    :class="[
                        'flex-1 rounded-lg py-1.5 text-xs font-bold transition-all',
                        locale === 'pt-BR'
                            ? 'bg-background text-primary shadow-sm'
                            : 'text-muted-foreground hover:text-foreground',
                    ]"
                >
                    PT
                </button>
            </div>
        </div>

        <SidebarFooter>
            <!-- <NavFooter :items="footerNavItems" /> -->
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
