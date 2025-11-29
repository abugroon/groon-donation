<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
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

interface Translations {
    title: string;
    save: string;
    cancel: string;
    fields: Record<string, string>;
    listTitle: string;
    dashboardTitle: string;
}

const props = defineProps<{
    bankAccount: BankAccount;
    translations: Translations;
}>();

const breadcrumbs = computed<BreadcrumbItemType[]>(() => [
    { title: props.translations.dashboardTitle, href: dashboard() },
    { title: props.translations.listTitle, href: '/admin/bank-accounts' },
    { title: props.translations.title, href: `/admin/bank-accounts/${props.bankAccount.id}/edit` },
]);

const form = useForm({
    account_name: props.bankAccount.account_name,
    bank_name: props.bankAccount.bank_name,
    iban: props.bankAccount.iban,
    account_number: props.bankAccount.account_number,
    status: props.bankAccount.status,
});

const submit = () => {
    form.put(`/admin/bank-accounts/${props.bankAccount.id}`);
};
</script>

<template>
    <Head :title="translations.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4" dir="rtl">
            <Card class="mx-auto w-full max-w-3xl">
                <CardHeader>
                    <CardTitle class="text-2xl font-semibold">{{ translations.title }}</CardTitle>
                </CardHeader>
                <form @submit.prevent="submit">
                    <CardContent class="grid gap-4">
                        <div class="grid gap-2">
                            <Label for="account_name">{{ translations.fields.account_name }}</Label>
                            <Input
                                id="account_name"
                                v-model="form.account_name"
                                required
                                type="text"
                                :class="{ 'border-destructive': form.errors.account_name }"
                            />
                            <InputError :message="form.errors.account_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="bank_name">{{ translations.fields.bank_name }}</Label>
                            <Input
                                id="bank_name"
                                v-model="form.bank_name"
                                required
                                type="text"
                                :class="{ 'border-destructive': form.errors.bank_name }"
                            />
                            <InputError :message="form.errors.bank_name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="iban">{{ translations.fields.iban }}</Label>
                            <Input
                                id="iban"
                                v-model="form.iban"
                                type="text"
                                :class="{ 'border-destructive': form.errors.iban }"
                            />
                            <InputError :message="form.errors.iban" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="account_number">{{ translations.fields.account_number }}</Label>
                            <Input
                                id="account_number"
                                v-model="form.account_number"
                                required
                                type="text"
                                :class="{ 'border-destructive': form.errors.account_number }"
                            />
                            <InputError :message="form.errors.account_number" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="status">{{ translations.fields.status }}</Label>
                            <select
                                id="status"
                                v-model="form.status"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                            </select>
                            <InputError :message="form.errors.status" />
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-end gap-2">
                        <Button type="button" variant="outline" as-child>
                            <Link href="/admin/bank-accounts">{{ translations.cancel }}</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ translations.save }}
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
