<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { BreadcrumbItemType } from '@/types';
import { computed } from 'vue';
import { dashboard } from '@/routes';

interface Translations {
    title: string;
    save: string;
    cancel: string;
    listTitle: string;
    fields: Record<string, string>;
    roles: Record<string, string>;
    dashboardTitle: string;
}

const props = defineProps<{ translations: Translations }>();

const breadcrumbs = computed<BreadcrumbItemType[]>(() => [
    { title: props.translations.dashboardTitle, href: dashboard() },
    { title: props.translations.listTitle, href: '/admin/users' },
    { title: props.translations.title, href: '/admin/users/create' },
]);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'user',
});

const submit = () => {
    form.post('/admin/users');
};
</script>

<template>
    <Head :title="translations.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4" dir="rtl">
            <Card class="mx-auto max-w-3xl">
                <CardHeader>
                    <CardTitle class="text-xl font-semibold">{{ translations.title }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <form class="space-y-6" @submit.prevent="submit">
                        <div class="grid gap-2">
                            <label for="name" class="text-sm font-semibold">{{ translations.fields.name }}</label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <label for="email" class="text-sm font-semibold">{{ translations.fields.email }}</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <label for="password" class="text-sm font-semibold">{{ translations.fields.password }}</label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <label for="password_confirmation" class="text-sm font-semibold">
                                {{ translations.fields.password_confirmation }}
                            </label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                required
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                            <InputError :message="form.errors.password_confirmation" />
                        </div>

                        <div class="grid gap-2">
                            <label for="role" class="text-sm font-semibold">{{ translations.fields.role }}</label>
                            <select
                                id="role"
                                v-model="form.role"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option value="admin">{{ translations.roles.admin }}</option>
                                <option value="user">{{ translations.roles.user }}</option>
                            </select>
                            <InputError :message="form.errors.role" />
                        </div>

                        <div class="flex justify-end gap-2">
                            <Button type="button" variant="outline" as-child>
                                <Link href="/admin/users">{{ translations.cancel }}</Link>
                            </Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ translations.save }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
