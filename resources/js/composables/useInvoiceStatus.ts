import { useI18n } from 'vue-i18n';
import type { InvoiceStatus } from '@/types';

export interface StatusConfig {
    variant: 'secondary' | 'default' | 'outline' | 'destructive';
    label: string;
    class?: string;
}

export function useInvoiceStatus() {
    const { t } = useI18n();

    function getStatusConfig(status: InvoiceStatus): StatusConfig {
        const configs: Record<InvoiceStatus, StatusConfig> = {
            draft: { variant: 'secondary', label: t('invoices.status.draft') },
            sent: { variant: 'default', label: t('invoices.status.sent') },
            paid: {
                variant: 'outline',
                label: t('invoices.status.paid'),
                class: 'border-green-600 text-green-700 dark:border-green-500 dark:text-green-400',
            },
            overdue: { variant: 'destructive', label: t('invoices.status.overdue') },
        };

        return configs[status];
    }

    return { getStatusConfig };
}
