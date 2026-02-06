export * from './auth';
export * from './navigation';
export * from './ui';

import type { Auth } from './auth';

export interface Client {
    id: number;
    user_id: number;
    name: string;
    email: string;
    company_name: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
}

export type InvoiceStatus = 'draft' | 'sent' | 'paid' | 'overdue';

export interface Invoice {
    id: number;
    user_id: number;
    client_id: number;
    number: string;
    status: InvoiceStatus;
    issue_date: string;
    due_date: string;
    subtotal: string;
    tax: string;
    total: string;
    notes: string | null;
    public_token: string;
    sent_at: string | null;
    paid_at: string | null;
    created_at: string;
    updated_at: string;
    client: {
        id: number;
        name: string;
        email: string;
        company_name: string | null;
    };
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
};
