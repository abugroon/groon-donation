<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import { index as projectsIndex } from '@/routes/projects';
import { computed } from 'vue';

interface Stats {
    projects_total: number;
    projects_completed: number;
    projects_in_progress: number;
    donations_total: number;
    donations_amount: number;
    overall_progress: number;
}

interface ProjectSummary {
    id: number;
    name: string;
    progress: number;
    status: 'open' | 'in_progress' | 'completed';
    image_url?: string | null;
}

interface Translations {
    title: string;
    projects_total: string;
    projects_completed: string;
    projects_in_progress: string;
    donations_total: string;
    donations_amount: string;
    overall_progress: string;
    recent_projects: string;
    view_all_projects: string;
    recent_projects_empty: string;
    status_labels: Record<ProjectSummary['status'], string>;
}

const props = defineProps<{
    stats: Stats;
    recentProjects: ProjectSummary[];
    translations: Translations;
}>();

const breadcrumbs = computed<BreadcrumbItemType[]>(() => [
    { title: props.translations.title, href: dashboard() },
]);

const currencyFormatter = new Intl.NumberFormat(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const statusBreakdown = computed(() => {
    const openCount = Math.max(
        props.stats.projects_total - props.stats.projects_completed - props.stats.projects_in_progress,
        0,
    );

    return [
        {
            label: props.translations.status_labels.open,
            value: props.stats.projects_total
                ? Math.round((openCount * 100) / props.stats.projects_total)
                : 0,
        },
        {
            label: props.translations.status_labels.in_progress,
            value: props.stats.projects_total
                ? Math.round((props.stats.projects_in_progress * 100) / props.stats.projects_total)
                : 0,
        },
        {
            label: props.translations.status_labels.completed,
            value: props.stats.projects_total
                ? Math.round((props.stats.projects_completed * 100) / props.stats.projects_total)
                : 0,
        },
    ];
});

const progressWidth = (value: number) => `${Math.min(100, Math.max(0, value))}%`;
</script>

<template>
    <Head :title="translations.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-6 p-4">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>{{ translations.projects_total }}</CardDescription>
                        <CardTitle class="text-3xl font-semibold">
                            {{ stats.projects_total }}
                        </CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>{{ translations.projects_in_progress }}</CardDescription>
                        <CardTitle class="text-3xl font-semibold text-amber-600">
                            {{ stats.projects_in_progress }}
                        </CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>{{ translations.projects_completed }}</CardDescription>
                        <CardTitle class="text-3xl font-semibold text-emerald-600">
                            {{ stats.projects_completed }}
                        </CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>{{ translations.donations_amount }}</CardDescription>
                        <CardTitle class="text-3xl font-semibold text-primary">
                            {{ currencyFormatter.format(stats.donations_amount) }}
                        </CardTitle>
                        <CardDescription>
                            {{ translations.donations_total }}: {{ stats.donations_total }}
                        </CardDescription>
                    </CardHeader>
                </Card>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <Card class="lg:col-span-2">
                    <CardHeader>
                        <CardTitle>{{ translations.overall_progress }}</CardTitle>
                        <CardDescription>{{ translations.donations_amount }}: {{ currencyFormatter.format(stats.donations_amount) }}</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm font-medium">
                                <span>{{ translations.overall_progress }}</span>
                                <span>{{ stats.overall_progress }}%</span>
                            </div>
                            <div class="h-3 w-full rounded-full bg-muted">
                                <div
                                    class="h-3 rounded-full bg-primary transition-all"
                                    :style="{ width: progressWidth(stats.overall_progress) }"
                                />
                            </div>
                        </div>
                        <div class="grid gap-3 md:grid-cols-3">
                            <div
                                v-for="status in statusBreakdown"
                                :key="status.label"
                                class="space-y-1"
                            >
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-muted-foreground">{{ status.label }}</span>
                                    <span>{{ status.value }}%</span>
                                </div>
                                <div class="h-2 w-full rounded-full bg-muted">
                                    <div
                                        class="h-2 rounded-full bg-primary/70 transition-all"
                                        :style="{ width: progressWidth(status.value) }"
                                    />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="lg:col-span-1">
                    <CardHeader>
                        <CardTitle>{{ translations.recent_projects }}</CardTitle>
                        <CardDescription>
                            {{ translations.projects_total }}: {{ stats.projects_total }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div
                            v-if="recentProjects.length"
                            class="space-y-4"
                        >
                            <div
                                v-for="project in recentProjects"
                                :key="project.id"
                                class="flex items-center gap-3"
                            >
                                <div class="h-12 w-12 overflow-hidden rounded-md bg-muted">
                                    <img
                                        v-if="project.image_url"
                                        :src="project.image_url"
                                        :alt="project.name"
                                        class="h-full w-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/10 to-primary/30 text-xs font-medium text-primary"
                                    >
                                        {{ project.name.slice(0, 2).toUpperCase() }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between text-sm font-medium">
                                        <span>{{ project.name }}</span>
                                        <span>{{ project.progress }}%</span>
                                    </div>
                                    <div class="h-1.5 w-full rounded-full bg-muted">
                                        <div
                                            class="h-1.5 rounded-full bg-primary/80"
                                            :style="{ width: progressWidth(project.progress) }"
                                        />
                                    </div>
                                    <p class="mt-1 text-xs text-muted-foreground">
                                        {{ translations.status_labels[project.status] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div
                            v-else
                            class="rounded-lg border border-dashed border-muted-foreground/30 p-6 text-center text-sm text-muted-foreground"
                        >
                            {{ translations.recent_projects_empty }}
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Button as-child variant="outline" class="w-full">
                            <Link :href="projectsIndex()">{{ translations.view_all_projects }}</Link>
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
