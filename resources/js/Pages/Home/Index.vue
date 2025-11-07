<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import type { AppPageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    dashboard,
    login as loginRoute,
    logout as logoutRoute,
    register as registerRoute,
} from '@/routes';
import { index as projectsIndex, show as showProject } from '@/routes/projects';
import { computed } from 'vue';

interface Stats {
    projects_total: number;
    projects_completed: number;
    donations_total: number;
    donations_amount: number;
}

interface Project {
    id: number;
    name: string;
    description: string;
    progress: number;
    target_amount: number;
    collected_amount: number;
    status: 'open' | 'in_progress' | 'completed';
    image_url?: string | null;
}

interface Feature {
    title: string;
    description: string;
}

interface Translations {
    hero_title: string;
    hero_subtitle: string;
    primary_cta: string;
    secondary_cta: string;
    stats_title: string;
    stats_projects: string;
    stats_completed: string;
    stats_donations: string;
    stats_supporters: string;
    features_title: string;
    features_subtitle: string;
    features: Feature[];
    projects_title: string;
    projects_subtitle: string;
    view_all_projects: string;
    testimonial_quote: string;
    testimonial_author: string;
    contact_title: string;
    contact_description: string;
    contact_email_label: string;
    dashboard_label: string;
    projects_label: string;
    login_label: string;
    register_label: string;
    logout_label: string;
    progress_label: string;
    collected_label: string;
    target_label: string;
    empty_featured: string;
    details_label: string;
}

const props = defineProps<{
    stats: Stats;
    featuredProjects: Project[];
    translations: Translations;
}>();

const page = usePage<AppPageProps>();
const authUser = computed(() => page.props.auth?.user ?? null);

const amountFormatter = new Intl.NumberFormat(undefined, {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
});

const progressWidth = (value: number) => `${Math.min(100, Math.max(0, value))}%`;
</script>

<template>
    <Head :title="translations.hero_title" />

    <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-slate-50">
        <PlaceholderPattern class="pointer-events-none absolute inset-0 opacity-[0.03]" />

        <header class="relative z-10 border-b border-white/10">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 backdrop-blur">
                        <span class="text-2xl font-semibold tracking-tight text-white">{{ page.props.name[0] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-semibold leading-tight">{{ page.props.name }}</span>
                        <span class="text-xs text-white/70">{{ page.props.quote.message }}</span>
                    </div>
                </div>

                <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
                    <Link
                        :href="projectsIndex()"
                        class="transition hover:text-emerald-300"
                    >{{ translations.projects_label }}</Link>
                    <Link
                        v-if="authUser"
                        :href="dashboard()"
                        class="transition hover:text-emerald-300"
                    >{{ translations.dashboard_label }}</Link>
                    <Link
                        :href="projectsIndex()"
                        class="transition hover:text-emerald-300"
                    >{{ translations.view_all_projects }}</Link>
                </nav>

                <div class="flex items-center gap-3">
                    <template v-if="authUser">
                        <Button variant="ghost" class="text-white hover:bg-white/10" as-child>
                            <Link :href="logoutRoute()" method="post" as="button">
                                {{ translations.logout_label }}
                            </Link>
                        </Button>
                        <Button
                            variant="secondary"
                            class="bg-emerald-400 text-slate-900 hover:bg-emerald-300"
                            as-child
                        >
                            <Link :href="dashboard()">{{ translations.dashboard_label }}</Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Button variant="ghost" class="text-white hover:bg-white/10" as-child>
                            <Link :href="loginRoute()">{{ translations.login_label }}</Link>
                        </Button>
                        <Button class="bg-emerald-400 text-slate-900 hover:bg-emerald-300" as-child>
                            <Link :href="registerRoute()">{{ translations.register_label }}</Link>
                        </Button>
                    </template>
                </div>
            </div>
        </header>

        <main class="relative z-10">
            <section class="mx-auto flex w-full max-w-6xl flex-col gap-12 px-6 py-16 lg:flex-row lg:items-center">
                <div class="flex-1 space-y-8">
                    <div class="inline-flex items-center rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs uppercase tracking-[0.35em] text-emerald-200">
                        {{ translations.stats_title }}
                    </div>
                    <h1 class="text-4xl font-bold leading-tight text-white sm:text-5xl">
                        {{ translations.hero_title }}
                    </h1>
                    <p class="text-lg text-white/70">
                        {{ translations.hero_subtitle }}
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        <Button class="bg-emerald-400 text-slate-900 hover:bg-emerald-300" size="lg" as-child>
                            <Link :href="projectsIndex()">{{ translations.primary_cta }}</Link>
                        </Button>
                        <Button variant="outline" class="border-white/30 bg-transparent text-white hover:bg-white/10" size="lg" as-child>
                            <Link :href="authUser ? dashboard() : registerRoute()">
                                {{ translations.secondary_cta }}
                            </Link>
                        </Button>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="grid gap-6 rounded-3xl border border-white/10 bg-white/5 p-8 backdrop-blur">
                        <div class="grid grid-cols-2 gap-6 text-center">
                            <div class="rounded-2xl bg-white/5 p-6">
                                <p class="text-sm text-white/60">{{ translations.stats_projects }}</p>
                                <p class="mt-2 text-3xl font-semibold">{{ stats.projects_total }}</p>
                            </div>
                            <div class="rounded-2xl bg-white/5 p-6">
                                <p class="text-sm text-white/60">{{ translations.stats_completed }}</p>
                                <p class="mt-2 text-3xl font-semibold text-emerald-300">{{ stats.projects_completed }}</p>
                            </div>
                            <div class="rounded-2xl bg-white/5 p-6">
                                <p class="text-sm text-white/60">{{ translations.stats_donations }}</p>
                                <p class="mt-2 text-3xl font-semibold">{{ amountFormatter.format(stats.donations_amount) }}</p>
                            </div>
                            <div class="rounded-2xl bg-white/5 p-6">
                                <p class="text-sm text-white/60">{{ translations.stats_supporters }}</p>
                                <p class="mt-2 text-3xl font-semibold">{{ stats.donations_total }}</p>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-gradient-to-r from-emerald-400 via-emerald-500 to-sky-500 p-[1px]">
                            <div class="rounded-[1.4rem] bg-slate-950/90 p-6 text-center text-sm text-white/70">
                                “{{ translations.testimonial_quote }}”
                                <div class="mt-3 text-sm font-medium text-white/80">
                                    — {{ translations.testimonial_author }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-slate-950/60 py-16">
                <div class="mx-auto flex w-full max-w-6xl flex-col gap-12 px-6 lg:flex-row">
                    <div class="flex-1 space-y-6">
                        <h2 class="text-3xl font-semibold text-white">
                            {{ translations.features_title }}
                        </h2>
                        <p class="text-white/70">
                            {{ translations.features_subtitle }}
                        </p>
                    </div>
                    <div class="flex-1 grid gap-6">
                        <Card
                            v-for="feature in translations.features"
                            :key="feature.title"
                            class="border-white/10 bg-white/5 text-white backdrop-blur"
                        >
                            <CardHeader>
                                <CardTitle class="text-xl font-semibold">{{ feature.title }}</CardTitle>
                                <CardDescription class="text-sm text-white/70">
                                    {{ feature.description }}
                                </CardDescription>
                            </CardHeader>
                        </Card>
                    </div>
                </div>
            </section>

            <section class="mx-auto w-full max-w-6xl px-6 py-16">
                <div class="flex items-center justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-semibold text-white">{{ translations.projects_title }}</h2>
                        <p class="mt-2 text-white/60">
                            {{ translations.projects_subtitle }}
                        </p>
                    </div>
                    <Button variant="secondary" class="hidden bg-emerald-400 text-slate-900 hover:bg-emerald-300 md:inline-flex" as-child>
                        <Link :href="projectsIndex()">{{ translations.view_all_projects }}</Link>
                    </Button>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <Card
                        v-for="project in featuredProjects"
                        :key="project.id"
                        class="flex h-full flex-col overflow-hidden border-white/10 bg-white/5 text-white backdrop-blur"
                    >
                        <CardHeader class="space-y-4">
                            <div class="relative aspect-video overflow-hidden rounded-2xl bg-slate-800/60">
                                <img
                                    v-if="project.image_url"
                                    :src="project.image_url"
                                    :alt="project.name"
                                    class="h-full w-full object-cover"
                                />
                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center bg-gradient-to-br from-emerald-400/30 to-sky-400/30 text-sm font-semibold text-white"
                                >
                                    {{ project.name }}
                                </div>
                            </div>
                            <CardTitle class="text-xl font-semibold">{{ project.name }}</CardTitle>
                            <CardDescription class="text-sm text-white/70">
                                {{ project.description }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="mt-auto space-y-4">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm font-medium text-white/80">
                                    <span>{{ translations.progress_label }}</span>
                                    <span>{{ project.progress }}%</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-white/10">
                                    <div
                                        class="h-2 rounded-full bg-gradient-to-r from-emerald-400 via-emerald-500 to-sky-500"
                                        :style="{ width: progressWidth(project.progress) }"
                                    />
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-sm text-white/70">
                                <div>
                                    <span class="block text-xs uppercase tracking-wide text-white/50">{{ translations.collected_label }}</span>
                                    <span class="text-base font-semibold text-white">{{ amountFormatter.format(project.collected_amount) }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-xs uppercase tracking-wide text-white/50">{{ translations.target_label }}</span>
                                    <span class="text-base font-semibold text-white">{{ amountFormatter.format(project.target_amount) }}</span>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <Button class="flex-1 bg-emerald-400 text-slate-900 hover:bg-emerald-300" as-child>
                                    <Link :href="showProject(project.id)">
                                        {{ translations.primary_cta }}
                                    </Link>
                                </Button>
                                <Button variant="outline" class="flex-1 border-white/20 bg-transparent text-white hover:bg-white/10" as-child>
                                    <Link :href="showProject(project.id)">{{ translations.details_label }}</Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div
                    v-if="!featuredProjects.length"
                    class="mt-12 rounded-3xl border border-dashed border-white/20 bg-white/5 p-10 text-center text-white/70"
                >
                    {{ translations.empty_featured }}
                </div>
            </section>
        </main>

        <footer class="border-t border-white/10 bg-slate-950/80">
            <div class="mx-auto w-full max-w-6xl px-6 py-10">
                <div class="grid gap-8 md:grid-cols-2">
                    <div>
                        <h3 class="text-xl font-semibold text-white">{{ translations.contact_title }}</h3>
                        <p class="mt-2 text-sm text-white/60">
                            {{ translations.contact_description }}
                        </p>
                    </div>
                    <div class="space-y-3 text-sm text-white/70">
                        <div class="font-medium text-white">{{ translations.contact_email_label }}</div>
                        <a href="mailto:team@charityhub.test" class="text-emerald-300 hover:text-emerald-200">
                            team@charityhub.test
                        </a>
                    </div>
                </div>
                <div class="mt-10 flex flex-col gap-2 border-t border-white/5 pt-6 text-xs text-white/50 md:flex-row md:items-center md:justify-between">
                    <span>© {{ new Date().getFullYear() }} {{ page.props.name }}. All rights reserved.</span>
                    <span>{{ translations.testimonial_author }}</span>
                </div>
            </div>
        </footer>
    </div>
</template>
