import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { formatCurrency } from '@/lib/utils';

export type Locale = 'en' | 'pt-BR';

const DEFAULT_LOCALE: Locale = 'pt-BR';

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }
    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const getStoredLocale = (): Locale | null => {
    if (typeof window === 'undefined') {
        return null;
    }
    return localStorage.getItem('locale') as Locale | null;
};

export function initializeLocale(): void {
    if (typeof window === 'undefined') {
        return;
    }
    const saved = getStoredLocale();
    if (!saved) {
        localStorage.setItem('locale', DEFAULT_LOCALE);
        setCookie('locale', DEFAULT_LOCALE);
    }
}

const locale = ref<Locale>(
    (typeof window !== 'undefined'
        ? (localStorage.getItem('locale') as Locale | null)
        : null) ?? DEFAULT_LOCALE,
);

export function useLocale() {
    const { locale: i18nLocale } = useI18n();

    onMounted(() => {
        const saved = getStoredLocale();
        if (saved) {
            locale.value = saved;
            i18nLocale.value = saved;
        }
    });

    function setLocale(value: Locale) {
        locale.value = value;
        i18nLocale.value = value;
        localStorage.setItem('locale', value);
        setCookie('locale', value);
    }

    return {
        locale,
        setLocale,
    };
}

/**
 * Returns a reactive currency formatter that follows the current locale.
 * When called in a template, Vue tracks locale.value as a dependency
 * and will re-render when the locale changes.
 */
export function useFormatCurrency() {
    return (amount: string | number): string => {
        if (locale.value === 'en') {
            return formatCurrency(amount, 'en-US', 'USD');
        }
        return formatCurrency(amount, 'pt-BR', 'BRL');
    };
}
