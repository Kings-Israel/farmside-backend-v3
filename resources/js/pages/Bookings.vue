<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { CheckCircle2, ChevronLeft, ChevronRight, Eye } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Bookings', href: '/bookings' }];

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
    status: string;
    created_at: string;
    updated_at: string;
}

interface CalendarBooking {
    id: number;
    event_name: string;
    event_date: string;
    status: string;
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

const props = defineProps<{
    bookings: PaginatedBookings;
    calendarBookings: CalendarBooking[];
}>();

const activeTab = ref<'list' | 'calendar'>('list');

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

const statusBadgeClass = (status: string) =>
    status === 'confirmed' ? 'bg-green-400/20 text-green-700 dark:text-green-400' : 'bg-yellow-400/20 text-yellow-700 dark:text-yellow-400';

// Add booking dialog
const showAddDialog = ref(false);

const form = useForm({
    event_name: '',
    email: '',
    phone_number: '',
    event_date: '',
    location: '',
    event_type: '',
    description: '',
});

const submitBooking = () => {
    form.post('/bookings', {
        preserveScroll: true,
        onSuccess: () => {
            showAddDialog.value = false;
            form.reset();
        },
    });
};

// Calendar state
const calendarDate = ref(new Date());

const calendarMonthLabel = computed(() => calendarDate.value.toLocaleDateString('en-US', { month: 'long', year: 'numeric' }));

const prevMonth = () => {
    const d = new Date(calendarDate.value);
    d.setMonth(d.getMonth() - 1);
    calendarDate.value = d;
};

const nextMonth = () => {
    const d = new Date(calendarDate.value);
    d.setMonth(d.getMonth() + 1);
    calendarDate.value = d;
};

interface CalendarDay {
    dayNumber: number;
    currentMonth: boolean;
    isToday: boolean;
    bookings: CalendarBooking[];
}

const calendarDays = computed((): CalendarDay[] => {
    const year = calendarDate.value.getFullYear();
    const month = calendarDate.value.getMonth();

    const today = new Date();
    const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDow = firstDay.getDay();

    const toDateStr = (y: number, m: number, d: number) => `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;

    const days: CalendarDay[] = [];

    // Previous month padding
    for (let i = startDow - 1; i >= 0; i--) {
        const d = new Date(year, month, -i);
        const dateStr = toDateStr(d.getFullYear(), d.getMonth(), d.getDate());
        days.push({
            dayNumber: d.getDate(),
            currentMonth: false,
            isToday: dateStr === todayStr,
            bookings: props.calendarBookings.filter((b: CalendarBooking) => b.event_date === dateStr),
        });
    }

    // Current month days
    for (let d = 1; d <= lastDay.getDate(); d++) {
        const dateStr = toDateStr(year, month, d);
        days.push({
            dayNumber: d,
            currentMonth: true,
            isToday: dateStr === todayStr,
            bookings: props.calendarBookings.filter((b: CalendarBooking) => b.event_date === dateStr),
        });
    }

    // Next month padding
    let nextDay = 1;
    while (days.length < 42) {
        const d = new Date(year, month + 1, nextDay);
        const dateStr = toDateStr(d.getFullYear(), d.getMonth(), d.getDate());
        days.push({
            dayNumber: nextDay++,
            currentMonth: false,
            isToday: dateStr === todayStr,
            bookings: props.calendarBookings.filter((b: CalendarBooking) => b.event_date === dateStr),
        });
    }

    return days;
});
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
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Bookings</h2>
                <div class="flex items-center gap-3">
                    <span class="text-muted-foreground text-sm" v-if="bookings.total != null"> {{ bookings.total }} total </span>
                    <Button size="sm" @click="showAddDialog = true" class="gap-1.5 bg-yellow-400 text-black hover:bg-yellow-500">
                        Add Bookings
                    </Button>
                </div>
            </div>

            <div v-if="bookings.last_page > 1" class="flex items-center justify-between">
                <p class="text-muted-foreground text-sm">Showing {{ bookings.from }}–{{ bookings.to }} of {{ bookings.total }}</p>
            </div>

            <!-- Tabs -->
            <div class="border-sidebar-border/70 dark:border-sidebar-border flex border-b">
                <button
                    @click="activeTab = 'list'"
                    class="-mb-px px-4 py-2 text-sm font-medium transition-colors"
                    :class="activeTab === 'list' ? 'text-foreground border-b-2 border-yellow-400' : 'text-muted-foreground hover:text-foreground'"
                >
                    List
                </button>
                <button
                    @click="activeTab = 'calendar'"
                    class="-mb-px px-4 py-2 text-sm font-medium transition-colors"
                    :class="activeTab === 'calendar' ? 'text-foreground border-b-2 border-yellow-400' : 'text-muted-foreground hover:text-foreground'"
                >
                    Calendar
                </button>
            </div>

            <!-- List View -->
            <template v-if="activeTab === 'list'">
                <div
                    v-if="bookings.data.length === 0"
                    class="border-sidebar-border/70 dark:border-sidebar-border flex flex-1 items-center justify-center rounded-xl border p-12"
                >
                    <p class="text-muted-foreground">No bookings found.</p>
                </div>

                <div v-else class="border-sidebar-border/70 dark:border-sidebar-border overflow-x-auto rounded-xl border">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-sidebar-border/70 bg-muted/50 dark:border-sidebar-border border-b">
                                <th class="text-muted-foreground px-4 py-3 text-left font-medium">Event Name</th>
                                <th class="text-muted-foreground px-4 py-3 text-left font-medium">Email</th>
                                <th class="text-muted-foreground px-4 py-3 text-left font-medium">Phone</th>
                                <th class="text-muted-foreground px-4 py-3 text-left font-medium">Event Date</th>
                                <th class="text-muted-foreground px-4 py-3 text-left font-medium">Location</th>
                                <th class="text-muted-foreground px-4 py-3 text-left font-medium">Event Type</th>
                                <th class="text-muted-foreground px-4 py-3 text-left font-medium">Status</th>
                                <th class="text-muted-foreground px-4 py-3 text-left font-medium">Submitted</th>
                                <th class="text-muted-foreground px-4 py-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="booking in bookings.data"
                                :key="booking.id"
                                class="border-sidebar-border/70 dark:border-sidebar-border hover:bg-muted/30 border-b transition-colors last:border-0"
                            >
                                <td class="px-4 py-3 font-medium">{{ booking.event_name }}</td>
                                <td class="px-4 py-3">{{ booking.email }}</td>
                                <td class="px-4 py-3">{{ booking.phone_number }}</td>
                                <td class="px-4 py-3">{{ formatDate(booking.event_date) }}</td>
                                <td class="px-4 py-3">{{ booking.location }}</td>
                                <td class="flex flex-nowrap px-4 py-5">
                                    <span
                                        class="rounded-full bg-yellow-400/20 px-2 py-0.5 text-xs font-medium text-nowrap text-yellow-700 dark:text-yellow-400"
                                    >
                                        {{ booking.event_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="
                                            booking.confirmed_at
                                                ? 'bg-green-500/15 text-green-700 dark:text-green-400'
                                                : 'bg-muted text-muted-foreground'
                                        "
                                    >
                                        {{ booking.confirmed_at ? 'Confirmed' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="text-muted-foreground px-4 py-3">{{ formatDate(booking.created_at) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            v-if="!booking.confirmed_at || booking.status === 'pending'"
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
                                                        <dl
                                                            class="border-sidebar-border/70 dark:border-sidebar-border grid gap-3 rounded-lg border p-4 sm:grid-cols-2"
                                                        >
                                                            <div>
                                                                <dt class="text-muted-foreground text-xs font-medium">Event Type</dt>
                                                                <dd class="mt-1">{{ formatValue(booking.event_type) }}</dd>
                                                            </div>
                                                            <div>
                                                                <dt class="text-muted-foreground text-xs font-medium">Event Date</dt>
                                                                <dd class="mt-1">{{ formatDate(booking.event_date) }}</dd>
                                                            </div>
                                                            <div>
                                                                <dt class="text-muted-foreground text-xs font-medium">Duration</dt>
                                                                <dd class="mt-1">{{ formatValue(booking.event_duration) }}</dd>
                                                            </div>
                                                            <div>
                                                                <dt class="text-muted-foreground text-xs font-medium">Location</dt>
                                                                <dd class="mt-1">{{ formatValue(booking.location) }}</dd>
                                                            </div>
                                                            <div>
                                                                <dt class="text-muted-foreground text-xs font-medium">Status</dt>
                                                                <dd class="mt-1">{{ booking.confirmed_at ? 'Confirmed' : 'Pending' }}</dd>
                                                            </div>
                                                            <div>
                                                                <dt class="text-muted-foreground text-xs font-medium">Confirmed At</dt>
                                                                <dd class="mt-1">
                                                                    {{
                                                                        booking.confirmed_at ? formatDateTime(booking.confirmed_at) : 'Not confirmed'
                                                                    }}
                                                                </dd>
                                                            </div>
                                                        </dl>
                                                    </section>

                                                    <section>
                                                        <h3 class="mb-3 text-sm font-medium">Contact</h3>
                                                        <dl
                                                            class="border-sidebar-border/70 dark:border-sidebar-border grid gap-3 rounded-lg border p-4 sm:grid-cols-2"
                                                        >
                                                            <div>
                                                                <dt class="text-muted-foreground text-xs font-medium">Email</dt>
                                                                <dd class="mt-1 break-words">{{ formatValue(booking.email) }}</dd>
                                                            </div>
                                                            <div>
                                                                <dt class="text-muted-foreground text-xs font-medium">Phone Number</dt>
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
                                                            class="border-sidebar-border/70 dark:border-sidebar-border gap-3 rounded-lg border p-4 sm:grid-cols-2"
                                                        >
                                                            <div v-for="[key, value] in eventDetailEntries(booking)" :key="key" class="min-w-0">
                                                                <dt class="text-muted-foreground mt-4 text-xs font-medium">{{ formatLabel(key) }}</dt>
                                                                <dd
                                                                    class="mt-1 break-words whitespace-pre-wrap"
                                                                    :class="{ 'font-mono text-xs': typeof value === 'object' && value !== null }"
                                                                >
                                                                    {{ formatValue(value) }}
                                                                </dd>
                                                            </div>
                                                        </dl>
                                                        <p
                                                            v-else
                                                            class="border-sidebar-border/70 text-muted-foreground dark:border-sidebar-border rounded-lg border p-4 text-sm"
                                                        >
                                                            No extra event details provided.
                                                        </p>
                                                    </section>

                                                    <section>
                                                        <h3 class="mb-3 text-sm font-medium">Record</h3>
                                                        <dl
                                                            class="border-sidebar-border/70 dark:border-sidebar-border grid gap-3 rounded-lg border p-4 sm:grid-cols-2"
                                                        >
                                                            <div>
                                                                <dt class="text-muted-foreground text-xs font-medium">Submitted</dt>
                                                                <dd class="mt-1">{{ formatDateTime(booking.created_at) }}</dd>
                                                            </div>
                                                            <div>
                                                                <dt class="text-muted-foreground text-xs font-medium">Last Updated</dt>
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
                    <p class="text-muted-foreground text-sm">Showing {{ bookings.from }}–{{ bookings.to }} of {{ bookings.total }}</p>
                    <div class="flex gap-1">
                        <button
                            v-for="page in bookings.last_page"
                            :key="page"
                            @click="goToPage(page)"
                            class="h-8 min-w-8 rounded-md px-2 text-sm transition-colors"
                            :class="
                                page === bookings.current_page
                                    ? 'bg-yellow-400 font-medium text-black'
                                    : 'border-sidebar-border/70 dark:border-sidebar-border hover:bg-muted/50 border'
                            "
                        >
                            {{ page }}
                        </button>
                    </div>
                </div>
            </template>

            <!-- Calendar View -->
            <template v-if="activeTab === 'calendar'">
                <div class="border-sidebar-border/70 dark:border-sidebar-border overflow-hidden rounded-xl border">
                    <!-- Month navigation -->
                    <div class="border-sidebar-border/70 dark:border-sidebar-border bg-muted/30 flex items-center justify-between border-b px-4 py-3">
                        <button @click="prevMonth" class="hover:bg-muted/60 rounded p-1 transition-colors">
                            <ChevronLeft class="size-4" />
                        </button>
                        <span class="text-sm font-medium">{{ calendarMonthLabel }}</span>
                        <button @click="nextMonth" class="hover:bg-muted/60 rounded p-1 transition-colors">
                            <ChevronRight class="size-4" />
                        </button>
                    </div>

                    <!-- Day-of-week headers -->
                    <div class="border-sidebar-border/70 dark:border-sidebar-border bg-muted/20 grid grid-cols-7 border-b">
                        <div
                            v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']"
                            :key="day"
                            class="text-muted-foreground px-2 py-2 text-center text-xs font-medium"
                        >
                            {{ day }}
                        </div>
                    </div>

                    <!-- Day grid -->
                    <div class="grid grid-cols-7">
                        <div
                            v-for="(day, idx) in calendarDays"
                            :key="idx"
                            class="border-sidebar-border/70 dark:border-sidebar-border min-h-[96px] border-b p-2"
                            :class="{
                                'border-sidebar-border/70 dark:border-sidebar-border border-r': (idx + 1) % 7 !== 0,
                                'border-b-0': idx >= 35,
                                'bg-muted/10': !day.currentMonth,
                            }"
                        >
                            <span
                                class="mb-1 inline-flex size-6 items-center justify-center rounded-full text-xs font-medium"
                                :class="{
                                    'bg-yellow-400 font-semibold text-black': day.isToday,
                                    'text-muted-foreground': !day.currentMonth && !day.isToday,
                                    'text-foreground': day.currentMonth && !day.isToday,
                                }"
                            >
                                {{ day.dayNumber }}
                            </span>
                            <div class="space-y-0.5">
                                <div
                                    v-for="(booking, bIdx) in day.bookings.slice(0, 3)"
                                    :key="bIdx"
                                    :class="
                                        booking.status === 'confirmed'
                                            ? 'bg-green-400/25 text-green-800 dark:text-green-300'
                                            : 'bg-yellow-400/25 text-yellow-800 dark:text-yellow-300'
                                    "
                                    class="truncate rounded px-1 py-0.5 text-xs leading-tight"
                                >
                                    {{ booking.event_name }}
                                </div>
                                <div v-if="day.bookings.length > 3" class="text-muted-foreground pl-1 text-xs">
                                    +{{ day.bookings.length - 3 }} more
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Calendar legend -->
                <div class="text-muted-foreground flex items-center gap-4 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="size-2.5 rounded-sm bg-green-400/40"></span>
                        Confirmed
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="size-2.5 rounded-sm bg-yellow-400/40"></span>
                        Pending
                    </div>
                </div>
            </template>
        </div>

        <!-- Add Booking Dialog -->
        <Dialog :open="showAddDialog" @update:open="showAddDialog = $event">
            <DialogContent class="sm:max-w-2xl">
                <DialogHeader>
                    <DialogTitle>Add Booking</DialogTitle>
                </DialogHeader>

                <form @submit.prevent="submitBooking" class="space-y-4 pt-1">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 space-y-1.5">
                            <Label for="event_name">Event Name</Label>
                            <Input
                                id="event_name"
                                v-model="form.event_name"
                                placeholder="Enter event name"
                                :aria-invalid="!!form.errors.event_name"
                            />
                            <p v-if="form.errors.event_name" class="text-destructive text-xs">{{ form.errors.event_name }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                type="email"
                                v-model="form.email"
                                placeholder="client@example.com"
                                :aria-invalid="!!form.errors.email"
                            />
                            <p v-if="form.errors.email" class="text-destructive text-xs">{{ form.errors.email }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="phone_number">Phone Number</Label>
                            <Input
                                id="phone_number"
                                v-model="form.phone_number"
                                placeholder="+1 234 567 8900"
                                :aria-invalid="!!form.errors.phone_number"
                            />
                            <p v-if="form.errors.phone_number" class="text-destructive text-xs">{{ form.errors.phone_number }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="event_date">Event Date</Label>
                            <Input id="event_date" type="date" v-model="form.event_date" :aria-invalid="!!form.errors.event_date" />
                            <p v-if="form.errors.event_date" class="text-destructive text-xs">{{ form.errors.event_date }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="location">Location</Label>
                            <Input id="location" v-model="form.location" placeholder="Event location" :aria-invalid="!!form.errors.location" />
                            <p v-if="form.errors.location" class="text-destructive text-xs">{{ form.errors.location }}</p>
                        </div>

                        <div class="col-span-2 space-y-1.5">
                            <Label for="event_type">Event Type</Label>
                            <Input
                                id="event_type"
                                v-model="form.event_type"
                                placeholder="e.g. Wedding, Corporate, Birthday"
                                :aria-invalid="!!form.errors.event_type"
                            />
                            <p v-if="form.errors.event_type" class="text-destructive text-xs">{{ form.errors.event_type }}</p>
                        </div>

                        <div class="col-span-2 space-y-1.5">
                            <Label for="description">Description <span class="text-muted-foreground font-normal">(optional)</span></Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Additional notes about the booking"
                                rows="3"
                                class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 dark:bg-input/30 flex w-full resize-none rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] focus-visible:ring-[3px] focus-visible:outline-none"
                            />
                        </div>
                    </div>

                    <DialogFooter>
                        <DialogClose as-child>
                            <Button type="button" variant="secondary">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" class="bg-yellow-400 text-black hover:bg-yellow-500" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Add Booking' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
