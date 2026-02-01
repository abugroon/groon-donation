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
} from '@/routes';
import { index as projectsIndex, show as showProject } from '@/routes/projects';
import { computed, ref } from 'vue';
import { formatNumber } from '@/utils/formatNumber';

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
    privacy_policy: string;
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

const progressWidth = (value: number) => `${Math.min(100, Math.max(0, value))}%`;
const brokenImages = ref(new Set<number>());

const hasImage = (project: Project) =>
    Boolean(project.image_url) && !brokenImages.value.has(project.id);

const markBroken = (projectId: number) => {
    brokenImages.value.add(projectId);
};
</script>

<template>
    <Head :title="translations.hero_title" />

    <div class="relative min-h-screen overflow-hidden bg-[var(--bg)] text-[var(--text)]">
        <PlaceholderPattern class="pointer-events-none absolute inset-0 opacity-[0.03]" />

        <header class="relative z-10 navbar">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--bg-alt)]">
                        <span class="text-2xl font-semibold tracking-tight text-[var(--text)]">{{ page.props.name[0] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-semibold leading-tight">{{ page.props.name }}</span>
                    </div>
                </div>

                <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
                    <Link
                        :href="projectsIndex()"
                        class="transition hover:text-[var(--primary-600)]"
                    >{{ translations.projects_label }}</Link>
                    <Link
                        v-if="authUser"
                        :href="dashboard()"
                        class="transition hover:text-[var(--primary-600)]"
                    >{{ translations.dashboard_label }}</Link>
                    <Link
                        :href="projectsIndex()"
                        class="transition hover:text-[var(--primary-600)]"
                    >{{ translations.view_all_projects }}</Link>
                </nav>

                <div class="flex items-center gap-3">
                    <template v-if="authUser">
                        <Button variant="ghost" class="hover:bg-[var(--bg-alt)]" as-child>
                            <Link :href="logoutRoute()" method="post" as="button">
                                {{ translations.logout_label }}
                            </Link>
                        </Button>
                        <Button class="btn btn-primary" as-child>
                            <Link :href="dashboard()">{{ translations.dashboard_label }}</Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Button variant="ghost" class="hover:bg-[var(--bg-alt)]" as-child>
                            <Link :href="loginRoute()">{{ translations.login_label }}</Link>
                        </Button>
                    </template>
                </div>
            </div>
        </header>

        <main class="relative z-10">
            <section class="mx-auto flex w-full max-w-6xl flex-col gap-12 px-6 py-16 lg:flex-row lg:items-center">
                <div class="flex-1 space-y-8">
                    <div class="inline-flex items-center rounded-full border border-[var(--border)] bg-[var(--primary-100)] px-4 py-1 text-xs uppercase tracking-[0.35em] text-[var(--primary-600)]">
                        {{ translations.stats_title }}
                    </div>
                    <h1 class="text-4xl font-bold leading-tight sm:text-5xl">
                        {{ translations.hero_title }}
                    </h1>
                    <p class="text-lg text-[var(--text-muted)]">
                        {{ translations.hero_subtitle }}
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        <Button class="btn btn-cta" size="lg" as-child>
                            <Link :href="projectsIndex()">{{ translations.primary_cta }}</Link>
                        </Button>
                        <Button class="btn btn-primary" size="lg" as-child>
                            <Link :href="authUser ? dashboard() : loginRoute()">
                                {{ translations.secondary_cta }}
                            </Link>
                        </Button>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="grid gap-6 rounded-3xl border border-[var(--border)] bg-[var(--surface-2)] p-8">
                        <div class="grid grid-cols-2 gap-6 text-center">
                            <div class="rounded-2xl bg-[var(--surface)] p-6 shadow-[var(--shadow-soft)]">
                                <p class="text-sm text-[var(--text-muted)]">{{ translations.stats_projects }}</p>
                                <p class="mt-2 text-3xl font-semibold">{{ stats.projects_total }}</p>
                            </div>
                            <div class="rounded-2xl bg-[var(--surface)] p-6 shadow-[var(--shadow-soft)]">
                                <p class="text-sm text-[var(--text-muted)]">{{ translations.stats_completed }}</p>
                                <p class="mt-2 text-3xl font-semibold text-[var(--primary-600)]">{{ stats.projects_completed }}</p>
                            </div>
                            <div class="rounded-2xl bg-[var(--surface)] p-6 shadow-[var(--shadow-soft)]">
                                <p class="text-sm text-[var(--text-muted)]">{{ translations.stats_donations }}</p>
                                <p class="mt-2 text-3xl font-semibold">{{ formatNumber(stats.donations_amount) }}</p>
                            </div>
                            <div class="rounded-2xl bg-[var(--surface)] p-6 shadow-[var(--shadow-soft)]">
                                <p class="text-sm text-[var(--text-muted)]">{{ translations.stats_supporters }}</p>
                                <p class="mt-2 text-3xl font-semibold">{{ stats.donations_total }}</p>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-[var(--border)] bg-[var(--surface)] p-6 text-center text-sm text-[var(--text-muted)]">
                                “{{ translations.testimonial_quote }}”
                                <div class="mt-3 text-sm font-medium text-[var(--text)]">
                                    — {{ translations.testimonial_author }}
                                </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="section-alt py-16">
                <div class="mx-auto flex w-full max-w-6xl flex-col gap-12 px-6 lg:flex-row">
                    <div class="flex-1 space-y-6">
                        <h2 class="text-3xl font-semibold">
                            {{ translations.features_title }}
                        </h2>
                        <p class="text-[var(--text-muted)]">
                            {{ translations.features_subtitle }}
                        </p>
                    </div>
                    <div class="flex-1 grid gap-6">
                        <Card
                            v-for="feature in translations.features"
                            :key="feature.title"
                            class="border-[var(--border)] bg-[var(--surface)] shadow-[var(--shadow)]"
                        >
                            <CardHeader>
                                <CardTitle class="text-xl font-semibold">{{ feature.title }}</CardTitle>
                                <CardDescription class="text-sm text-[var(--text-muted)]">
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
                        <h2 class="text-3xl font-semibold">{{ translations.projects_title }}</h2>
                        <p class="mt-2 text-[var(--text-muted)]">
                            {{ translations.projects_subtitle }}
                        </p>
                    </div>
                    <Button class="btn btn-cta hidden md:inline-flex" as-child>
                        <Link :href="projectsIndex()">{{ translations.view_all_projects }}</Link>
                    </Button>
                </div>

                <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    <Card
                        v-for="project in featuredProjects"
                        :key="project.id"
                        class="flex h-full flex-col overflow-hidden border-[var(--border)] bg-[var(--surface)] shadow-[var(--shadow)]"
                    >
                        <CardHeader class="space-y-4">
                            <div class="relative aspect-video overflow-hidden rounded-2xl bg-[var(--bg-alt)]">
                                <img
                                    v-if="hasImage(project)"
                                    :src="project.image_url"
                                    :alt="project.name"
                                    class="h-full w-full object-cover"
                                    @error="markBroken(project.id)"
                                />
                                <div
                                    v-else
                                    class="project-fallback project-fallback-pattern flex h-full w-full flex-col items-center justify-center gap-1 text-sm font-semibold"
                                >
                                    <span class="text-lg tracking-widest">
                                        {{ project.name.slice(0, 2).toUpperCase() }}
                                    </span>
                                    <span class="text-xs font-medium text-[var(--text-muted)]">
                                        {{ project.name }}
                                    </span>
                                </div>
                            </div>
                            <CardTitle class="text-xl font-semibold">{{ project.name }}</CardTitle>
                            <CardDescription class="text-sm text-[var(--text-muted)]">
                                {{ project.description }}
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="mt-auto space-y-4">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm font-medium text-[var(--text-muted)]">
                                    <span>{{ translations.progress_label }}</span>
                                    <span>{{ project.progress }}%</span>
                                </div>
                                <div class="progress h-2 w-full">
                                    <div
                                        class="bar h-2"
                                        :style="{ width: progressWidth(project.progress) }"
                                    />
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-sm text-[var(--text-muted)]">
                                <div>
                                    <span class="block text-xs uppercase tracking-wide text-[var(--text-muted)]">{{ translations.collected_label }}</span>
                                    <span class="text-base font-semibold">{{ formatNumber(project.collected_amount) }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-xs uppercase tracking-wide text-[var(--text-muted)]">{{ translations.target_label }}</span>
                                    <span class="text-base font-semibold">{{ formatNumber(project.target_amount) }}</span>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <Button class="btn btn-cta flex-1" as-child>
                                    <Link :href="showProject(project.id)">
                                        {{ translations.primary_cta }}
                                    </Link>
                                </Button>
                                <Button class="btn btn-primary flex-1" as-child>
                                    <Link :href="showProject(project.id)">{{ translations.details_label }}</Link>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div
                    v-if="!featuredProjects.length"
                    class="mt-12 rounded-3xl border border-dashed border-[var(--border)] bg-[var(--surface-2)] p-10 text-center text-[var(--text-muted)]"
                >
                    {{ translations.empty_featured }}
                </div>
            </section>
        </main>

        <footer class="border-t border-[var(--border)] bg-[var(--bg-alt)]">
            <div class="mx-auto w-full max-w-6xl px-6 py-10">
                <div class="grid gap-8 md:grid-cols-2">
                    <div>
                        <h3 class="text-xl font-semibold">{{ translations.contact_title }}</h3>
                        <p class="mt-2 text-sm text-[var(--text-muted)]">
                            {{ translations.contact_description }}
                        </p>
                    </div>
                    <div class="space-y-3 text-sm text-[var(--text-muted)]">
                        <div class="font-medium text-[var(--text)]">{{ translations.contact_email_label }}</div>
                        <a href="mailto:moawiaabugroon@gmail.com" class="text-[var(--primary)] hover:text-[var(--primary-600)]">
                            moawiaabugroon@gmail.com
                        </a>

                        <div class="font-medium text-[var(--text)]">تواصل معنا على الواتساب</div>
                        <a href="https://wa.me/+249113040255" target="_blank" class="text-[var(--primary)] hover:text-[var(--primary-600)]">
                            +249113040255
                        </a>
                    </div>
                </div>
                <div
                    class="mt-10 flex flex-col gap-2 border-t border-[var(--border)] pt-6 text-xs text-[var(--text-muted)] md:flex-row md:items-center md:justify-between"
                >
                    <span>© {{ new Date().getFullYear() }} {{ page.props.name }}. All rights reserved.</span>
                    <div class="flex flex-wrap items-center gap-4">
                        <Link
                            href="/privacy-policy"
                            class="text-[var(--primary)] transition hover:text-[var(--primary-600)]"
                        >
                            {{ translations.privacy_policy }}
                        </Link>
                        <a
                            href="https://me.moawiaabugroon.com"
                            target="_blank"
                            class="transition hover:text-[var(--primary-600)]"
                        >
                            {{ translations.testimonial_author }}
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
