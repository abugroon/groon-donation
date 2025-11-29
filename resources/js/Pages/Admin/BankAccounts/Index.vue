<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';
import { computed } from 'vue';
import { dashboard } from '@/routes';

interface BankAccount {
    id: number;
    account_name: string;
    bank_name: string;
    iban: string;
    account_number: string;
    status: 'active' | 'inactive';
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedBankAccounts {
    data: BankAccount[];
    links: PaginationLink[];
}

interface Translations {
    title: string;
    create: string;
    empty: string;
    fields: Record<string, string>;
    actions: Record<string, string>;
    status_labels: Record<BankAccount['status'], string>;
    confirm_delete: string;
    dashboardTitle: string;
}

const props = defineProps<{
    bankAccounts: PaginatedBankAccounts;
    translations: Translations;
}>();

const breadcrumbs = computed<BreadcrumbItemType[]>(() => [
    { title: props.translations.dashboardTitle, href: dashboard() },
    { title: props.translations.title, href: '/admin/bank-accounts' },
]);

const deleteForm = useForm({});

const deleteAccount = (account: BankAccount) => {
    if (!confirm(props.translations.confirm_delete)) return;

    deleteForm.delete(`/admin/bank-accounts/${account.id}`);
};
</script>

<template>
    <Head :title="translations.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4" dir="rtl">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-2xl font-semibold">{{ translations.title }}</h1>
                <Button as-child class="bg-primary text-primary-foreground hover:bg-primary/90">
                    <Link href="/admin/bank-accounts/create">{{ translations.create }}</Link>
                </Button>
            </div>

            <Card v-if="bankAccounts.data.length" class="overflow-hidden">
                <CardHeader>
                    <CardTitle class="text-lg font-semibold">{{ translations.title }}</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border text-right">
                            <thead class="bg-muted/50 text-sm font-medium">
                                <tr>
                                    <th class="px-4 py-3">{{ translations.fields.account_name }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.bank_name }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.iban }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.account_number }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.status }}</th>
                                    <th class="px-4 py-3 text-center">{{ translations.actions.edit }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border text-sm">
                                <tr v-for="account in bankAccounts.data" :key="account.id" class="hover:bg-muted/40">
                                    <td class="px-4 py-3 font-medium">{{ account.account_name }}</td>
                                    <td class="px-4 py-3">{{ account.bank_name }}</td>
                                    <td class="px-4 py-3">{{ account.iban }}</td>
                                    <td class="px-4 py-3">{{ account.account_number }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="rounded-full px-3 py-1 text-xs font-semibold"
                                            :class="{
                                                'bg-emerald-100 text-emerald-700': account.status === 'active',
                                                'bg-amber-100 text-amber-700': account.status === 'inactive',
                                            }"
                                        >
                                            {{ translations.status_labels[account.status] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <Button variant="outline" size="sm" as-child>
                                                <Link :href="`/admin/bank-accounts/${account.id}/edit`">
                                                    {{ translations.actions.edit }}
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="destructive"
                                                size="sm"
                                                :disabled="deleteForm.processing"
                                                @click="deleteAccount(account)"
                                            >
                                                {{ translations.actions.delete }}
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-border px-4 py-3 text-sm">
                        <template v-for="link in bankAccounts.links" :key="link.label">
                            <Button
                                v-if="link.url"
                                :variant="link.active ? 'default' : 'outline'"
                                size="sm"
                                as-child
                            >
                                <Link :href="link.url" preserve-scroll preserve-state v-html="link.label" />
                            </Button>
                            <Button v-else variant="outline" size="sm" disabled v-html="link.label" />
                        </template>
                    </div>
                </CardContent>
            </Card>

            <div v-else class="rounded-lg border border-dashed border-border bg-muted/40 p-6 text-center text-sm">
                {{ translations.empty }}
            </div>

            <InputError :message="deleteForm.errors.message" class="mt-4" />
        </div>
    </AppLayout>
</template>
