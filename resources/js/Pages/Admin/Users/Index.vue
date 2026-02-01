<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import type { BreadcrumbItemType } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { dashboard } from '@/routes';

interface UserRow {
    id: number;
    name: string;
    email: string;
    role: string;
    created_at: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedUsers {
    data: UserRow[];
    links: PaginationLink[];
}

interface Translations {
    title: string;
    create: string;
    empty: string;
    fields: Record<string, string>;
    actions: Record<string, string>;
    roles: Record<string, string>;
    confirm_delete: string;
    dashboardTitle: string;
}

const props = defineProps<{
    users: PaginatedUsers;
    translations: Translations;
}>();

const breadcrumbs = computed<BreadcrumbItemType[]>(() => [
    { title: props.translations.dashboardTitle, href: dashboard() },
    { title: props.translations.title, href: '/admin/users' },
]);

const deleteForm = useForm({});

const deleteUser = (user: UserRow) => {
    if (!confirm(props.translations.confirm_delete)) return;

    deleteForm.delete(`/admin/users/${user.id}`);
};
</script>

<template>
    <Head :title="translations.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4" dir="rtl">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <h1 class="text-2xl font-semibold">{{ translations.title }}</h1>
                <Button as-child class="btn btn-primary text-white">
                    <Link href="/admin/users/create">{{ translations.create }}</Link>
                </Button>
            </div>

            <Card v-if="users.data.length" class="overflow-hidden">
                <CardHeader>
                    <CardTitle class="text-lg font-semibold">{{ translations.title }}</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border text-right">
                            <thead class="bg-muted/50 text-sm font-medium">
                                <tr>
                                    <th class="px-4 py-3">{{ translations.fields.name }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.email }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.role }}</th>
                                    <th class="px-4 py-3">{{ translations.fields.created_at }}</th>
                                    <th class="px-4 py-3 text-center">{{ translations.actions.edit }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border text-sm">
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-muted/40">
                                    <td class="px-4 py-3 font-medium">{{ user.name }}</td>
                                    <td class="px-4 py-3">{{ user.email }}</td>
                                    <td class="px-4 py-3">{{ translations.roles[user.role] ?? user.role }}</td>
                                    <td class="px-4 py-3">{{ user.created_at ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <Button variant="outline" size="sm" as-child>
                                                <Link :href="`/admin/users/${user.id}/edit`">
                                                    {{ translations.actions.edit }}
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="destructive"
                                                size="sm"
                                                :disabled="deleteForm.processing"
                                                @click="deleteUser(user)"
                                            >
                                                {{ translations.actions.delete }}
                                            </Button>
                                        </div>
                                        <InputError :message="deleteForm.errors.user" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <div
                v-else
                class="flex min-h-[240px] items-center justify-center rounded-lg border border-dashed border-border bg-muted/30 p-6 text-center text-sm text-muted-foreground"
            >
                {{ translations.empty }}
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2" v-if="users.links?.length">
                <Link
                    v-for="link in users.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    class="rounded-md border px-3 py-2 text-sm"
                    :class="{
                        'btn btn-primary text-white': link.active,
                        'text-[var(--text)] border-[var(--border)] hover:bg-[var(--bg-alt)]': !link.active,
                        'pointer-events-none opacity-60': !link.url,
                    }"
                    v-html="link.label"
                />
            </div>
        </div>
    </AppLayout>
</template>
