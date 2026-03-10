import type { Features } from '@/types';

export function canSendInvoice(features: Features | null): boolean {
    return features?.canSendInvoice ?? false;
}

export function canViewPdf(features: Features | null): boolean {
    return features?.canViewPdf ?? false;
}

export function canChangeStatus(features: Features | null): boolean {
    return features?.canChangeStatus ?? false;
}
