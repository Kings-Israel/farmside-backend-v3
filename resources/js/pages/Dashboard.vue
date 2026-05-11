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
import { Head } from '@inertiajs/vue3';
import { CalendarDays, ChevronLeft, ChevronRight, Clock, MapPin } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
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

const props = defineProps<{ confirmedBookings: Booking[] }>();

const today = new Date();
const currentMonth = ref(new Date(today.getFullYear(), today.getMonth(), 1));
const weekdayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const dateKey = (date: Date | string) => {
    if (typeof date === 'string') {
        return date.slice(0, 10);
    }

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const parseDate = (date: string) => {
    return new Date(`${dateKey(date)}T00:00:00`);
};

const formatDate = (date: string) => {
    return parseDate(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
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

const monthLabel = computed(() => {
    return currentMonth.value.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
});

const bookingsByDate = computed(() => {
    const grouped = new Map<string, Booking[]>();

    for (const booking of props.confirmedBookings) {
        const key = dateKey(booking.event_date);
        const bookings = grouped.get(key) ?? [];

        bookings.push(booking);
        grouped.set(key, bookings);
    }

    return grouped;
});

const calendarDays = computed(() => {
    const year = currentMonth.value.getFullYear();
    const month = currentMonth.value.getMonth();
    const firstDay = new Date(year, month, 1);
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const leadingDays = firstDay.getDay();
    const totalCells = Math.ceil((leadingDays + daysInMonth) / 7) * 7;

    return Array.from({ length: totalCells }, (_, index) => {
        const dayNumber = index - leadingDays + 1;

        if (dayNumber < 1 || dayNumber > daysInMonth) {
            return {
                date: null,
                dayNumber: null,
                key: `empty-${index}`,
                bookings: [],
                isToday: false,
            };
        }

        const date = new Date(year, month, dayNumber);
        const key = dateKey(date);

        return {
            date,
            dayNumber,
            key,
            bookings: bookingsByDate.value.get(key) ?? [],
            isToday: key === dateKey(today),
        };
    });
});

const upcomingBookings = computed(() => {
    const startOfToday = new Date(today.getFullYear(), today.getMonth(), today.getDate());

    return [...props.confirmedBookings]
        .filter((booking) => parseDate(booking.event_date) >= startOfToday)
        .sort((a, b) => parseDate(a.event_date).getTime() - parseDate(b.event_date).getTime());
});

const nextBooking = computed(() => upcomingBookings.value[0] ?? null);

const previousMonth = () => {
    currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() - 1, 1);
};

const nextMonth = () => {
    currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1, 1);
};

const goToCurrentMonth = () => {
    currentMonth.value = new Date(today.getFullYear(), today.getMonth(), 1);
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold">Confirmed Booking Calendar</h2>
                    <p class="text-sm text-muted-foreground">Track accepted bookings by event date.</p>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" size="sm" @click="previousMonth">
                        <ChevronLeft class="size-4" />
                    </Button>
                    <Button variant="outline" size="sm" @click="goToCurrentMonth">Today</Button>
                    <Button variant="outline" size="sm" @click="nextMonth">
                        <ChevronRight class="size-4" />
                    </Button>
                </div>
            </div>

            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                        <CalendarDays class="size-4" />
                        Confirmed
                    </div>
                    <p class="mt-3 text-3xl font-semibold">{{ confirmedBookings.length }}</p>
                </div>
                <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                        <Clock class="size-4" />
                        Upcoming
                    </div>
                    <p class="mt-3 text-3xl font-semibold">{{ upcomingBookings.length }}</p>
                </div>
                <div class="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                        <MapPin class="size-4" />
                        Next Booking
                    </div>
                    <p class="mt-3 truncate text-lg font-semibold">{{ nextBooking?.event_name ?? 'None scheduled' }}</p>
                    <p v-if="nextBooking" class="mt-1 text-sm text-muted-foreground">{{ formatDate(nextBooking.event_date) }}</p>
                </div>
            </div>

            <section class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <div class="flex items-center justify-between border-b border-sidebar-border/70 bg-muted/40 px-4 py-3 dark:border-sidebar-border">
                    <h3 class="text-base font-semibold">{{ monthLabel }}</h3>
                    <span class="text-sm text-muted-foreground">{{ confirmedBookings.length }} confirmed bookings</span>
                </div>

                <div class="grid grid-cols-7 border-b border-sidebar-border/70 bg-muted/20 text-xs font-medium text-muted-foreground dark:border-sidebar-border">
                    <div v-for="weekday in weekdayLabels" :key="weekday" class="px-2 py-2 text-center">
                        {{ weekday }}
                    </div>
                </div>

                <div class="grid grid-cols-7">
                    <div
                        v-for="day in calendarDays"
                        :key="day.key"
                        class="min-h-28 border-r border-b border-sidebar-border/70 p-2 last:border-r-0 dark:border-sidebar-border"
                        :class="day.date ? 'bg-background' : 'bg-muted/20'"
                    >
                        <div v-if="day.date" class="flex h-full flex-col gap-2">
                            <span
                                class="flex size-7 items-center justify-center rounded-full text-sm font-medium"
                                :class="day.isToday ? 'bg-yellow-400 text-black' : 'text-muted-foreground'"
                            >
                                {{ day.dayNumber }}
                            </span>

                            <div class="space-y-1">
                                <Dialog v-for="booking in day.bookings" :key="booking.id">
                                    <DialogTrigger as-child>
                                        <button
                                            type="button"
                                            class="w-full rounded-md border border-green-500/20 bg-green-500/10 px-2 py-1 text-left text-xs transition-colors hover:bg-green-500/20"
                                        >
                                            <span class="block truncate font-medium text-green-800 dark:text-green-300">{{ booking.event_name }}</span>
                                            <span class="block truncate text-muted-foreground">{{ booking.location }}</span>
                                        </button>
                                    </DialogTrigger>
                                    <DialogContent class="max-h-[75vh] overflow-y-auto sm:max-w-4xl">
                                        <DialogHeader>
                                            <DialogTitle>{{ booking.event_name }}</DialogTitle>
                                            <DialogDescription>Confirmed booking #{{ booking.id }}</DialogDescription>
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
                                                        <dt class="text-xs font-medium text-muted-foreground">Confirmed At</dt>
                                                        <dd class="mt-1">{{ booking.confirmed_at ? formatDateTime(booking.confirmed_at) : 'Not provided' }}</dd>
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

                                            <section>
                                                <h3 class="mb-3 text-sm font-medium">Event Details</h3>
                                                <dl
                                                    v-if="eventDetailEntries(booking).length > 0"
                                                    class="gap-3 rounded-lg border border-sidebar-border/70 p-4 sm:grid-cols-2 dark:border-sidebar-border"
                                                >
                                                    <div v-for="[key, value] in eventDetailEntries(booking)" :key="key" class="min-w-0">
                                                        <dt class="text-xs font-medium text-muted-foreground mb-4">{{ formatLabel(key) }}</dt>
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
                                        </div>
                                    </DialogContent>
                                </Dialog>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
