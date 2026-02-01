<script setup lang="ts">
import InputError from '@/components/InputError.vue';
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
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import type { AppPageProps } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { dashboard, home } from '@/routes';
import { index as projectsIndex } from '@/routes/projects';
import { store as storeDonation } from '@/routes/donations';
import { computed, ref } from 'vue';
import { formatNumber } from '@/utils/formatNumber';

interface Donation {
    id: number;
    donor_name: string | null;
    amount: number;
    anonymous: boolean;
    method: string;
    status: string;
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
    update_project: string;
    save_project: string;
}

const props = defineProps<{
    project: Project;
    translations: Translations;
}>();

const page = usePage<AppPageProps>();
const authUser = computed(() => page.props.auth?.user ?? null);

const donationDialogOpen = ref(false);
const editDialogOpen = ref(false);

const form = useForm({
    project_id: props.project.id,
    donor_name: '',
    amount: '',
    anonymous: false,
    method: 'bank',
    cash_description: '',
    transfer_receipt: null as File | null,
});

const updateForm = useForm({
    name: props.project.name ?? '',
    description: props.project.description ?? '',
    target_amount: props.project.target_amount ?? '',
    start_date: props.project.start_date ?? '',
    image: null as File | null,
});

const submitDonation = () => {
    form.post(storeDonation(), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            donationDialogOpen.value = false;
            form.reset('donor_name', 'amount', 'method', 'cash_description', 'transfer_receipt', 'anonymous');
            form.method = 'bank';
            form.transfer_receipt = null;
        },
    });
};

const submitUpdate = () => {
    updateForm.put(`/projects/${props.project.id}`, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            editDialogOpen.value = false;
            updateForm.reset('image');
        },
    });
};

const onUpdateImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const [file] = target.files ?? [];
    updateForm.image = file ?? null;
};

const onReceiptChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const [file] = target.files ?? [];
    form.transfer_receipt = file ?? null;
};

const progressWidth = (value: number) => `${Math.min(100, Math.max(0, value))}%`;
const brokenImages = ref(new Set<number>());

const hasImage = computed(
    () => Boolean(props.project.image_url) && !brokenImages.value.has(props.project.id),
);

const markBroken = () => {
    brokenImages.value.add(props.project.id);
};

</script>

<template>
    <Head :title="project.name" />

    <div class="relative min-h-screen overflow-hidden bg-[var(--bg)] text-[var(--text)]">
        <PlaceholderPattern class="pointer-events-none absolute inset-0 opacity-[0.03]" />

        <header class="relative z-10 navbar">
            <div class="mx-auto flex w-full max-w-6xl items-center justify-between px-6 py-5">
                <Link :href="home()" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[var(--bg-alt)]">
                        <span class="text-2xl font-semibold tracking-tight text-[var(--text)]">{{ page.props.name[0] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-semibold leading-tight">{{ page.props.name }}</span>
                    </div>
                </Link>

                <nav class="hidden items-center gap-6 text-sm font-medium md:flex">
                    <Link
                        :href="projectsIndex()"
                        class="transition hover:text-[var(--primary-600)]"
                    >{{ translations.projectsTitle }}</Link>
                    <Link
                        v-if="authUser"
                        :href="dashboard()"
                        class="transition hover:text-[var(--primary-600)]"
                    >{{ translations.dashboardTitle }}</Link>
                </nav>

                <div class="flex items-center gap-3">
                    <Button
                        class="btn btn-donate"
                        @click="donationDialogOpen = true"
                    >
                        {{ translations.donate }}
                    </Button>
                    <Dialog v-if="authUser" v-model:open="editDialogOpen">
                        <DialogTrigger as-child>
                            <Button class="btn btn-primary text-white">
                                {{ translations.update_project }}
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="max-w-2xl">
                            <DialogHeader>
                                <DialogTitle>{{ translations.update_project }}</DialogTitle>
                            </DialogHeader>
                            <form @submit.prevent="submitUpdate" class="space-y-4">
                                <div class="grid gap-2">
                                    <Label for="edit_name">{{ translations.fields.name }}</Label>
                                    <Input
                                        id="edit_name"
                                        v-model="updateForm.name"
                                        type="text"
                                        required
                                        :class="{ 'border-destructive': updateForm.errors.name }"
                                    />
                                    <InputError :message="updateForm.errors.name" />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="edit_description">{{ translations.fields.description }}</Label>
                                    <textarea
                                        id="edit_description"
                                        v-model="updateForm.description"
                                        rows="4"
                                        required
                                        class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                    />
                                    <InputError :message="updateForm.errors.description" />
                                </div>
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="grid gap-2">
                                        <Label for="edit_target">{{ translations.fields.target_amount }}</Label>
                                        <Input
                                            id="edit_target"
                                            v-model="updateForm.target_amount"
                                            type="number"
                                            min="1"
                                            step="0.01"
                                            required
                                            :class="{ 'border-destructive': updateForm.errors.target_amount }"
                                        />
                                        <InputError :message="updateForm.errors.target_amount" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="edit_start_date">{{ translations.fields.start_date }}</Label>
                                        <Input
                                            id="edit_start_date"
                                            v-model="updateForm.start_date"
                                            type="date"
                                            required
                                            :class="{ 'border-destructive': updateForm.errors.start_date }"
                                        />
                                        <InputError :message="updateForm.errors.start_date" />
                                    </div>
                                </div>
                                <div class="grid gap-2">
                                    <Label for="edit_image">{{ translations.fields.image }}</Label>
                                    <Input
                                        id="edit_image"
                                        type="file"
                                        accept="image/*"
                                        @change="onUpdateImageChange"
                                    />
                                    <InputError :message="updateForm.errors.image" />
                                </div>
                                <DialogFooter class="gap-2">
                                    <Button
                                        type="button"
                                        variant="outline"
                                        :disabled="updateForm.processing"
                                        @click="editDialogOpen = false"
                                    >
                                        {{ translations.cancel }}
                                    </Button>
                                    <Button
                                        type="submit"
                                        :disabled="updateForm.processing"
                                        class="btn btn-primary text-white"
                                    >
                                        {{ translations.save_project }}
                                    </Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>
            </div>
        </header>

        <main class="relative z-10">
            <section class="mx-auto w-full max-w-6xl px-6 py-12">
                <div class="grid gap-6 lg:grid-cols-3">
                    <Card class="lg:col-span-2 border-[var(--border)] bg-[var(--surface)]">
                        <CardHeader class="space-y-4">
                            <CardTitle class="text-3xl font-semibold">
                                {{ project.name }}
                            </CardTitle>
                            <div class="flex flex-wrap items-center gap-3 text-sm text-[var(--text-muted)]">
                                <span
                                    class="inline-flex items-center rounded-full border px-3 py-1 uppercase tracking-wide"
                                    :class="{
                                        'border-[var(--success)] text-[var(--success)]': project.status === 'completed',
                                        'border-[var(--warning)] text-[var(--warning)]': project.status === 'in_progress',
                                        'border-[var(--primary)] text-[var(--primary)]': project.status === 'open',
                                    }"
                                >
                                    {{ translations.status_labels[project.status] }}
                                </span>
                                <span>
                                    {{ translations.target_label }}:
                                    {{ formatNumber(project.target_amount) }}
                                </span>
                                <span>
                                    {{ translations.collected_label }}:
                                    {{ formatNumber(project.collected_amount) }}
                                </span>
                            </div>

                            <div class="relative aspect-video overflow-hidden rounded-2xl bg-[var(--bg-alt)]">
                                <img
                                    v-if="hasImage"
                                    :src="project.image_url"
                                    :alt="project.name"
                                    class="h-full w-full object-cover"
                                    @error="markBroken"
                                />
                                <div
                                    v-else
                                    class="project-fallback project-fallback-pattern flex h-full w-full flex-col items-center justify-center gap-2 text-lg font-semibold"
                                >
                                    <span class="text-2xl tracking-widest">
                                        {{ project.name.slice(0, 2).toUpperCase() }}
                                    </span>
                                    <span class="text-sm font-medium text-[var(--text-muted)]">
                                        {{ project.name }}
                                    </span>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent class="space-y-6 text-[var(--text-muted)]">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm font-medium">
                                    <span>{{ translations.progress_label }}</span>
                                    <span>{{ project.progress }}%</span>
                                </div>
                                <div class="progress h-3 w-full">
                                    <div
                                        class="bar h-3 transition-all"
                                        :style="{ width: progressWidth(project.progress) }"
                                    />
                                </div>
                            </div>

                            <p class="text-sm leading-relaxed text-[var(--text-muted)]">
                                {{ project.description }}
                            </p>

                            <div class="grid gap-2 text-sm text-[var(--text-muted)] md:grid-cols-2">
                                <div>
                                    <span class="font-medium text-[var(--text)]">{{ translations.target_label }}: </span>
                                    {{ formatNumber(project.target_amount) }}
                                </div>
                                <div>
                                    <span class="font-medium text-[var(--text)]">{{ translations.collected_label }}: </span>
                                    {{ formatNumber(project.collected_amount) }}
                                </div>
                                <div v-if="project.start_date">
                                    <span class="font-medium text-[var(--text)]">{{ translations.fields.start_date }}: </span>
                                    {{ project.start_date }}
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter class="justify-end">
                            <Dialog v-model:open="donationDialogOpen">
                                <DialogTrigger as-child>
                                    <Button class="btn btn-donate">
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
                                            <Label for="method">طريقة التبرع</Label>
                                            <select
                                                id="method"
                                                v-model="form.method"
                                                class="rounded-md border border-[var(--border)] bg-[var(--surface)] px-3 py-2 text-sm text-[var(--text)] shadow-sm"
                                            >
                                                <option value="bank">بنك</option>
                                                <option value="cash">نقد</option>
                                            </select>
                                            <InputError :message="form.errors.method" />
                                        </div>
                                        <div class="grid gap-2" v-if="form.method === 'cash'">
                                            <Label for="cash_description">وصف التسليم النقدي</Label>
                                            <Input
                                                id="cash_description"
                                                v-model="form.cash_description"
                                                placeholder="من استلم منك المبلغ ؟"
                                            />
                                            <InputError :message="form.errors.cash_description" />
                                        </div>
                                        <div class="grid gap-2">
                                            <Label for="transfer_receipt">إيصال التحويل</Label>
                                            <Input id="transfer_receipt" type="file" accept="image/*,.pdf" @change="onReceiptChange" />
                                            <InputError :message="form.errors.transfer_receipt" />
                                        </div>
                                        <Label class="flex items-center gap-2">
                                            <Checkbox v-model:checked="form.anonymous" />
                                            <span>{{ translations.anonymous_label }}</span>
                                        </Label>
                                        <DialogFooter class="gap-2">
                                            <Button
                                                type="button"
                                                variant="outline"
                                                :disabled="form.processing"
                                                @click="donationDialogOpen = false"
                                                class="border-[var(--border)] bg-transparent text-[var(--text)] hover:bg-[var(--bg-alt)]"
                                            >
                                                {{ translations.cancel }}
                                            </Button>
                                            <Button type="submit" :disabled="form.processing" class="btn btn-donate">
                                                {{ translations.donate }}
                                            </Button>
                                        </DialogFooter>
                                    </form>
                                </DialogContent>
                            </Dialog>
                        </CardFooter>
                    </Card>

                    <Card class="lg:col-span-1 border-[var(--border)] bg-[var(--surface)]">
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
                                    class="rounded-2xl border border-[var(--border)] bg-[var(--surface-2)] p-4 text-sm shadow-sm"
                                >
                                    <div class="flex items-center justify-between font-medium text-[var(--text)]">
                                        <span>
                                            {{
                                                donation.anonymous
                                                    ? translations.anonymous_label
                                                    : donation.donor_name ?? translations.anonymous_label
                                            }}
                                        </span>
                                        <span class="amount">{{ formatNumber(donation.amount) }}</span>
                                    </div>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs text-[var(--text-muted)]">
                                        <span>{{ donation.method }}</span>
                                        <span>&middot;</span>
                                        <span>{{ new Date(donation.created_at).toLocaleString() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-else
                                class="rounded-2xl border border-dashed border-[var(--border)] bg-[var(--surface-2)] p-6 text-center text-sm text-[var(--text-muted)]"
                            >
                                {{ translations.donations_empty }}
                            </div>
                        </CardContent>
                    </Card>
                </div>

            </section>
        </main>
    </div>
</template>
