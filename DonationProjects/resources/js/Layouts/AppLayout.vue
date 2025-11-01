<template>
    <div class="min-h-screen bg-gray-100" :class="{ 'rtl': currentLocale === 'ar', 'ltr': currentLocale !== 'ar' }">
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex items-center justify-between">
                <h1 class="text-3xl font-bold leading-tight text-gray-900">
                    {{ $t('app.name') }}
                </h1>
                <nav class="flex space-x-4 rtl:space-x-reverse">
                    <Link :href="route('dashboard')" class="text-sm font-medium text-gray-700 hover:text-indigo-600" v-if="auth?.user">
                        {{ $t('navigation.dashboard') }}
                    </Link>
                    <Link :href="route('projects.index')" class="text-sm font-medium text-gray-700 hover:text-indigo-600" v-if="auth?.user">
                        {{ $t('navigation.projects') }}
                    </Link>
                    <Link :href="route('logout')" method="post" as="button" class="text-sm font-medium text-red-600 hover:text-red-700" v-if="auth?.user">
                        {{ $t('auth.logout') }}
                    </Link>
                    <Link :href="route('login')" class="text-sm font-medium text-indigo-600" v-else>
                        {{ $t('auth.login') }}
                    </Link>
                </nav>
            </div>
        </header>

        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import useTranslations from '../composables/useTranslations';

const page = usePage();
const auth = computed(() => page.props.auth ?? null);
const currentLocale = computed(() => page.props.locale ?? 'ar');

const { t } = useTranslations(page);

const $t = t;
</script>

<style scoped>
.rtl {
    direction: rtl;
}

.ltr {
    direction: ltr;
}
</style>
