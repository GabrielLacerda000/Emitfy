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

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
};
