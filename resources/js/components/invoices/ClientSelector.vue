<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import ClientController from '@/actions/App/Http/Controllers/ClientController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectSeparator,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { Client } from '@/types';

const page = usePage();

interface Props {
    modelValue: string | null;
    clients: Client[];
    error?: string;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    'update:modelValue': [value: string | null];
    'clientCreated': [client: Client];
}>();

const createDialogOpen = ref(false);
const newClientData = ref({
    name: '',
    email: '',
    company_name: '',
});
const createErrors = ref<Record<string, string>>({});
const creating = ref(false);
const selectedValue = ref<string>('');

// Watch for special "__create__" value
watch(selectedValue, (newValue) => {
    console.log('[ClientSelector] selectedValue changed:', { newValue, type: typeof newValue });
    if (newValue === '__create__') {
        createDialogOpen.value = true;
        // Reset to current modelValue
        selectedValue.value = props.modelValue ?? '';
        return;
    }
    console.log('[ClientSelector] Emitting update:modelValue:', { value: newValue || null, type: typeof (newValue || null) });
    emit('update:modelValue', newValue || null);
});

// Sync with prop changes
watch(() => props.modelValue, (newValue) => {
    console.log('[ClientSelector] props.modelValue changed:', { newValue, type: typeof newValue });
    selectedValue.value = newValue ?? '';
    console.log('[ClientSelector] Updated selectedValue to:', { selectedValue: selectedValue.value, type: typeof selectedValue.value });
}, { immediate: true });

function createClient() {
    creating.value = true;
    createErrors.value = {};

    router.post(
        ClientController.store.url(),
        newClientData.value,
        {
            preserveScroll: true,
            onError: (errors) => {
                createErrors.value = errors;
            },
            onFinish: () => {
                creating.value = false;
            },
        },
    );
}

// Watch for flash client data after successful creation
watch(
    () => page.props.client,
    (newClient) => {
        if (newClient && createDialogOpen.value) {
            emit('update:modelValue', (newClient as Client).id.toString());
            emit('clientCreated', newClient as Client);
            createDialogOpen.value = false;
            newClientData.value = { name: '', email: '', company_name: '' };
        }
    },
);

function formatClientName(client: Client): string {
    if (client.company_name) {
        return `${client.name} - ${client.company_name}`;
    }
    return client.name;
}
</script>

<template>
    <div class="space-y-2">
        <Select v-model="selectedValue">
            <SelectTrigger id="client-select">
                <SelectValue placeholder="Select a client" />
            </SelectTrigger>
            <SelectContent>
                <SelectItem value="__create__" class="font-medium">
                    + Create New Client
                </SelectItem>
                <SelectSeparator />
                <SelectItem
                    v-for="client in clients"
                    :key="client.id"
                    :value="client.id.toString()"
                >
                    {{ formatClientName(client) }}
                </SelectItem>
            </SelectContent>
        </Select>
        <InputError :message="error" />

        <Dialog v-model:open="createDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Create New Client</DialogTitle>
                    <DialogDescription>
                        Add a new client to your account. This client will be available for
                        invoices.
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <div class="grid gap-2">
                        <Label for="new-client-name">Name</Label>
                        <Input
                            id="new-client-name"
                            v-model="newClientData.name"
                            required
                            placeholder="Client name"
                        />
                        <InputError :message="createErrors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="new-client-email">Email</Label>
                        <Input
                            id="new-client-email"
                            v-model="newClientData.email"
                            type="email"
                            required
                            placeholder="client@example.com"
                        />
                        <InputError :message="createErrors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="new-client-company">Company Name</Label>
                        <Input
                            id="new-client-company"
                            v-model="newClientData.company_name"
                            placeholder="Company name (optional)"
                        />
                        <InputError :message="createErrors.company_name" />
                    </div>
                </div>

                <DialogFooter class="gap-2">
                    <Button
                        variant="secondary"
                        @click="createDialogOpen = false"
                        :disabled="creating"
                    >
                        Cancel
                    </Button>
                    <Button @click="createClient" :disabled="creating">
                        Create Client
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
