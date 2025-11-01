<template>
    <transition name="fade">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4" @click.self="$emit('close')">
            <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <slot name="title" />
                    </h3>
                    <button class="text-gray-400 hover:text-gray-600" @click="$emit('close')">&times;</button>
                </div>
                <div>
                    <slot />
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
