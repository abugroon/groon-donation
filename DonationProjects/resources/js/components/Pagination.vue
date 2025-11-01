<template>
    <nav v-if="meta.total > meta.per_page" class="mt-8 flex justify-center">
        <ul class="inline-flex items-center space-x-1 rounded-md border border-gray-200 bg-white px-2 py-1 text-sm rtl:space-x-reverse">
            <li v-for="link in links" :key="link.label">
                <span
                    v-if="!link.url"
                    class="px-3 py-1 text-gray-400"
                    v-html="link.label"
                />
                <Link
                    v-else
                    :href="link.url"
                    class="rounded px-3 py-1"
                    :class="link.active ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100'"
                    v-html="link.label"
                />
            </li>
        </ul>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    meta: {
        type: Object,
        required: true,
    },
    links: {
        type: Array,
        required: true,
    },
});
</script>
