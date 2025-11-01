<template>
    <div class="mx-auto max-w-3xl py-12 px-6">
        <div class="rounded-lg bg-white p-8 shadow">
            <h2 class="mb-6 text-2xl font-semibold text-gray-800">{{ $t('projects.edit') }}</h2>
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('projects.fields.name') }}</label>
                    <input v-model="form.name" type="text" class="input-control" required />
                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('projects.fields.description') }}</label>
                    <textarea v-model="form.description" rows="4" class="input-control" required></textarea>
                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('projects.fields.target_amount') }}</label>
                        <input v-model.number="form.target_amount" type="number" min="1" step="0.5" class="input-control" required />
                        <p v-if="form.errors.target_amount" class="mt-1 text-sm text-red-600">{{ form.errors.target_amount }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('projects.fields.status') }}</label>
                        <select v-model="form.status" class="input-control">
                            <option value="open">{{ $t('projects.status.open') }}</option>
                            <option value="in_progress">{{ $t('projects.status.in_progress') }}</option>
                            <option value="completed">{{ $t('projects.status.completed') }}</option>
                        </select>
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('projects.fields.start_date') }}</label>
                        <input v-model="form.start_date" type="date" class="input-control" required />
                        <p v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">{{ form.errors.start_date }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('projects.fields.end_date') }}</label>
                        <input v-model="form.end_date" type="date" class="input-control" required />
                        <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">{{ form.errors.end_date }}</p>
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('projects.fields.collected_amount') }}</label>
                        <input v-model.number="form.collected_amount" type="number" min="0" step="0.5" class="input-control" />
                        <p v-if="form.errors.collected_amount" class="mt-1 text-sm text-red-600">{{ form.errors.collected_amount }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('projects.fields.image') }}</label>
                        <input v-model="form.image" type="text" class="input-control" />
                        <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{ form.errors.image }}</p>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 rtl:space-x-reverse">
                    <Link :href="route('projects.index')" class="btn-secondary">{{ $t('common.cancel') }}</Link>
                    <button type="submit" class="btn-primary">{{ $t('common.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import useTranslations from '../../composables/useTranslations';

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const { t } = useTranslations(page);
const $t = t;

const form = useForm({
    name: props.project.name,
    description: props.project.description,
    target_amount: props.project.target_amount,
    status: props.project.status,
    start_date: props.project.start_date,
    end_date: props.project.end_date,
    image: props.project.image,
    collected_amount: props.project.collected_amount,
});

const submit = () => {
    form.put(route('projects.update', props.project.id));
};
</script>

<style scoped>
.input-control {
    @apply w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500;
}

.btn-primary {
    @apply rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500;
}

.btn-secondary {
    @apply rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300;
}
</style>
