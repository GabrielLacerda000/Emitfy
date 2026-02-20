<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Lock, Mail, User } from 'lucide-vue-next'; // Adicionado ícones
import { useI18n } from 'vue-i18n';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';

const { t } = useI18n();
</script>

<template>
    <AuthBase
        :title="t('auth.register.title')"
        :description="t('auth.register.description')"
    >
        <Head title="Register" />

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

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-5">
                <div class="grid gap-2">
                    <Label
                        for="name"
                        class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                    >
                        {{ t('auth.register.fullName') }}
                    </Label>
                    <div class="group relative">
                        <User
                            class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-muted-foreground/40 transition-colors group-focus-within:text-primary"
                        />
                        <Input
                            id="name"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="name"
                            name="name"
                            placeholder="John Doe"
                            class="h-12 rounded-xl border-border/60 bg-muted/20 pl-11 transition-all focus:bg-background"
                        />
                    </div>
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label
                        for="email"
                        class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                    >
                        {{ t('auth.register.email') }}
                    </Label>
                    <div class="group relative">
                        <Mail
                            class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-muted-foreground/40 transition-colors group-focus-within:text-primary"
                        />
                        <Input
                            id="email"
                            type="email"
                            required
                            :tabindex="2"
                            autocomplete="email"
                            name="email"
                            placeholder="name@company.com"
                            class="h-12 rounded-xl border-border/60 bg-muted/20 pl-11 transition-all focus:bg-background"
                        />
                    </div>
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label
                        for="password"
                        class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                    >
                        {{ t('auth.register.password') }}
                    </Label>
                    <div class="group relative">
                        <Lock
                            class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-muted-foreground/40 transition-colors group-focus-within:text-primary"
                        />
                        <Input
                            id="password"
                            type="password"
                            required
                            :tabindex="3"
                            autocomplete="new-password"
                            name="password"
                            placeholder="••••••••"
                            class="h-12 rounded-xl border-border/60 bg-muted/20 pl-11 transition-all focus:bg-background"
                        />
                    </div>
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label
                        for="password_confirmation"
                        class="ml-1 text-[10px] font-black tracking-widest text-muted-foreground/80 uppercase"
                    >
                        {{ t('auth.register.confirmPassword') }}
                    </Label>
                    <div class="group relative">
                        <Lock
                            class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-muted-foreground/40 transition-colors group-focus-within:text-primary"
                        />
                        <Input
                            id="password_confirmation"
                            type="password"
                            required
                            :tabindex="4"
                            autocomplete="new-password"
                            name="password_confirmation"
                            placeholder="••••••••"
                            class="h-12 rounded-xl border-border/60 bg-muted/20 pl-11 transition-all focus:bg-background"
                        />
                    </div>
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 h-12 w-full rounded-xl bg-primary text-primary-foreground shadow-lg shadow-primary/20 transition-all hover:-translate-y-0.5 hover:shadow-primary/30 active:translate-y-0"
                    :tabindex="5"
                    :disabled="processing"
                >
                    <Spinner v-if="processing" class="mr-2" />
                    {{ t('auth.register.createAccount') }}
                </Button>
            </div>

            <div class="text-center text-sm font-medium text-muted-foreground">
                {{ t('auth.register.haveAccount') }}
                <TextLink
                    :href="login()"
                    class="font-bold text-primary underline-offset-4 transition-all hover:underline"
                    :tabindex="6"
                >
                    {{ t('auth.register.logIn') }}
                </TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
