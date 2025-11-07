<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/components/ui/navigation-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import UserMenuContent from '@/components/UserMenuContent.vue';
import { getInitials } from '@/composables/useInitials';
import { urlIsActive } from '@/lib/utils';
import {
    dashboard,
    home,
    login as loginRoute,
    logout as logoutRoute,
    register as registerRoute,
} from '@/routes';
import { create as createProject, index as projectsIndex } from '@/routes/projects';
import type { AppPageProps, BreadcrumbItem, NavItem } from '@/types';
import { InertiaLinkProps, Link, usePage } from '@inertiajs/vue3';
import {
    HeartHandshake,
    Home as HomeIcon,
    LayoutGrid,
    Menu,
    PlusCircle,
} from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const page = usePage<AppPageProps>();
const authUser = computed(() => page.props.auth?.user ?? null);
const navigation = computed(() => page.props.navigation ?? {});

const isCurrentRoute = computed(
    () => (url: NonNullable<InertiaLinkProps['href']>) =>
        urlIsActive(url, page.url),
);

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
</script>

<template>
    <div class="border-b border-sidebar-border/70 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 text-white">
        <div class="mx-auto flex h-16 w-full items-center px-4 md:max-w-7xl">
            <!-- Mobile Menu -->
            <div class="lg:hidden">
                <Sheet>
                    <SheetTrigger :as-child="true">
                        <Button
                            variant="ghost"
                            size="icon"
                            class="mr-2 h-9 w-9 text-white hover:bg-white/10"
                        >
                            <Menu class="h-5 w-5" />
                        </Button>
                    </SheetTrigger>
                    <SheetContent side="left" class="w-[300px] border-r border-white/10 bg-slate-950 text-white">
                        <SheetTitle class="sr-only">{{ navigation.menu ?? 'Main menu' }}</SheetTitle>
                        <SheetHeader class="flex items-center justify-start gap-3 text-left">
                            <AppLogoIcon class="size-6 fill-current text-white" />
                            <span class="text-sm font-medium">{{ page.props.name }}</span>
                        </SheetHeader>
                        <div class="flex h-full flex-1 flex-col justify-between space-y-6 py-6">
                            <nav class="-mx-3 space-y-1">
                                <Link
                                    v-for="item in mainNavItems"
                                    :key="item.title"
                                    :href="item.href"
                                    class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium text-white/80 hover:bg-white/10 hover:text-white"
                                    :class="{ 'bg-white/10 text-white': isCurrentRoute(item.href) }"
                                >
                                    <component
                                        v-if="item.icon"
                                        :is="item.icon"
                                        class="h-5 w-5"
                                    />
                                    {{ item.title }}
                                </Link>
                            </nav>
                            <div class="space-y-3">
                                <template v-if="authUser">
                                    <Button class="w-full bg-emerald-400 text-slate-900 hover:bg-emerald-300" as-child>
                                        <Link :href="dashboard()">{{ navigation.dashboard ?? 'Dashboard' }}</Link>
                                    </Button>
                                    <Button variant="ghost" class="w-full border border-white/10 text-white hover:bg-white/10" as-child>
                                        <Link :href="logoutRoute()" method="post" as="button">
                                            {{ navigation.logout ?? 'Sign out' }}
                                        </Link>
                                    </Button>
                                </template>
                                <template v-else>
                                    <Button class="w-full bg-emerald-400 text-slate-900 hover:bg-emerald-300" as-child>
                                        <Link :href="registerRoute()">{{ navigation.register ?? 'Create account' }}</Link>
                                    </Button>
                                    <Button variant="ghost" class="w-full border border-white/10 text-white hover:bg-white/10" as-child>
                                        <Link :href="loginRoute()">{{ navigation.login ?? 'Sign in' }}</Link>
                                    </Button>
                                </template>
                            </div>
                        </div>
                    </SheetContent>
                </Sheet>
            </div>

            <Link :href="home()" class="flex items-center gap-x-2">
                <AppLogo />
            </Link>

            <!-- Desktop Menu -->
            <div class="hidden h-full flex-1 lg:flex">
                <NavigationMenu class="ml-10 flex h-full items-stretch">
                    <NavigationMenuList class="flex h-full items-stretch space-x-2">
                        <NavigationMenuItem
                            v-for="(item, index) in mainNavItems"
                            :key="index"
                            class="relative flex h-full items-center"
                        >
                            <Link
                                :class="[
                                    navigationMenuTriggerStyle(),
                                    isCurrentRoute(item.href)
                                        ? 'bg-white/10 text-white'
                                        : 'text-white/70 hover:bg-white/10 hover:text-white',
                                    'h-9 cursor-pointer px-3',
                                ]"
                                :href="item.href"
                            >
                                <component
                                    v-if="item.icon"
                                    :is="item.icon"
                                    class="mr-2 h-4 w-4"
                                />
                                {{ item.title }}
                            </Link>
                            <div
                                v-if="isCurrentRoute(item.href)"
                                class="absolute bottom-0 left-0 h-0.5 w-full translate-y-px bg-emerald-400"
                            ></div>
                        </NavigationMenuItem>
                    </NavigationMenuList>
                </NavigationMenu>
            </div>

            <div class="ml-auto flex items-center gap-2">
                <template v-if="authUser">
                    <DropdownMenu>
                        <DropdownMenuTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative size-10 w-auto rounded-full p-1 text-white hover:bg-white/10 focus-visible:ring-2 focus-visible:ring-emerald-400"
                            >
                                <Avatar class="size-8 overflow-hidden rounded-full">
                                    <AvatarImage
                                        v-if="authUser.avatar"
                                        :src="authUser.avatar"
                                        :alt="authUser.name"
                                    />
                                    <AvatarFallback class="rounded-full bg-white/20 text-sm font-semibold text-white">
                                        {{ getInitials(authUser.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-60">
                            <UserMenuContent :user="authUser" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </template>
                <template v-else>
                    <Button variant="ghost" class="hidden text-white hover:bg-white/10 lg:inline-flex" as-child>
                        <Link :href="loginRoute()">{{ navigation.login ?? 'Sign in' }}</Link>
                    </Button>
                    <Button class="hidden bg-emerald-400 text-slate-900 hover:bg-emerald-300 lg:inline-flex" as-child>
                        <Link :href="registerRoute()">{{ navigation.register ?? 'Create account' }}</Link>
                    </Button>
                </template>
            </div>
        </div>

        <div
            v-if="props.breadcrumbs.length > 1"
            class="flex w-full border-t border-white/10 bg-slate-950/40"
        >
            <div class="mx-auto flex h-12 w-full items-center justify-start px-4 text-xs uppercase tracking-wide text-white/60 md:max-w-7xl">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
