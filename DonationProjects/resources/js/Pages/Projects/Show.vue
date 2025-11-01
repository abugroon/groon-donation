<template>
    <div class="mx-auto max-w-5xl py-12 px-6">
        <div class="mb-8 flex flex-col gap-6 lg:flex-row">
            <div class="flex-1 rounded-lg bg-white p-6 shadow">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ project.name }}</h2>
                <p class="text-gray-600 leading-relaxed mb-6">{{ project.description }}</p>

                <div class="space-y-4">
                    <div>
                        <span class="text-sm text-gray-500">{{ $t('projects.fields.target_amount') }}</span>
                        <p class="text-lg font-semibold text-gray-800">{{ formatCurrency(project.target_amount) }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">{{ $t('projects.fields.collected_amount') }}</span>
                        <p class="text-lg font-semibold text-emerald-600">{{ formatCurrency(project.collected_amount) }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">{{ $t('projects.fields.progress') }}</span>
                        <div class="mt-2 h-3 w-full rounded-full bg-gray-200">
                            <div
                                class="h-3 rounded-full bg-gradient-to-r from-indigo-500 to-emerald-500"
                                :style="{ width: `${Math.min(project.progress, 100)}%` }"
                            ></div>
                        </div>
                        <p class="mt-2 text-sm font-semibold text-indigo-600">{{ project.progress }}%</p>
                    </div>
                    <div class="flex gap-4 text-sm text-gray-600">
                        <div>
                            <span class="block text-gray-500">{{ $t('projects.fields.start_date') }}</span>
                            <span class="font-medium">{{ project.start_date }}</span>
                        </div>
                        <div>
                            <span class="block text-gray-500">{{ $t('projects.fields.end_date') }}</span>
                            <span class="font-medium">{{ project.end_date }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <aside class="w-full lg:w-80 space-y-4">
                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $t('donations.title') }}</h3>
                    <ul class="space-y-3 max-h-80 overflow-y-auto">
                        <li
                            v-for="donation in donations"
                            :key="donation.id"
                            class="rounded border border-gray-100 p-3"
                        >
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-800">
                                    {{ donation.is_anonymous ? $t('donations.fields.is_anonymous') : donation.donor_name }}
                                </span>
                                <span class="text-emerald-600 font-semibold">
                                    {{ formatCurrency(donation.amount) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ donation.created_at }}</p>
                        </li>
                    </ul>
                </div>
                <div class="rounded-lg bg-white p-6 shadow">
                    <button
                        class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500"
                        @click="showDonationForm = true"
                    >
                        {{ $t('donations.actions.donate_now') }}
                    </button>
                </div>
            </aside>
        </div>

        <Modal :show="showDonationForm" @close="showDonationForm = false">
            <template #title>{{ $t('donations.actions.donate_now') }}</template>
            <form @submit.prevent="submitDonation" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('donations.fields.donor_name') }}</label>
                    <input v-model="form.donor_name" type="text" class="input-control" />
                    <p v-if="form.errors.donor_name" class="mt-1 text-sm text-red-600">{{ form.errors.donor_name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('donations.fields.amount') }}</label>
                    <input v-model.number="form.amount" type="number" min="1" step="0.5" class="input-control" required />
                    <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">{{ form.errors.amount }}</p>
                </div>
                <div class="flex items-center">
                    <input v-model="form.is_anonymous" type="checkbox" id="anonymous" class="h-4 w-4 rounded border-gray-300" />
                    <label for="anonymous" class="ml-2 text-sm text-gray-600">{{ $t('donations.fields.is_anonymous') }}</label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $t('donations.fields.payment_method') }}</label>
                    <input v-model="form.payment_method" type="text" class="input-control" />
                    <p v-if="form.errors.payment_method" class="mt-1 text-sm text-red-600">{{ form.errors.payment_method }}</p>
                </div>
                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                    <button type="button" class="btn-secondary" @click="showDonationForm = false">{{ $t('common.cancel') }}</button>
                    <button type="submit" class="btn-primary">{{ $t('common.save') }}</button>
                </div>
            </form>
        </Modal>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import useTranslations from '../../composables/useTranslations';
import Modal from '../../components/Modal.vue';
import { usePage } from '@inertiajs/vue3';

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    donations: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const { t } = useTranslations(page);
const $t = t;

const showDonationForm = ref(false);

const form = useForm({
    project_id: props.project.id,
    donor_name: '',
    amount: 50,
    is_anonymous: false,
    payment_method: '',
});

const submitDonation = () => {
    form.post(route('projects.donations.store', props.project.id), {
        onSuccess: () => {
            showDonationForm.value = false;
            form.reset('donor_name', 'amount', 'is_anonymous', 'payment_method');
            form.project_id = props.project.id;
        },
    });
};

const formatCurrency = (value) => new Intl.NumberFormat(undefined, {
    style: 'currency',
    currency: 'SAR',
}).format(Number(value ?? 0));
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
