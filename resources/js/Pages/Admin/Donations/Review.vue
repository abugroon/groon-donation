<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { dashboard } from '@/routes';
import { formatNumber } from '@/utils/formatNumber';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';

interface Donation {
    id: number;
    project: { id: number; name: string };
    donor_name: string | null;
    amount: number;
    anonymous: boolean;
    method: 'bank' | 'cash';
    status: 'pending' | 'approved' | 'rejected';
    bank_account_id: number | null;
    cash_description: string | null;
    transfer_receipt_url: string | null;
}

interface BankAccount {
    id: number;
    account_name: string;
    bank_name: string;
}

interface Translations {
    title: string;
    approve: string;
    reject: string;
    back_to_project: string;
    fields: Record<string, string>;
    status_labels: Record<Donation['status'], string>;
    methods: Record<Donation['method'], string>;
    dashboardTitle: string;
}

const props = defineProps<{
    donation: Donation;
    bankAccounts: BankAccount[];
    translations: Translations;
}>();

const breadcrumbs = computed<BreadcrumbItemType[]>(() => [
    { title: props.translations.dashboardTitle, href: dashboard() },
    { title: props.donation.project.name, href: `/admin/projects/${props.donation.project.id}` },
    { title: props.translations.title, href: `/admin/donations/${props.donation.id}/review` },
]);

const form = useForm({
    status: props.donation.status as Donation['status'],
    bank_account_id: props.donation.bank_account_id ?? '',
});

const showReceipt = ref(false);
const receiptIsImage = computed(
    () =>
        !!props.donation.transfer_receipt_url?.match(
            /(\.png|\.jpg|\.jpeg|\.gif|\.webp|\.svg)$/i,
        ),
);

const submit = () => {
    form.put(`/admin/donations/${props.donation.id}/review`);
};
</script>

<template>
    <Head :title="translations.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4" dir="rtl">
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <Button variant="outline" as-child>
                    <Link :href="`/admin/projects/${donation.project.id}`">
                        {{ translations.back_to_project }}
                    </Link>
                </Button>
            </div>

            <Card class="mx-auto w-full max-w-4xl">
                <CardHeader class="space-y-2">
                    <CardTitle class="text-2xl font-semibold">{{ translations.title }}</CardTitle>
                    <CardDescription class="text-sm text-muted-foreground">
                        {{ donation.project.name }}
                    </CardDescription>
                </CardHeader>

                <CardContent class="grid gap-6">
                    <div class="grid gap-2 rounded-lg border border-border p-4 text-sm">
                        <div class="flex flex-col gap-1">
                            <span class="text-muted-foreground">{{ translations.fields.amount }}</span>
                            <span class="text-lg font-semibold">{{ formatNumber(donation.amount) }} SD</span>
                        </div>

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div class="flex flex-col gap-1">
                                <span class="text-muted-foreground">{{ translations.fields.donor_name }}</span>
                                <span class="font-medium">
                                    {{ donation.anonymous ? translations.fields.anonymous : donation.donor_name }}
                                </span>
                            </div>

                            <div class="flex flex-col gap-1">
                                <span class="text-muted-foreground">{{ translations.fields.method }}</span>
                                <span class="font-medium">
                                    {{ translations.methods[donation.method] }}
                                </span>
                            </div>
                        </div>

                        <div v-if="donation.method === 'cash'" class="flex flex-col gap-1">
                            <span class="text-muted-foreground">{{ translations.fields.cash_description }}</span>
                            <span class="font-medium">{{ donation.cash_description }}</span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-muted-foreground">{{ translations.fields.status }}</span>
                            <span class="font-semibold">{{ translations.status_labels[donation.status] }}</span>
                        </div>

                        <div v-if="donation.transfer_receipt_url" class="flex flex-col gap-2">
                            <span class="text-muted-foreground">{{ translations.fields.receipt }}</span>
                            <div class="flex flex-wrap items-center gap-3">
                                <a
                                    :href="donation.transfer_receipt_url"
                                    class="text-primary hover:underline"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    {{ translations.fields.receipt }}
                                </a>

                                <Dialog v-model:open="showReceipt">
                                    <DialogTrigger as-child>
                                        <Button size="sm" variant="outline">
                                            {{ translations.fields.view_receipt }}
                                        </Button>
                                    </DialogTrigger>
                                    <DialogContent class="max-w-5xl">
                                        <DialogHeader>
                                            <DialogTitle>{{ translations.fields.receipt }}</DialogTitle>
                                        </DialogHeader>
                                        <div class="max-h-[80vh] overflow-auto">
                                            <img
                                                v-if="receiptIsImage"
                                                :src="donation.transfer_receipt_url"
                                                alt="Receipt"
                                                class="mx-auto max-h-[75vh] w-full rounded-md object-contain"
                                            />
                                            <iframe
                                                v-else
                                                :src="donation.transfer_receipt_url"
                                                class="h-[75vh] w-full rounded-md"
                                                allowfullscreen
                                            />
                                        </div>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </div>
                    </div>

                    <form class="grid gap-4" @submit.prevent="submit">
                        <div class="grid gap-2">
                            <span class="text-sm font-semibold">{{ translations.fields.status }}</span>
                            <div class="flex flex-wrap gap-2">
                                <Button
                                    type="button"
                                    :variant="form.status === 'approved' ? 'default' : 'outline'"
                                    @click="form.status = 'approved'"
                                >
                                    {{ translations.approve }}
                                </Button>
                                <Button
                                    type="button"
                                    :variant="form.status === 'rejected' ? 'default' : 'outline'"
                                    @click="form.status = 'rejected'; form.bank_account_id = ''"
                                >
                                    {{ translations.reject }}
                                </Button>
                            </div>
                            <InputError :message="form.errors.status" />
                        </div>

                        <div v-if="form.status === 'approved'" class="grid gap-2">
                            <label for="bank_account_id" class="text-sm font-semibold">
                                {{ translations.fields.bank_account }}
                            </label>
                            <select
                                id="bank_account_id"
                                v-model="form.bank_account_id"
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
                            <InputError :message="form.errors.bank_account_id" />
                        </div>

                        <div class="flex justify-end gap-2">
                            <Button
                                type="submit"
                                :disabled="form.processing || (form.status === 'approved' && !form.bank_account_id)"
                            >
                                {{ translations.approve }} / {{ translations.reject }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
