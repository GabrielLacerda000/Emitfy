export * from './auth';
export * from './navigation';
export * from './ui';

import type { Auth } from './auth';

export interface Client {
    id: string;
    user_id: number;
    name: string;
    email: string;
    company_name: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
}

export interface ClientStats {
    totalPaid: string;
    totalPaidCount: number;
    totalPending: string;
    totalPendingCount: number;
    totalOverdue: string;
    totalOverdueCount: number;
    totalDraft: string;
    totalDraftCount: number;
    lastInvoiceSent: Invoice | null;
}

export interface PaginatedInvoices {
    data: Invoice[];
    links: PaginationLink[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
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
    paid_at: string;
    items: InvoiceItem[];
}

export interface CreateInvoiceData {
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
    tax: number;
    total: string;
    notes: string | null;
    public_token: string;
    sent_at: string | null;
    paid_at: string | null;
    created_at: string;
    updated_at: string;
    client: {
        id: string;
        name: string;
        email: string;
        company_name: string | null;
    };
    items?: InvoiceItem[];
}

export interface DashboardStats {
    totalOutstanding: string;
    totalPaid: string;
    totalPaidCount: number;
    totalOverdue: string;
    overdueCount: number;
    dueSoonCount: number;
    dueSoonTotal: string;
}

export interface MonthlyRevenueData {
    labels: string[];
    data: number[];
}

export interface DashboardData {
    stats: DashboardStats;
    recentInvoices: Invoice[];
    recentClients: (Client & { invoices_count: number })[];
    monthlyRevenue: MonthlyRevenueData;
}

export interface FlashMessage {
    message: string | null;
    type: 'success' | 'error' | 'info' | 'warning';
}

export interface Features {
    canSendInvoice: boolean;
    canViewPdf: boolean;
    canChangeStatus: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    flash: FlashMessage;
    features: Features | null;
    [key: string]: unknown;
};
