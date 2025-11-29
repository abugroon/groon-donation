<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { BreadcrumbItemType } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { dashboard } from '@/routes';
import { formatNumber } from '@/utils/formatNumber';

interface BankAccountSummary {
    bank_account_id: number;
    donations_count: number;
    total_amount: number;
}

interface BankAccount {
    id: number;
    account_name: string;
    bank_name: string;
    iban: string;
    account_number: string;
    status: 'active' | 'inactive';
}

interface Project {
    id: number;
    name: string;
    description: string;
    target_amount: number;
    collected_amount: number;
    progress: number;
    status: 'open' | 'in_progress' | 'completed';
    bank_accounts: BankAccount[];
}

interface Translations {
    title: string;
    summary: string;
    sql_examples: string;
    fields: Record<string, string>;
    statuses: Record<Project['status'], string>;
    labels: Record<string, string>;
    dashboardTitle: string;
    projectsTitle: string;
}

const props = defineProps<{
    project: Project;
    accountSummaries: BankAccountSummary[];
    translations: Translations;
}>();

const breadcrumbs = computed<BreadcrumbItemType[]>(() => [
    { title: props.translations.dashboardTitle, href: dashboard() },
    { title: props.translations.projectsTitle, href: '/projects' },
    { title: props.project.name, href: `/admin/projects/${props.project.id}` },
]);

const summaryRows = computed(() =>
    props.project.bank_accounts.map((account) => {
        const summary = props.accountSummaries.find(
            (item) => item.bank_account_id === account.id,
        );

        return {
            ...account,
            donations_count: summary?.donations_count ?? 0,
            total_amount: summary?.total_amount ?? 0,
        };
    }),
);
</script>

<template>
    <Head :title="project.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 p-4" dir="rtl">
            <Card>
                <CardHeader>
                    <CardTitle class="text-2xl font-semibold">{{ project.name }}</CardTitle>
                </CardHeader>
                <CardContent class="grid gap-3 text-sm">
                    <p class="text-muted-foreground">{{ project.description }}</p>
                    <div class="grid gap-2 md:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-lg bg-muted/60 p-3">
                            <div class="text-xs text-muted-foreground">{{ translations.labels.target }}</div>
                            <div class="text-lg font-semibold">{{ formatNumber(project.target_amount) }} ر.س</div>
                        </div>
                        <div class="rounded-lg bg-muted/60 p-3">
                            <div class="text-xs text-muted-foreground">{{ translations.labels.collected }}</div>
                            <div class="text-lg font-semibold">{{ formatNumber(project.collected_amount) }} ر.س</div>
                        </div>
                        <div class="rounded-lg bg-muted/60 p-3">
                            <div class="text-xs text-muted-foreground">{{ translations.labels.progress }}</div>
                            <div class="text-lg font-semibold">%{{ project.progress }}</div>
                        </div>
                        <div class="rounded-lg bg-muted/60 p-3">
                            <div class="text-xs text-muted-foreground">{{ translations.labels.status }}</div>
                            <div class="text-lg font-semibold">
                                {{ translations.statuses[project.status] }}
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-xl font-semibold">{{ translations.summary }}</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border text-right text-sm">
                            <thead class="bg-muted/50 font-medium">
                                <tr>
                                    <th class="px-4 py-3">{{ translations.fields.account_name }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.bank_name }}</th>
                                    <th class="px-4 py-3">{{ translations.labels.total_received }}</th>
                                    <th class="px-4 py-3">{{ translations.labels.donations_count }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                <tr v-for="account in summaryRows" :key="account.id" class="hover:bg-muted/40">
                                    <td class="px-4 py-3 font-medium">
                                        {{ account.account_name }}
                                    </td>
                                    <td class="px-4 py-3">{{ account.bank_name }}</td>
                                    <td class="px-4 py-3">{{ formatNumber(account.total_amount) }} ر.س</td>
                                    <td class="px-4 py-3">{{ account.donations_count }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
