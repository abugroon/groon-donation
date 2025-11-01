import { computed } from 'vue';

export default function useTranslations(page) {
    const translations = computed(() => page.props.translations ?? {});

    const t = (key, fallback = '') => {
        const keys = key.split('.');
        let current = translations.value;
        for (const k of keys) {
            if (current && Object.prototype.hasOwnProperty.call(current, k)) {
                current = current[k];
            } else {
                return fallback || key;
            }
        }

        return current;
    };

    return { t };
}
