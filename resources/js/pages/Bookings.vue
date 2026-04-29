<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Bookings',
        href: '/bookings',
    },
];

interface Booking {
    id: number;
    event_name: string;
    email: string;
    phone_number: string;
    event_date: string;
    location: string;
    event_type: string;
    event_details: Record<string, unknown> | null;
    created_at: string;
}

interface PaginatedBookings {
    data: Booking[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

const props = defineProps<{ bookings: PaginatedBookings }>();

const goToPage = (page: number) => {
    router.get('/bookings', { page }, { preserveState: true, preserveScroll: true });
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};
</script>

<template>
    <Head title="Bookings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Bookings</h2>
                <span class="text-sm text-muted-foreground" v-if="bookings.total != null">
                    {{ bookings.total }} total
                </span>
            </div>

            <div v-if="bookings.data.length === 0" class="flex flex-1 items-center justify-center rounded-xl border border-sidebar-border/70 p-12 dark:border-sidebar-border">
                <p class="text-muted-foreground">No bookings found.</p>
            </div>

            <div v-else class="overflow-x-auto rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border">
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">#</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Event Name</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Email</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Phone</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Event Date</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Location</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Event Type</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="booking in bookings.data"
                            :key="booking.id"
                            class="border-b border-sidebar-border/70 last:border-0 dark:border-sidebar-border hover:bg-muted/30 transition-colors"
                        >
                            <td class="px-4 py-3 text-muted-foreground">{{ booking.id }}</td>
                            <td class="px-4 py-3 font-medium">{{ booking.event_name }}</td>
                            <td class="px-4 py-3">{{ booking.email }}</td>
                            <td class="px-4 py-3">{{ booking.phone_number }}</td>
                            <td class="px-4 py-3">{{ formatDate(booking.event_date) }}</td>
                            <td class="px-4 py-3">{{ booking.location }}</td>
                            <td class="px-4 py-3">
                                <span class="rounded-full bg-yellow-400/20 px-2 py-0.5 text-xs font-medium text-yellow-700 dark:text-yellow-400">
                                    {{ booking.event_type }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">{{ formatDate(booking.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="bookings.last_page > 1" class="flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    Showing {{ bookings.from }}–{{ bookings.to }} of {{ bookings.total }}
                </p>
                <div class="flex gap-1">
                    <button
                        v-for="page in bookings.last_page"
                        :key="page"
                        @click="goToPage(page)"
                        class="h-8 min-w-8 rounded-md px-2 text-sm transition-colors"
                        :class="page === bookings.current_page
                            ? 'bg-yellow-400 text-black font-medium'
                            : 'border border-sidebar-border/70 dark:border-sidebar-border hover:bg-muted/50'"
                    >
                        {{ page }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
