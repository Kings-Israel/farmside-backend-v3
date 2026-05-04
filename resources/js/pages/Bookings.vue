<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { CheckCircle2, Eye } from 'lucide-vue-next';

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
    event_duration: string | null;
    event_details: Record<string, unknown> | unknown[] | null;
    description: string | null;
    confirmed_at: string | null;
    created_at: string;
    updated_at: string;
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

const confirmBooking = (booking: Booking) => {
    router.patch(
        `/bookings/${booking.id}/confirm`,
        {},
        {
            preserveScroll: true,
            only: ['bookings'],
        },
    );
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const formatDateTime = (date: string) => {
    return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
};

const formatLabel = (key: string) => {
    return key.replace(/_/g, ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());
};

const formatValue = (value: unknown) => {
    if (value === null || value === undefined || value === '') {
        return 'Not provided';
    }

    if (Array.isArray(value)) {
        return value.length > 0 ? value.join(', ') : 'Not provided';
    }

    if (typeof value === 'object') {
        return JSON.stringify(value, null, 2);
    }

    if (typeof value === 'boolean') {
        return value ? 'Yes' : 'No';
    }

    return String(value);
};

const eventDetailEntries = (booking: Booking) => {
    if (!booking.event_details) {
        return [];
    }

    if (Array.isArray(booking.event_details)) {
        return booking.event_details.map((value, index) => [`Item ${index + 1}`, value] as const);
    }

    return Object.entries(booking.event_details);
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
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Status</th>
                            <th class="px-4 py-3 text-left font-medium text-muted-foreground">Submitted</th>
                            <th class="px-4 py-3 text-right font-medium text-muted-foreground">Actions</th>
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
                            <td class="px-4 py-3">
                                <span
                                    class="rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="booking.confirmed_at
                                        ? 'bg-green-500/15 text-green-700 dark:text-green-400'
                                        : 'bg-muted text-muted-foreground'"
                                >
                                    {{ booking.confirmed_at ? 'Confirmed' : 'Pending' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-muted-foreground">{{ formatDate(booking.created_at) }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        v-if="!booking.confirmed_at"
                                        variant="default"
                                        size="sm"
                                        class="gap-2 bg-green-600 text-white hover:bg-green-700"
                                        @click="confirmBooking(booking)"
                                    >
                                        <CheckCircle2 class="size-4" />
                                        Confirm
                                    </Button>

                                    <Button v-else variant="secondary" size="sm" class="gap-2" disabled>
                                        <CheckCircle2 class="size-4" />
                                        Confirmed
                                    </Button>

                                    <Dialog>
                                        <DialogTrigger as-child>
                                            <Button variant="outline" size="sm" class="gap-2">
                                                <Eye class="size-4" />
                                                View
                                            </Button>
                                        </DialogTrigger>
                                        <DialogContent class="max-h-[75vh] overflow-y-auto sm:max-w-4xl">
                                            <DialogHeader>
                                                <DialogTitle>{{ booking.event_name }}</DialogTitle>
                                                <DialogDescription>Booking #{{ booking.id }}</DialogDescription>
                                            </DialogHeader>

                                        <div class="space-y-5">
                                            <section>
                                                <h3 class="mb-3 text-sm font-medium">Event</h3>
                                                <dl class="grid gap-3 rounded-lg border border-sidebar-border/70 p-4 sm:grid-cols-2 dark:border-sidebar-border">
                                                    <div>
                                                        <dt class="text-xs font-medium text-muted-foreground">Event Type</dt>
                                                        <dd class="mt-1">{{ formatValue(booking.event_type) }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-xs font-medium text-muted-foreground">Event Date</dt>
                                                        <dd class="mt-1">{{ formatDate(booking.event_date) }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-xs font-medium text-muted-foreground">Duration</dt>
                                                        <dd class="mt-1">{{ formatValue(booking.event_duration) }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-xs font-medium text-muted-foreground">Location</dt>
                                                        <dd class="mt-1">{{ formatValue(booking.location) }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-xs font-medium text-muted-foreground">Status</dt>
                                                        <dd class="mt-1">{{ booking.confirmed_at ? 'Confirmed' : 'Pending' }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-xs font-medium text-muted-foreground">Confirmed At</dt>
                                                        <dd class="mt-1">{{ booking.confirmed_at ? formatDateTime(booking.confirmed_at) : 'Not confirmed' }}</dd>
                                                    </div>
                                                </dl>
                                            </section>

                                            <section>
                                                <h3 class="mb-3 text-sm font-medium">Contact</h3>
                                                <dl class="grid gap-3 rounded-lg border border-sidebar-border/70 p-4 sm:grid-cols-2 dark:border-sidebar-border">
                                                    <div>
                                                        <dt class="text-xs font-medium text-muted-foreground">Email</dt>
                                                        <dd class="mt-1 break-words">{{ formatValue(booking.email) }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-xs font-medium text-muted-foreground">Phone Number</dt>
                                                        <dd class="mt-1">{{ formatValue(booking.phone_number) }}</dd>
                                                    </div>
                                                </dl>
                                            </section>

                                            <!-- <section>
                                                <h3 class="mb-3 text-sm font-medium">Description</h3>
                                                <p class="whitespace-pre-wrap rounded-lg border border-sidebar-border/70 p-4 text-sm dark:border-sidebar-border">
                                                    {{ formatValue(booking.description) }}
                                                </p>
                                            </section> -->

                                            <section>
                                                <h3 class="mb-3 text-sm font-medium">Event Details</h3>
                                                <dl
                                                    v-if="eventDetailEntries(booking).length > 0"
                                                    class="gap-3 rounded-lg border border-sidebar-border/70 p-4 sm:grid-cols-2 dark:border-sidebar-border"
                                                >
                                                    <div v-for="[key, value] in eventDetailEntries(booking)" :key="key" class="min-w-0">
                                                        <dt class="text-xs font-medium text-muted-foreground mt-4">{{ formatLabel(key) }}</dt>
                                                        <dd
                                                            class="mt-1 whitespace-pre-wrap break-words"
                                                            :class="{ 'font-mono text-xs': typeof value === 'object' && value !== null }"
                                                        >
                                                            {{ formatValue(value) }}
                                                        </dd>
                                                    </div>
                                                </dl>
                                                <p v-else class="rounded-lg border border-sidebar-border/70 p-4 text-sm text-muted-foreground dark:border-sidebar-border">
                                                    No extra event details provided.
                                                </p>
                                            </section>

                                            <section>
                                                <h3 class="mb-3 text-sm font-medium">Record</h3>
                                                <dl class="grid gap-3 rounded-lg border border-sidebar-border/70 p-4 sm:grid-cols-2 dark:border-sidebar-border">
                                                    <div>
                                                        <dt class="text-xs font-medium text-muted-foreground">Submitted</dt>
                                                        <dd class="mt-1">{{ formatDateTime(booking.created_at) }}</dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-xs font-medium text-muted-foreground">Last Updated</dt>
                                                        <dd class="mt-1">{{ formatDateTime(booking.updated_at) }}</dd>
                                                    </div>
                                                </dl>
                                            </section>
                                        </div>
                                        </DialogContent>
                                    </Dialog>
                                </div>
                            </td>
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
