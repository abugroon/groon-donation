<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { BreadcrumbItemType } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import {
    create as createProjectRoute,
    index as projectsIndex,
    store as storeProject,
} from '@/routes/projects';
import { dashboard } from '@/routes';
import { computed, ref } from 'vue';

interface Translations {
    title: string;
    save: string;
    fields: Record<string, string>;
    listTitle: string;
    cancel: string;
    dashboardTitle: string;
}

const props = defineProps<{
    translations: Translations;
}>();

const breadcrumbs = computed<BreadcrumbItemType[]>(() => [
    { title: props.translations.dashboardTitle, href: dashboard() },
    { title: props.translations.listTitle, href: projectsIndex() },
    { title: props.translations.title, href: createProjectRoute() },
]);

const previewUrl = ref<string | null>(null);

const form = useForm({
    name: '',
    description: '',
    target_amount: '',
    start_date: '',
    end_date: '',
    image: null as File | null,
});

const submit = () => {
    form.post(storeProject(), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            previewUrl.value = null;
        },
    });
};

const onFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const [file] = target.files ?? [];

    form.image = file ?? null;

    if (file) {
        previewUrl.value = URL.createObjectURL(file);
    } else {
        previewUrl.value = null;
    }
};
</script>

<template>
    <Head :title="translations.title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <Card class="mx-auto w-full max-w-4xl">
                <CardHeader>
                    <CardTitle class="text-2xl font-semibold">
                        {{ translations.title }}
                    </CardTitle>
                </CardHeader>
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    <CardContent class="grid gap-6">
                        <div class="grid gap-2">
                            <Label for="name">{{ translations.fields.name }}</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                autocomplete="off"
                                :class="{ 'border-destructive': form.errors.name }"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">
                                {{ translations.fields.description }}
                            </Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="4"
                                required
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="target_amount">
                                    {{ translations.fields.target_amount }}
                                </Label>
                                <Input
                                    id="target_amount"
                                    v-model="form.target_amount"
                                    type="number"
                                    min="1"
                                    step="0.01"
                                    required
                                    :class="{ 'border-destructive': form.errors.target_amount }"
                                />
                                <InputError :message="form.errors.target_amount" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="image">{{ translations.fields.image }}</Label>
                                <Input
                                    id="image"
                                    type="file"
                                    accept="image/*"
                                    @change="onFileChange"
                                />
                                <InputError :message="form.errors.image" />
                                <img
                                    v-if="previewUrl"
                                    :src="previewUrl"
                                    :alt="form.name"
                                    class="mt-2 h-32 w-full rounded-md object-cover"
                                />
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="start_date">
                                    {{ translations.fields.start_date }}
                                </Label>
                                <Input
                                    id="start_date"
                                    v-model="form.start_date"
                                    type="date"
                                    required
                                    :class="{ 'border-destructive': form.errors.start_date }"
                                />
                                <InputError :message="form.errors.start_date" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="end_date">
                                    {{ translations.fields.end_date }}
                                </Label>
                                <Input
                                    id="end_date"
                                    v-model="form.end_date"
                                    type="date"
                                    required
                                    :class="{ 'border-destructive': form.errors.end_date }"
                                />
                                <InputError :message="form.errors.end_date" />
                            </div>
                        </div>
                    </CardContent>

                    <CardFooter class="flex justify-end gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="form.processing"
                            @click="form.reset(); previewUrl.value = null;"
                        >
                            {{ translations.cancel }}
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                        >
                            {{ translations.save }}
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
