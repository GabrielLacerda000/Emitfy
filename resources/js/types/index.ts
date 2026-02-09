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

export interface InvoiceItem {
    id?: number;
    description: string;
    quantity: number;
    unit_price: number;
    total: number;
}

export interface InvoiceFormData {
    client_id: string | null;
    issue_date: string;
    due_date: string;
    tax: number;
    notes: string;
    status: InvoiceStatus;
    items: InvoiceItem[];
}

export interface Invoice {
    id: number;
    user_id: number;
    client_id: string;
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
    items?: InvoiceItem[];
}

export interface DashboardStats {
    totalOutstanding: string;
    totalOverdue: string;
    overdueCount: number;
    dueSoonCount: number;
    dueSoonTotal: string;
}

export interface DashboardData {
    stats: DashboardStats;
    recentInvoices: Invoice[];
    recentClients: (Client & { invoices_count: number })[];
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
};
