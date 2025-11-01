<template>
    <div class="mx-auto max-w-7xl py-12 px-6 space-y-10">
        <h2 class="text-2xl font-bold text-gray-800">{{ $t('dashboard.title') }}</h2>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-sm text-gray-500">{{ $t('dashboard.stats.projects') }}</p>
                <p class="mt-2 text-3xl font-semibold text-indigo-600">{{ projectsCount }}</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-sm text-gray-500">{{ $t('dashboard.stats.total_collected') }}</p>
                <p class="mt-2 text-3xl font-semibold text-emerald-600">{{ formatCurrency(totalCollected) }}</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-sm text-gray-500">{{ $t('dashboard.stats.total_target') }}</p>
                <p class="mt-2 text-3xl font-semibold text-sky-600">{{ formatCurrency(totalTarget) }}</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow">
                <p class="text-sm text-gray-500">{{ $t('dashboard.stats.overall_progress') }}</p>
                <p class="mt-2 text-3xl font-semibold text-amber-600">{{ overallProgress }}%</p>
            </div>
        </div>

        <div class="rounded-lg bg-white p-8 shadow">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">{{ $t('dashboard.stats.recent_donations') }}</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">{{ $t('donations.fields.donor_name') }}</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">{{ $t('donations.fields.amount') }}</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">{{ $t('donations.fields.payment_method') }}</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500">{{ $t('donations.fields.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="donation in recentDonations" :key="donation.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-700">
                                {{ donation.is_anonymous ? $t('donations.fields.is_anonymous') : donation.donor_name }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-emerald-600">{{ formatCurrency(donation.amount) }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ donation.payment_method || '-' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ donation.created_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-lg bg-white p-8 shadow">
            <h3 class="mb-4 text-lg font-semibold text-gray-800">{{ $t('dashboard.stats.overall_progress') }}</h3>
            <div class="h-64 w-full">
                <div class="flex h-full items-end space-x-4 rtl:space-x-reverse">
                    <div class="flex-1">
                        <div
                            class="w-full rounded-t bg-indigo-500"
                            :style="{ height: `${Math.min(overallProgress, 100)}%` }"
                        ></div>
                        <p class="mt-2 text-center text-sm text-gray-600">{{ overallProgress }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import useTranslations from '../../composables/useTranslations';

const props = defineProps({
    projectsCount: Number,
    totalCollected: Number,
    totalTarget: Number,
    overallProgress: Number,
    recentDonations: Array,
});

const page = usePage();
const { t } = useTranslations(page);
const $t = t;

const formatCurrency = (value) => new Intl.NumberFormat(undefined, {
    style: 'currency',
    currency: 'SAR',
}).format(Number(value ?? 0));
</script>
