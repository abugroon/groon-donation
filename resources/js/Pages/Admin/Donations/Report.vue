<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { formatNumber } from '@/utils/formatNumber';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Donation {
    id: number;
    donor_name: string | null;
    anonymous: boolean;
    amount: number;
    created_at: string | null;
    transfer_receipt_url: string | null;
    bank_account: { id: number; account_name: string; bank_name: string } | null;
    project: { id: number; name: string } | null;
}

interface TotalRow {
    bank_account_id: number | null;
    total_amount: number;
    total_count: number;
}

interface BankAccount {
    id: number;
    account_name: string;
    bank_name: string;
}

interface Project {
    id: number;
    name: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedDonations {
    data: Donation[];
    links: PaginationLink[];
}

interface Translations {
    title: string;
    filters_title: string;
    project_label: string;
    bank_account_label: string;
    all_projects: string;
    all_accounts: string;
    donor_label: string;
    anonymous_label: string;
    amount_label: string;
    date_label: string;
    receipt_label: string;
    total_label: string;
    count_label: string;
    no_receipt: string;
    empty: string;
    unassigned_label: string;
}

const props = defineProps<{
    donations: PaginatedDonations;
    totals: TotalRow[];
    bankAccounts: BankAccount[];
    projects: Project[];
    filters: {
        project_id: number | null;
        bank_account_id: number | null;
    };
    translations: Translations;
}>();

const projectId = computed({
    get: () => (props.filters.project_id ? String(props.filters.project_id) : ''),
    set: (value) => updateFilters({ project_id: value ? Number(value) : null }),
});

const bankAccountId = computed({
    get: () => (props.filters.bank_account_id ? String(props.filters.bank_account_id) : ''),
    set: (value) => updateFilters({ bank_account_id: value ? Number(value) : null }),
});

const updateFilters = (payload: { project_id?: number | null; bank_account_id?: number | null }) => {
    router.get(
        '/admin/donations-report',
        {
            project_id: payload.project_id ?? props.filters.project_id ?? undefined,
            bank_account_id: payload.bank_account_id ?? props.filters.bank_account_id ?? undefined,
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
};

const totalsMap = computed(() => {
    const map = new Map<number | null, { total: number; count: number }>();
    props.totals.forEach((row) => {
        map.set(row.bank_account_id, {
            total: row.total_amount,
            count: row.total_count,
        });
    });
    return map;
});

const receiptModalOpen = ref(false);
const activeReceiptUrl = ref<string | null>(null);

const openReceipt = (url: string) => {
    activeReceiptUrl.value = url;
    receiptModalOpen.value = true;
};

const isPdfReceipt = computed(() =>
    activeReceiptUrl.value ? activeReceiptUrl.value.toLowerCase().includes('.pdf') : false,
);
</script>

<template>
    <Head :title="translations.title" />

    <AppLayout>
        <div class="p-4">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-2xl font-semibold">{{ translations.title }}</h1>
            </div>

            <Card class="mb-6">
                <CardHeader>
                    <CardTitle class="text-lg font-semibold">{{ translations.filters_title }}</CardTitle>
                </CardHeader>
                <CardContent class="grid gap-4 md:grid-cols-2">
                    <div class="grid gap-2">
                        <label class="text-sm font-medium">{{ translations.project_label }}</label>
                        <select
                            v-model="projectId"
                            class="w-full rounded-md border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm text-[var(--text)] shadow-sm"
                        >
                            <option value="">{{ translations.all_projects }}</option>
                            <option v-for="project in projects" :key="project.id" :value="project.id">
                                {{ project.name }}
                            </option>
                        </select>
                    </div>
                    <div class="grid gap-2">
                        <label class="text-sm font-medium">{{ translations.bank_account_label }}</label>
                        <select
                            v-model="bankAccountId"
                            class="w-full rounded-md border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm text-[var(--text)] shadow-sm"
                        >
                            <option value="">{{ translations.all_accounts }}</option>
                            <option v-for="account in bankAccounts" :key="account.id" :value="account.id">
                                {{ account.account_name }} - {{ account.bank_name }}
                            </option>
                        </select>
                    </div>
                </CardContent>
            </Card>

            <Card class="mb-6">
                <CardHeader>
                    <CardTitle class="text-lg font-semibold">{{ translations.total_label }}</CardTitle>
                </CardHeader>
                <CardContent class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[var(--border)] text-sm">
                        <thead class="bg-[var(--bg-alt)] text-[var(--text)]">
                            <tr>
                                <th class="px-4 py-3 text-right">{{ translations.bank_account_label }}</th>
                                <th class="px-4 py-3 text-right">{{ translations.total_label }}</th>
                                <th class="px-4 py-3 text-right">{{ translations.count_label }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--border)]">
                            <tr v-for="account in bankAccounts" :key="account.id">
                                <td class="px-4 py-3 font-medium">
                                    {{ account.account_name }} - {{ account.bank_name }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ formatNumber(totalsMap.get(account.id)?.total ?? 0) }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ totalsMap.get(account.id)?.count ?? 0 }}
                                </td>
                            </tr>
                            <tr v-if="totalsMap.has(null)">
                                <td class="px-4 py-3 font-medium">{{ translations.unassigned_label }}</td>
                                <td class="px-4 py-3">
                                    {{ formatNumber(totalsMap.get(null)?.total ?? 0) }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ totalsMap.get(null)?.count ?? 0 }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-lg font-semibold">{{ translations.title }}</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[var(--border)] text-right text-sm">
                            <thead class="bg-[var(--bg-alt)] text-[var(--text)]">
                                <tr>
                                    <th class="px-4 py-3">{{ translations.project_label }}</th>
                                    <th class="px-4 py-3">{{ translations.bank_account_label }}</th>
                                    <th class="px-4 py-3">{{ translations.donor_label }}</th>
                                    <th class="px-4 py-3">{{ translations.amount_label }}</th>
                                    <th class="px-4 py-3">{{ translations.date_label }}</th>
                                    <th class="px-4 py-3">{{ translations.receipt_label }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--border)]">
                                <tr v-for="donation in donations.data" :key="donation.id">
                                    <td class="px-4 py-3">
                                        {{ donation.project?.name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{
                                            donation.bank_account
                                                ? `${donation.bank_account.account_name} - ${donation.bank_account.bank_name}`
                                                : translations.unassigned_label
                                        }}
                                    </td>
                                    <td class="px-4 py-3 font-medium">
                                        {{
                                            donation.anonymous
                                                ? translations.anonymous_label
                                                : donation.donor_name ?? translations.anonymous_label
                                        }}
                                    </td>
                                    <td class="px-4 py-3">{{ formatNumber(donation.amount) }}</td>
                                    <td class="px-4 py-3">
                                        {{ donation.created_at ? new Date(donation.created_at).toLocaleString() : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <button
                                            v-if="donation.transfer_receipt_url"
                                            type="button"
                                            class="inline-flex items-center gap-2 text-[var(--primary)] hover:text-[var(--primary-600)]"
                                            @click="openReceipt(donation.transfer_receipt_url)"
                                        >
                                            معاينة
                                        </button>
                                        <span v-else class="text-[var(--text-muted)]">{{ translations.no_receipt }}</span>
                                    </td>
                                </tr>
                                <tr v-if="!donations.data.length">
                                    <td class="px-4 py-6 text-center text-[var(--text-muted)]" colspan="6">
                                        {{ translations.empty }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-[var(--border)] px-4 py-3 text-sm">
                        <template v-for="link in donations.links" :key="link.label">
                            <Button
                                v-if="link.url"
                                :variant="link.active ? 'default' : 'outline'"
                                size="sm"
                                as-child
                                :class="link.active ? 'btn btn-primary text-white' : 'border-[var(--border)] text-[var(--text)] hover:bg-[var(--bg-alt)]'"
                            >
                                <Link :href="link.url" preserve-scroll preserve-state v-html="link.label" />
                            </Button>
                            <Button v-else variant="outline" size="sm" disabled v-html="link.label" />
                        </template>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>

    <Dialog v-model:open="receiptModalOpen">
        <DialogContent class="max-w-4xl">
            <DialogHeader>
                <DialogTitle>{{ translations.receipt_label }}</DialogTitle>
            </DialogHeader>
            <div class="mt-2 overflow-hidden rounded-lg border border-[var(--border)] bg-[var(--surface-2)]">
                <template v-if="activeReceiptUrl">
                    <iframe
                        v-if="isPdfReceipt"
                        :src="activeReceiptUrl"
                        class="h-[70vh] w-full"
                    ></iframe>
                    <img
                        v-else
                        :src="activeReceiptUrl"
                        alt="Receipt"
                        class="max-h-[70vh] w-full object-contain"
                    />
                </template>
            </div>
        </DialogContent>
    </Dialog>
</template>
