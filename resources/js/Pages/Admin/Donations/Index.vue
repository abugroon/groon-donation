<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import InputError from '@/components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';
import { computed, ref, watch } from 'vue';
import { dashboard } from '@/routes';
import { formatNumber } from '@/utils/formatNumber';

interface Donation {
    id: number;
    project: { id: number; name: string };
    donor_name: string | null;
    anonymous: boolean;
    amount: number;
    status: 'pending' | 'approved' | 'rejected';
    created_at: string;
    transfer_receipt_url: string | null;
}

interface BankAccount {
    id: number;
    account_name: string;
    bank_name: string;
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
    empty: string;
    approve: string;
    reject: string;
    fields: Record<string, string>;
    status_labels: Record<Donation['status'], string>;
    view_full: string;
    select_bank: string;
    anonymous: string;
    dashboardTitle: string;
}

const props = defineProps<{
    donations: PaginatedDonations;
    bankAccounts: BankAccount[];
    translations: Translations;
}>();

const breadcrumbs = computed<BreadcrumbItemType[]>(() => [
    { title: props.translations.dashboardTitle, href: dashboard() },
    { title: props.translations.title, href: '/admin/donations' },
]);

const receiptDialogOpen = ref(false);
const actionDialogOpen = ref(false);
const receiptDonation = ref<Donation | null>(null);
const selectedDonation = ref<Donation | null>(null);

const actionForm = useForm({
    status: 'pending' as Donation['status'],
    bank_account_id: '' as string | number,
});

const receiptIsImage = computed(
    () =>
        !!receiptDonation.value?.transfer_receipt_url?.match(
            /(\.png|\.jpg|\.jpeg|\.gif|\.webp|\.svg)$/i,
        ),
);

const isImageUrl = (url: string | null) =>
    !!url?.match(/(\.png|\.jpg|\.jpeg|\.gif|\.webp|\.svg)$/i);

const formattedDate = (value: string) =>
    new Date(value).toLocaleString('ar-EG', {
        dateStyle: 'medium',
        timeStyle: 'short',
    });

const openReceipt = (donation: Donation) => {
    receiptDonation.value = donation;
    receiptDialogOpen.value = true;
};

const openAction = (donation: Donation, status: Donation['status']) => {
    selectedDonation.value = donation;
    actionForm.reset();
    actionForm.status = status;
    actionForm.bank_account_id = '';
    actionDialogOpen.value = true;
};

const submitAction = () => {
    if (!selectedDonation.value) return;

    actionForm.put(`/admin/donations/${selectedDonation.value.id}/review`, {
        preserveScroll: true,
        onSuccess: () => {
            actionDialogOpen.value = false;
            selectedDonation.value = null;
        },
    });
};

watch(
    () => actionDialogOpen.value,
    (open) => {
        if (!open) {
            actionForm.clearErrors();
        }
    },
);
</script>

<template>
    <Head :title="translations.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4" dir="rtl">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-2xl font-semibold">{{ translations.title }}</h1>
            </div>

            <Card v-if="donations.data.length" class="overflow-hidden">
                <CardHeader>
                    <CardTitle class="text-lg font-semibold">{{ translations.title }}</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border text-right">
                            <thead class="bg-muted/50 text-sm font-medium">
                                <tr>
                                    <th class="px-4 py-3">{{ translations.fields.donor_name }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.amount }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.project }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.date }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.status }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.receipt }}</th>
                                    <th class="px-4 py-3 text-center">{{ translations.fields.bank_account }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border text-sm">
                                <tr v-for="donation in donations.data" :key="donation.id" class="hover:bg-muted/40">
                                    <td class="px-4 py-3 font-medium">
                                        {{ donation.anonymous ? translations.anonymous : donation.donor_name }}
                                    </td>
                                    <td class="px-4 py-3 font-semibold">
                                        {{ formatNumber(donation.amount) }} SD
                                    </td>
                                    <td class="px-4 py-3">{{ donation.project.name }}</td>
                                    <td class="px-4 py-3">{{ formattedDate(donation.created_at) }}</td>
                                    <td class="px-4 py-3">
                                        {{ translations.status_labels[donation.status] }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <button
                                                v-if="donation.transfer_receipt_url && isImageUrl(donation.transfer_receipt_url)"
                                                type="button"
                                                class="overflow-hidden rounded-md border border-border bg-background"
                                                @click="openReceipt(donation)"
                                            >
                                                <img
                                                    :src="donation.transfer_receipt_url"
                                                    alt="receipt"
                                                    class="h-14 w-14 object-cover"
                                                />
                                            </button>
                                            <Button
                                                v-else-if="donation.transfer_receipt_url"
                                                variant="outline"
                                                size="sm"
                                                @click="openReceipt(donation)"
                                            >
                                                {{ translations.view_full }}
                                            </Button>
                                            <span v-else class="text-muted-foreground text-xs">—</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex flex-wrap items-center justify-center gap-2" v-if="donation.status === 'pending'">
                                            <Button
                                                size="sm"
                                                class="bg-emerald-600 text-emerald-50 hover:bg-emerald-700"
                                                @click="openAction(donation, 'approved')"
                                            >
                                                {{ translations.approve }}
                                            </Button>
                                            <Button
                                                size="sm"
                                                variant="destructive"
                                                @click="openAction(donation, 'rejected')"
                                            >
                                                {{ translations.reject }}
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-2 border-t border-border px-4 py-3 text-sm">
                        <template v-for="link in donations.links" :key="link.label">
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
        </div>
    </AppLayout>

    <Dialog v-model:open="receiptDialogOpen">
        <DialogContent class="max-w-5xl">
            <DialogHeader>
                <DialogTitle>{{ translations.fields.receipt }}</DialogTitle>
            </DialogHeader>
            <div class="max-h-[80vh] overflow-auto">
                <img
                    v-if="receiptDonation && receiptIsImage"
                    :src="receiptDonation.transfer_receipt_url || ''"
                    alt="Receipt"
                    class="mx-auto max-h-[75vh] w-full rounded-md object-contain"
                />
                <iframe
                    v-else
                    :src="receiptDonation?.transfer_receipt_url || ''"
                    class="h-[75vh] w-full rounded-md"
                    allowfullscreen
                />
            </div>
        </DialogContent>
    </Dialog>

    <Dialog v-model:open="actionDialogOpen">
        <DialogContent class="sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>{{ translations.title }}</DialogTitle>
            </DialogHeader>

            <div v-if="selectedDonation" class="grid gap-4 text-right" dir="rtl">
                <div class="grid gap-1 text-sm">
                    <span class="text-muted-foreground">{{ translations.fields.donor_name }}</span>
                    <span class="font-semibold">
                        {{
                            selectedDonation.anonymous
                                ? translations.anonymous
                                : selectedDonation.donor_name
                        }}
                    </span>
                </div>

                <div class="grid gap-1 text-sm">
                    <span class="text-muted-foreground">{{ translations.fields.amount }}</span>
                    <span class="text-lg font-semibold">{{ formatNumber(selectedDonation.amount) }} SD</span>
                </div>

                <div class="grid gap-1 text-sm">
                    <span class="text-muted-foreground">{{ translations.fields.project }}</span>
                    <span class="font-semibold">{{ selectedDonation.project.name }}</span>
                </div>

                <div class="grid gap-2">
                    <span class="text-sm font-semibold">{{ translations.fields.status }}</span>
                    <div class="flex flex-wrap gap-2">
                        <Button
                            type="button"
                            :variant="actionForm.status === 'approved' ? 'default' : 'outline'"
                            @click="actionForm.status = 'approved'"
                        >
                            {{ translations.approve }}
                        </Button>
                        <Button
                            type="button"
                            :variant="actionForm.status === 'rejected' ? 'default' : 'outline'"
                            @click="actionForm.status = 'rejected'; actionForm.bank_account_id = ''"
                        >
                            {{ translations.reject }}
                        </Button>
                    </div>
                    <InputError :message="actionForm.errors.status" />
                </div>

                <div v-if="actionForm.status === 'approved'" class="grid gap-2">
                    <label for="bank_account_id" class="text-sm font-semibold">
                        {{ translations.select_bank }}
                    </label>
                    <select
                        id="bank_account_id"
                        v-model="actionForm.bank_account_id"
                        required
                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                    >
                        <option value="" disabled>-- اختر الحساب البنكي --</option>
                        <option
                            v-for="account in bankAccounts"
                            :key="account.id"
                            :value="account.id"
                        >
                            {{ account.account_name }} - {{ account.bank_name }}
                        </option>
                    </select>
                    <InputError :message="actionForm.errors.bank_account_id" />
                </div>

                <div class="flex justify-between gap-2">
                    <Button variant="outline" type="button" @click="actionDialogOpen = false">إلغاء</Button>
                    <Button type="button" :disabled="actionForm.processing" @click="submitAction">
                        {{ actionForm.status === 'approved' ? translations.approve : translations.reject }}
                    </Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
