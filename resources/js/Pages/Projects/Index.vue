<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type {
    AppPageProps,
    BreadcrumbItemType,
} from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import {
    create as createProject,
    index as projectsIndex,
    show as showProject,
} from '@/routes/projects';
import { dashboard } from '@/routes';
import { computed } from 'vue';

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

const breadcrumbs = computed<BreadcrumbItemType[]>(() => [
    { title: props.translations.dashboardTitle, href: dashboard() },
    { title: props.translations.title, href: projectsIndex() },
]);

const numberFormatter = new Intl.NumberFormat(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const progressWidth = (value: number) => `${Math.min(100, Math.max(0, value))}%`;
</script>

<template>
    <Head :title="translations.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-2xl font-semibold">{{ translations.title }}</h1>

                <Button
                    v-if="canManageProjects"
                    as-child
                    class="bg-primary text-primary-foreground hover:bg-primary/90"
                >
                    <Link :href="createProject()">{{ translations.create }}</Link>
                </Button>
            </div>

            <div
                v-if="projects.length"
                class="grid gap-6 md:grid-cols-2 xl:grid-cols-3"
            >
                <Card
                    v-for="project in projects"
                    :key="project.id"
                    class="flex h-full flex-col overflow-hidden"
                >
                    <CardHeader class="space-y-4">
                        <div
                            class="relative aspect-video overflow-hidden rounded-lg bg-muted"
                        >
                            <img
                                v-if="project.image_url"
                                :src="project.image_url"
                                :alt="project.name"
                                class="h-full w-full object-cover"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/10 to-primary/30 text-sm font-medium text-primary"
                            >
                                {{ project.name }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <CardTitle class="text-lg">
                                {{ project.name }}
                            </CardTitle>
                            <span
                                class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium uppercase tracking-wide"
                                :class="{
                                    'border-emerald-600 text-emerald-600 dark:border-emerald-400 dark:text-emerald-400': project.status === 'completed',
                                    'border-amber-600 text-amber-600 dark:border-amber-400 dark:text-amber-400': project.status === 'in_progress',
                                    'border-primary text-primary': project.status === 'open',
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
                            <div class="h-2 w-full rounded-full bg-muted">
                                <div
                                    class="h-2 rounded-full bg-primary transition-all"
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
                                    {{
                                        numberFormatter.format(
                                            project.target_amount,
                                        )
                                    }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-muted-foreground">
                                    {{ translations.collected_label }}
                                </span>
                                <span>
                                    {{
                                        numberFormatter.format(
                                            project.collected_amount,
                                        )
                                    }}
                                </span>
                            </div>
                        </div>
                    </CardContent>

                    <CardFooter class="mt-auto flex justify-end">
                        <Button variant="outline" as-child>
                            <Link :href="showProject(project.id)">
                                {{ translations.view }}
                            </Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>

            <div
                v-else
                class="flex flex-col items-center justify-center rounded-lg border border-dashed border-muted-foreground/30 p-12 text-center"
            >
                <h2 class="text-lg font-semibold">
                    {{ translations.title }}
                </h2>
                <p class="mt-2 max-w-xl text-sm text-muted-foreground">
                    {{ translations.empty }}
                </p>
            </div>
        </div>
    </AppLayout>
</template>
