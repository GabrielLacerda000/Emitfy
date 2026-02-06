import type { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}

export function formatCurrency(
    amount: string | number,
    locale: string = 'pt-BR',
    currency: string = 'BRL'
): string {
    const numAmount = typeof amount === 'string' ? parseFloat(amount) : amount;
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency,
    }).format(numAmount);
}

// Convenience helper for BRL
export function formatBRL(amount: string | number): string {
    return formatCurrency(amount, 'pt-BR', 'BRL');
}

export function formatDate(date: string | Date): string {
    const dateObj = typeof date === 'string' ? new Date(date) : date;
    return dateObj.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

export function isOverdue(dueDate: string, paidAt: string | null): boolean {
    if (paidAt) return false;
    return new Date(dueDate) < new Date();
}
