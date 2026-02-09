import type { InvoiceStatus } from '@/types';

export interface StatusConfig {
    variant: 'secondary' | 'default' | 'outline' | 'destructive';
    label: string;
    class?: string;
}

export function getStatusConfig(status: InvoiceStatus): StatusConfig {
    const configs: Record<InvoiceStatus, StatusConfig> = {
        draft: { variant: 'secondary', label: 'Draft' },
        sent: { variant: 'default', label: 'Sent' },
        paid: {
            variant: 'outline',
            label: 'Paid',
            class: 'border-green-600 text-green-700 dark:border-green-500 dark:text-green-400',
        },
        overdue: { variant: 'destructive', label: 'Overdue' },
    };

    return configs[status];
}
