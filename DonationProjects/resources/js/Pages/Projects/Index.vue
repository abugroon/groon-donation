<template>
    <div class="mx-auto max-w-7xl py-12 px-6">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-800">{{ $t('projects.title') }}</h2>
            <Link
                v-if="auth?.user"
                :href="route('projects.create')"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500"
            >
                {{ $t('projects.create') }}
            </Link>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="project in projects.data"
                :key="project.id"
                class="rounded-lg bg-white p-6 shadow-sm border border-gray-200"
            >
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">{{ project.name }}</h3>
                    <span
                        class="rounded-full px-3 py-1 text-xs font-medium"
                        :class="statusClass(project.status)"
                    >
                        {{ $t(`projects.status.${project.status}`) }}
                    </span>
                </div>
                <div v-if="project.image" class="mb-4">
                    <img :src="project.image" :alt="project.name" class="h-40 w-full rounded-md object-cover" />
                </div>
                <p class="text-sm text-gray-600 mb-4 overflow-hidden text-ellipsis" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                    {{ project.description }}
                </p>
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>{{ $t('projects.fields.collected_amount') }}: {{ formatCurrency(project.collected_amount) }}</span>
                        <span>{{ $t('projects.fields.target_amount') }}: {{ formatCurrency(project.target_amount) }}</span>
                    </div>
                    <div class="mt-2">
                        <div class="h-2 w-full rounded-full bg-gray-200">
                            <div
                                class="h-2 rounded-full bg-gradient-to-r from-emerald-400 to-emerald-600"
                                :style="{ width: `${Math.min(project.progress, 100)}%` }"
                            ></div>
                        </div>
                        <p class="mt-2 text-right text-sm font-medium text-emerald-600">{{ project.progress }}%</p>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <Link
                        :href="route('projects.show', project.id)"
                        class="text-sm font-medium text-indigo-600 hover:text-indigo-500"
                    >
                        {{ $t('projects.details') }}
                    </Link>
                </div>
            </div>
        </div>

        <Pagination v-if="projects.meta" :meta="projects.meta" :links="projects.links" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Pagination from '../../components/Pagination.vue';
import useTranslations from '../../composables/useTranslations';

const page = usePage();
const auth = computed(() => page.props.auth ?? null);
const { t } = useTranslations(page);
const $t = t;

const props = defineProps({
    projects: {
        type: Object,
        required: true,
    },
});

const statusClass = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-emerald-100 text-emerald-700';
        case 'in_progress':
            return 'bg-amber-100 text-amber-700';
        default:
            return 'bg-sky-100 text-sky-700';
    }
};

const formatCurrency = (value) => new Intl.NumberFormat(undefined, {
    style: 'currency',
    currency: 'SAR',
}).format(Number(value ?? 0));
</script>
