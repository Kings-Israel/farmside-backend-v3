<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
        <header class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
            </nav>
        </header>
        <div class="flex w-full flex-col items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
            <div class="flex justify-center">
                <AppLogo />
            </div>
            <Link :href="route('login')" v-if="!$page.props.auth.user" class="rounded-lg bg-green-400 px-4 py-2 text-center text-white">Login</Link>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
