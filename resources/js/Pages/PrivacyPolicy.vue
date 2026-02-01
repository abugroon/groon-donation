<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

interface BulletSection {
    title: string;
    items: string[];
}

interface Translations {
    title: string;
    intro: string;
    last_updated: string;
    summary_title: string;
    data_collected_title: string;
    data_collected: BulletSection[];
    data_use_title: string;
    data_use: BulletSection[];
    play_requirements_title: string;
    play_requirements: string[];
    sharing_title: string;
    sharing: string[];
    rights_title: string;
    rights: string[];
    retention_title: string;
    retention: string[];
    security_title: string;
    security: string[];
    contact_title: string;
    contact_description: string;
    contact_email_label: string;
    contact_email: string;
    contact_phone_label: string;
    contact_phone: string;
}

const { translations } = defineProps<{
    translations: Translations;
}>();
</script>

<template>
    <Head :title="translations.title" />

    <div class="min-h-screen bg-[var(--bg)] text-[var(--text)]">
        <div class="mx-auto flex max-w-6xl flex-col gap-8 px-6 py-12">
            <header class="space-y-4">
                <Link
                    href="/"
                    class="inline-flex items-center gap-2 text-sm text-[var(--primary)] transition hover:text-[var(--primary-600)]"
                >
                    ← {{ $page.props.name }}
                </Link>
                <div class="space-y-3">
                    <p class="text-xs uppercase tracking-[0.28em] text-[var(--primary-600)]">{{ translations.summary_title }}</p>
                    <h1 class="text-3xl font-bold leading-tight sm:text-4xl">{{ translations.title }}</h1>
                    <p class="text-sm text-[var(--text-muted)]">{{ translations.last_updated }}</p>
                    <p class="max-w-4xl text-[var(--text-muted)]">{{ translations.intro }}</p>
                </div>
            </header>

            <section class="space-y-4">
                <h2 class="text-xl font-semibold">{{ translations.data_collected_title }}</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    <div
                        v-for="section in translations.data_collected"
                        :key="section.title"
                        class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-[var(--shadow-soft)]"
                    >
                        <h3 class="text-lg font-semibold">{{ section.title }}</h3>
                        <ul class="mt-3 space-y-2 text-sm text-[var(--text-muted)]">
                            <li v-for="(item, index) in section.items" :key="index" class="flex gap-2">
                                <span class="text-[var(--primary)]">•</span>
                                <span>{{ item }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="space-y-4">
                <h2 class="text-xl font-semibold">{{ translations.data_use_title }}</h2>
                <div class="grid gap-6 md:grid-cols-2">
                    <div
                        v-for="section in translations.data_use"
                        :key="section.title"
                        class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-[var(--shadow-soft)]"
                    >
                        <h3 class="text-lg font-semibold">{{ section.title }}</h3>
                        <ul class="mt-3 space-y-2 text-sm text-[var(--text-muted)]">
                            <li v-for="(item, index) in section.items" :key="index" class="flex gap-2">
                                <span class="text-[var(--primary)]">•</span>
                                <span>{{ item }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-[var(--shadow-soft)]">
                    <h2 class="text-lg font-semibold">{{ translations.play_requirements_title }}</h2>
                    <ul class="mt-3 space-y-2 text-sm text-[var(--text-muted)]">
                        <li v-for="(item, index) in translations.play_requirements" :key="index" class="flex gap-2">
                            <span class="text-[var(--primary)]">•</span>
                            <span>{{ item }}</span>
                        </li>
                    </ul>
                </div>
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-[var(--shadow-soft)]">
                    <h2 class="text-lg font-semibold">{{ translations.sharing_title }}</h2>
                    <ul class="mt-3 space-y-2 text-sm text-[var(--text-muted)]">
                        <li v-for="(item, index) in translations.sharing" :key="index" class="flex gap-2">
                            <span class="text-[var(--primary)]">•</span>
                            <span>{{ item }}</span>
                        </li>
                    </ul>
                </div>
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-[var(--shadow-soft)]">
                    <h2 class="text-lg font-semibold">{{ translations.security_title }}</h2>
                    <ul class="mt-3 space-y-2 text-sm text-[var(--text-muted)]">
                        <li v-for="(item, index) in translations.security" :key="index" class="flex gap-2">
                            <span class="text-[var(--primary)]">•</span>
                            <span>{{ item }}</span>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="grid gap-6 md:grid-cols-2">
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-[var(--shadow-soft)]">
                    <h2 class="text-lg font-semibold">{{ translations.rights_title }}</h2>
                    <ul class="mt-3 space-y-2 text-sm text-[var(--text-muted)]">
                        <li v-for="(item, index) in translations.rights" :key="index" class="flex gap-2">
                            <span class="text-[var(--primary)]">•</span>
                            <span>{{ item }}</span>
                        </li>
                    </ul>
                </div>
                <div class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-6 shadow-[var(--shadow-soft)]">
                    <h2 class="text-lg font-semibold">{{ translations.retention_title }}</h2>
                    <ul class="mt-3 space-y-2 text-sm text-[var(--text-muted)]">
                        <li v-for="(item, index) in translations.retention" :key="index" class="flex gap-2">
                            <span class="text-[var(--primary)]">•</span>
                            <span>{{ item }}</span>
                        </li>
                    </ul>
                </div>
            </section>

            <section class="rounded-3xl border border-[var(--border)] bg-[var(--surface)] p-8 shadow-[var(--shadow-soft)] sm:p-10">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div class="space-y-2">
                            <h2 class="text-xl font-semibold">{{ translations.contact_title }}</h2>
                            <p class="text-sm text-[var(--text-muted)]">{{ translations.contact_description }}</p>
                        </div>
                        <div class="space-y-3 text-sm text-[var(--text-muted)]">
                            <div class="flex items-center gap-2">
                                <span class="text-[var(--text-muted)]">{{ translations.contact_email_label }}:</span>
                                <a
                                    class="text-[var(--primary)] transition hover:text-[var(--primary-600)]"
                                    :href="`mailto:${translations.contact_email}`"
                                >
                                    {{ translations.contact_email }}
                                </a>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[var(--text-muted)]">{{ translations.contact_phone_label }}:</span>
                                <a
                                    class="text-[var(--primary)] transition hover:text-[var(--primary-600)]"
                                    :href="`https://wa.me/${translations.contact_phone.replace('+', '')}`"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    {{ translations.contact_phone }}
                                </a>
                            </div>
                        </div>
                    </div>
            </section>
        </div>
    </div>
</template>
