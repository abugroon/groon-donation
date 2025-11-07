<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
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
import { dashboard, home } from '@/routes';
import { create as createProject, index as projectsIndex } from '@/routes/projects';
import type { AppPageProps, NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    HeartHandshake,
    Home as HomeIcon,
    LayoutGrid,
    LifeBuoy,
    Mail,
    PlusCircle,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage<AppPageProps>();
const authUser = computed(() => page.props.auth?.user ?? null);
const navigation = computed(() => page.props.navigation ?? {});

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        {
            title: navigation.value.home ?? 'Home',
            href: home(),
            icon: HomeIcon,
        },
        {
            title: navigation.value.projects ?? 'Projects',
            href: projectsIndex(),
            icon: HeartHandshake,
        },
    ];

    if (authUser.value) {
        items.push({
            title: navigation.value.dashboard ?? 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        });
    }

    if (authUser.value?.role === 'admin') {
        items.push({
            title: navigation.value.manage_projects ?? 'Manage projects',
            href: createProject(),
            icon: PlusCircle,
        });
    }

    return items;
});

const footerNavItems = computed<NavItem[]>(() => [
    {
        title: navigation.value.help_center ?? 'Help center',
        href: 'https://charityhub.example/help',
        icon: LifeBuoy,
    },
    {
        title: navigation.value.contact ?? 'Contact support',
        href: 'mailto:team@charityhub.test',
        icon: Mail,
    },
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="home()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain
                :items="mainNavItems"
                :label="navigation.menu ?? 'Menu'"
            />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
