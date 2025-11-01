<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md rounded-lg bg-white p-8 shadow">
            <h2 class="mb-6 text-center text-2xl font-semibold text-gray-800">{{ $t('auth.login') }}</h2>
            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                    <input v-model="form.email" type="email" class="input-control" required autofocus />
                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">كلمة المرور</label>
                    <input v-model="form.password" type="password" class="input-control" required autocomplete="current-password" />
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 rtl:space-x-reverse text-sm text-gray-600">
                        <input v-model="form.remember" type="checkbox" class="h-4 w-4 rounded border-gray-300" />
                        <span>{{ $t('auth.remember_me') }}</span>
                    </label>
                    <Link :href="route('register')" class="text-sm text-indigo-600 hover:text-indigo-500">{{ $t('auth.register') }}</Link>
                </div>
                <button type="submit" class="btn-primary w-full">{{ $t('auth.login') }}</button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import useTranslations from '../../composables/useTranslations';

const page = usePage();
const { t } = useTranslations(page);
const $t = t;

defineOptions({
    layout: null,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'));
};
</script>

<style scoped>
.input-control {
    @apply w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500;
}

.btn-primary {
    @apply rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500;
}
</style>
