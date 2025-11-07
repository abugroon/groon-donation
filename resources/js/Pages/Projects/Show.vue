<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import type { BreadcrumbItemType } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { index as projectsIndex, show as showProject } from '@/routes/projects';
import { store as storeDonation } from '@/routes/donations';
import { dashboard } from '@/routes';
import { computed, ref, watch } from 'vue';

interface Donation {
    id: number;
    donor_name: string | null;
    amount: number;
    is_anonymous: boolean;
    payment_method: string | null;
    created_at: string;
}

interface Project {
    id: number;
    name: string;
    description: string;
    status: 'open' | 'in_progress' | 'completed';
    target_amount: number;
    collected_amount: number;
    progress: number;
    image_url?: string | null;
    start_date?: string | null;
    end_date?: string | null;
    donations: Donation[];
}

interface Translations {
    donations: string;
    donate: string;
    anonymous_label: string;
    amount_label: string;
    donor_label: string;
    payment_method_label: string;
    status_labels: Record<Project['status'], string>;
    progress_label: string;
    target_label: string;
    collected_label: string;
    dashboardTitle: string;
    projectsTitle: string;
    donations_empty: string;
    cancel: string;
    fields: Record<string, string>;
}

const props = defineProps<{
    project: Project;
    translations: Translations;
}>();

const numberFormatter = new Intl.NumberFormat(undefined, {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
});

const breadcrumbs = computed<BreadcrumbItemType[]>(() => [
    { title: props.translations.dashboardTitle, href: dashboard() },
    { title: props.translations.projectsTitle, href: projectsIndex() },
    { title: props.project.name, href: showProject(props.project.id) },
]);

const donationDialogOpen = ref(false);

const form = useForm({
    project_id: props.project.id,
    donor_name: '',
    amount: '',
    is_anonymous: false,
    payment_method: '',
});

watch(
    () => form.is_anonymous,
    (value) => {
        if (value) {
            form.donor_name = '';
        }
    },
);

const submitDonation = () => {
    form.post(storeDonation(), {
        preserveScroll: true,
        onSuccess: () => {
            donationDialogOpen.value = false;
            form.reset('donor_name', 'amount', 'payment_method', 'is_anonymous');
        },
    });
};

const progressWidth = (value: number) => `${Math.min(100, Math.max(0, value))}%`;

</script>

<template>
    <Head :title="project.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="grid gap-6 p-4 lg:grid-cols-3">
            <Card class="lg:col-span-2">
                <CardHeader class="space-y-4">
                    <CardTitle class="text-3xl font-semibold">
                        {{ project.name }}
                    </CardTitle>
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <span
                            class="inline-flex items-center rounded-full border px-3 py-1 uppercase tracking-wide"
                            :class="{
                                'border-emerald-600 text-emerald-600 dark:border-emerald-400 dark:text-emerald-400': project.status === 'completed',
                                'border-amber-600 text-amber-600 dark:border-amber-400 dark:text-amber-400': project.status === 'in_progress',
                                'border-primary text-primary': project.status === 'open',
                            }"
                        >
                            {{ translations.status_labels[project.status] }}
                        </span>
                        <span class="text-muted-foreground">
                            {{ translations.target_label }}:
                            {{ numberFormatter.format(project.target_amount) }}
                        </span>
                        <span class="text-muted-foreground">
                            {{ translations.collected_label }}:
                            {{ numberFormatter.format(project.collected_amount) }}
                        </span>
                    </div>

                    <div class="relative aspect-video overflow-hidden rounded-lg bg-muted">
                        <img
                            v-if="project.image_url"
                            :src="project.image_url"
                            :alt="project.name"
                            class="h-full w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/10 to-primary/30 text-lg font-semibold text-primary"
                        >
                            {{ project.name }}
                        </div>
                    </div>
                </CardHeader>
                <CardContent class="space-y-6">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm font-medium">
                            <span>{{ translations.progress_label }}</span>
                            <span>{{ project.progress }}%</span>
                        </div>
                        <div class="h-3 w-full rounded-full bg-muted">
                            <div
                                class="h-3 rounded-full bg-primary transition-all"
                                :style="{ width: progressWidth(project.progress) }"
                            />
                        </div>
                    </div>

                    <p class="text-sm leading-relaxed text-muted-foreground">
                        {{ project.description }}
                    </p>

                    <div class="grid gap-2 text-sm text-muted-foreground md:grid-cols-2">
                        <div>
                            <span class="font-medium text-foreground">{{ translations.target_label }}: </span>
                            {{ numberFormatter.format(project.target_amount) }}
                        </div>
                        <div>
                            <span class="font-medium text-foreground">{{ translations.collected_label }}: </span>
                            {{ numberFormatter.format(project.collected_amount) }}
                        </div>
                        <div v-if="project.start_date">
                            <span class="font-medium text-foreground">{{ translations.fields.start_date }}: </span>
                            {{ project.start_date }}
                        </div>
                        <div v-if="project.end_date">
                            <span class="font-medium text-foreground">{{ translations.fields.end_date }}: </span>
                            {{ project.end_date }}
                        </div>
                    </div>
                </CardContent>
                <CardFooter class="justify-end">
                    <Dialog v-model:open="donationDialogOpen">
                        <DialogTrigger as-child>
                            <Button>
                                {{ translations.donate }}
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="max-w-lg">
                            <DialogHeader>
                                <DialogTitle>{{ translations.donate }}</DialogTitle>
                            </DialogHeader>
                            <form @submit.prevent="submitDonation" class="space-y-4">
                                <div class="grid gap-2">
                                    <Label for="donor_name">{{ translations.donor_label }}</Label>
                                    <Input
                                        id="donor_name"
                                        v-model="form.donor_name"
                                        :disabled="form.is_anonymous"
                                        :placeholder="translations.donor_label"
                                    />
                                    <InputError :message="form.errors.donor_name" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="amount">{{ translations.amount_label }}</Label>
                                    <Input
                                        id="amount"
                                        v-model="form.amount"
                                        type="number"
                                        min="1"
                                        step="0.01"
                                        required
                                        :class="{ 'border-destructive': form.errors.amount }"
                                    />
                                    <InputError :message="form.errors.amount" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="payment_method">{{ translations.payment_method_label }}</Label>
                                    <Input
                                        id="payment_method"
                                        v-model="form.payment_method"
                                        :placeholder="translations.payment_method_label"
                                    />
                                    <InputError :message="form.errors.payment_method" />
                                </div>
                                <Label class="flex items-center gap-2">
                                    <Checkbox v-model:checked="form.is_anonymous" />
                                    <span>{{ translations.anonymous_label }}</span>
                                </Label>
                                <DialogFooter class="gap-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        :disabled="form.processing"
                                        @click="donationDialogOpen = false"
                                    >
                                        {{ translations.cancel }}
                                    </Button>
                                    <Button type="submit" :disabled="form.processing">
                                        {{ translations.donate }}
                                    </Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </CardFooter>
            </Card>

            <Card class="lg:col-span-1">
                <CardHeader>
                    <CardTitle class="text-xl font-semibold">
                        {{ translations.donations }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div
                        v-if="project.donations.length"
                        class="space-y-3"
                    >
                        <div
                            v-for="donation in project.donations"
                            :key="donation.id"
                            class="rounded-lg border border-border/60 p-4 text-sm shadow-sm"
                        >
                            <div class="flex items-center justify-between font-medium">
                                <span>
                                    {{
                                        donation.is_anonymous
                                            ? translations.anonymous_label
                                            : donation.donor_name ?? translations.anonymous_label
                                    }}
                                </span>
                                <span class="text-primary">
                                    {{ numberFormatter.format(donation.amount) }}
                                </span>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-2 text-xs text-muted-foreground">
                                <span>{{ donation.payment_method ?? translations.payment_method_label }}</span>
                                <span>&middot;</span>
                                <span>{{ new Date(donation.created_at).toLocaleString() }}</span>
                            </div>
                        </div>
                    </div>
                    <div
                        v-else
                        class="rounded-lg border border-dashed border-muted-foreground/40 p-6 text-center text-sm text-muted-foreground"
                    >
                        {{ translations.donations_empty }}
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
