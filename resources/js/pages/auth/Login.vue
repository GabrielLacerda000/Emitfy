<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Lock, Mail } from 'lucide-vue-next'; // Adicionado ícones
import { useI18n } from 'vue-i18n';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

const { t } = useI18n();

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <AuthBase
        :title="t('auth.login.title')"
        :description="t('auth.login.description')"
    >
        <Head title="Log in" />

        <div class="absolute top-8 left-8">
            <Button
                as-child
                variant="ghost"
                class="group rounded-xl text-muted-foreground transition-all hover:bg-muted/50 hover:text-foreground"
            >
                <Link href="/">
                    <ArrowLeft
                        class="mr-2 h-4 w-4 transition-transform group-hover:-translate-x-1"
                    />
                    {{ t('auth.backToWebsite') }}
                </Link>
            </Button>
        </div>

        <div
            v-if="status"
            class="mb-6 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-center text-sm font-bold text-emerald-600 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400"
        >
            {{ status }}
        </div>

        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-5">
                <div class="grid gap-2">
                    <Label
                        for="email"
                        class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                    >
                        {{ t('auth.login.email') }}
                    </Label>
                    <div class="group relative">
                        <Mail
                            class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-muted-foreground/40 transition-colors group-focus-within:text-primary"
                        />
                        <Input
                            id="email"
                            type="email"
                            name="email"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="email"
                            placeholder="name@company.com"
                            class="h-12 rounded-xl border-border/60 bg-muted/20 pl-11 transition-all focus:bg-background"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <div class="ml-1 flex items-center justify-between">
                        <Label
                            for="password"
                            class="text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                        >
                            {{ t('auth.login.password') }}
                        </Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-[11px] font-bold tracking-tight text-primary/60 uppercase transition-colors hover:text-primary"
                            :tabindex="5"
                        >
                            {{ t('auth.login.forgot') }}
                        </TextLink>
                    </div>
                    <div class="group relative">
                        <Lock
                            class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-muted-foreground/40 transition-colors group-focus-within:text-primary"
                        />
                        <Input
                            id="password"
                            type="password"
                            name="password"
                            required
                            :tabindex="2"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="h-12 rounded-xl border-border/60 bg-muted/20 pl-11 transition-all focus:bg-background"
                        />
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <div class="flex items-center justify-between px-1">
                    <label
                        for="remember"
                        class="group flex cursor-pointer items-center gap-3"
                    >
                        <Checkbox
                            id="remember"
                            name="remember"
                            :tabindex="3"
                            class="rounded-md border-muted-foreground/30 data-[state=checked]:bg-primary"
                        />
                        <span
                            class="text-sm font-medium text-muted-foreground transition-colors group-hover:text-foreground"
                            >{{ t('auth.login.rememberMe') }}</span
                        >
                    </label>
                </div>

                <Button
                    type="submit"
                    class="mt-2 h-12 w-full rounded-xl bg-primary text-primary-foreground shadow-lg shadow-primary/20 transition-all hover:-translate-y-0.5 hover:shadow-primary/30 active:translate-y-0"
                    :tabindex="4"
                    :disabled="processing"
                >
                    <Spinner v-if="processing" class="mr-2" />
                    {{ t('auth.login.signIn') }}
                </Button>
            </div>

            <div
                class="text-center text-sm font-medium text-muted-foreground"
                v-if="canRegister"
            >
                {{ t('auth.login.noAccount') }}
                <TextLink
                    :href="register()"
                    :tabindex="5"
                    class="font-bold text-primary underline-offset-4 hover:underline"
                >
                    {{ t('auth.login.createAccount') }}
                </TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
