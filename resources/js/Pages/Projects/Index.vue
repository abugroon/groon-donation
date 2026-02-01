<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { AppPageProps } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    create as createProject,
    index as projectsIndex,
    show as showProject,
} from '@/routes/projects';
import { dashboard, home } from '@/routes';
import { computed, ref } from 'vue';
import { formatNumber } from '@/utils/formatNumber';

interface Project {
    id: number;
    name: string;
    description: string;
    status: 'open' | 'in_progress' | 'completed';
    progress: number;
    target_amount: number;
    collected_amount: number;
    image_url?: string | null;
}

interface Translations {
    title: string;
    create: string;
    view: string;
    progress_label: string;
    target_label: string;
    collected_label: string;
    status_labels: Record<Project['status'], string>;
    empty: string;
    dashboardTitle: string;
}

const props = defineProps<{
    projects: Project[];
    translations: Translations;
}>();

const page = usePage<AppPageProps>();
const canManageProjects = computed(
    () => page.props.auth?.user?.role === 'admin',
);
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
    <Head :title="translations.title" />

    <div class="relative min-h-screen overflow-hidden bg-[var(--bg)] text-[var(--text)]">
        <header class="relative z-10 navbar">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-5">
                <Link :href="home()" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--bg-alt)]">
                        <span class="text-2xl font-semibold tracking-tight text-[var(--text)]">{{ page.props.name[0] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-semibold leading-tight">{{ page.props.name }}</span>
                    </div>
                </Link>

                <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
                    <Link :href="projectsIndex()" class="transition hover:text-[var(--primary-600)]">
                        {{ translations.title }}
                    </Link>
                    <Link
                        v-if="authUser"
                        :href="dashboard()"
                        class="transition hover:text-[var(--primary-600)]"
                    >
                        {{ translations.dashboardTitle }}
                    </Link>
                </nav>

                <div class="flex items-center gap-3">
                    <Button
                        v-if="canManageProjects"
                        as-child
                        class="btn btn-primary"
                    >
                        <Link :href="createProject()">{{ translations.create }}</Link>
                    </Button>
                </div>
            </div>
        </header>

        <main class="relative z-10">
            <section class="mx-auto w-full max-w-6xl px-6 py-12">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h1 class="text-2xl font-semibold">{{ translations.title }}</h1>
                </div>

                <div
                    v-if="projects.length"
                    class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3"
                >
                    <Card
                        v-for="project in projects"
                        :key="project.id"
                        class="flex h-full flex-col overflow-hidden"
                    >
                        <CardHeader class="space-y-4">
                            <div
                                class="relative aspect-video overflow-hidden rounded-lg bg-[var(--bg-alt)]"
                            >
                                <img
                                    v-if="hasImage(project)"
                                    :src="project.image_url"
                                    :alt="project.name"
                                    class="h-full w-full object-cover"
                                    @error="markBroken(project.id)"
                                />
                            <div
                                v-else
                                class="project-fallback project-fallback-pattern flex h-full w-full flex-col items-center justify-center gap-1 text-sm font-medium"
                            >
                                <span class="text-base tracking-widest">
                                    {{ project.name.slice(0, 2).toUpperCase() }}
                                </span>
                                <span class="text-xs font-medium text-[var(--text-muted)]">
                                    {{ project.name }}
                                </span>
                            </div>
                            </div>

                            <div class="space-y-2">
                                <CardTitle class="text-lg">
                                    {{ project.name }}
                                </CardTitle>
                                <span
                                    class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium uppercase tracking-wide"
                                    :class="{
                                        'border-[var(--success)] text-[var(--success)]': project.status === 'completed',
                                        'border-[var(--warning)] text-[var(--warning)]': project.status === 'in_progress',
                                        'border-[var(--primary)] text-[var(--primary)]': project.status === 'open',
                                    }"
                                >
                                    {{ translations.status_labels[project.status] }}
                                </span>
                            </div>
                        </CardHeader>

                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <p class="line-clamp-3 text-sm text-muted-foreground">
                                    {{ project.description }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm font-medium">
                                    <span>{{ translations.progress_label }}</span>
                                    <span>{{ project.progress }}%</span>
                                </div>
                                <div class="progress h-2 w-full">
                                    <div
                                        class="bar h-2 transition-all"
                                        :style="{ width: progressWidth(project.progress) }"
                                    />
                                </div>
                            </div>

                            <div class="grid gap-1 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">
                                        {{ translations.target_label }}
                                    </span>
                                    <span>
                                        {{ formatNumber(project.target_amount) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-muted-foreground">
                                        {{ translations.collected_label }}
                                    </span>
                                    <span>
                                        {{ formatNumber(project.collected_amount) }}
                                    </span>
                                </div>
                            </div>
                        </CardContent>

                        <CardFooter class="mt-auto flex justify-end">
                            <Button class="btn btn-primary" as-child>
                                <Link :href="showProject(project.id)">
                                    {{ translations.view }}
                                </Link>
                            </Button>
                        </CardFooter>
                    </Card>
                </div>

                <div
                    v-else
                    class="mt-8 flex flex-col items-center justify-center rounded-lg border border-dashed border-[var(--border)] bg-[var(--surface-2)] p-12 text-center"
                >
                    <h2 class="text-lg font-semibold">
                        {{ translations.title }}
                    </h2>
                    <p class="mt-2 max-w-xl text-sm text-[var(--text-muted)]">
                        {{ translations.empty }}
                    </p>
                </div>
            </section>
        </main>
    </div>
</template>
