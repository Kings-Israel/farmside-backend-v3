<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogFooter,
    DialogClose,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ChevronLeft, ChevronRight, Plus } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Bookings', href: '/bookings' },
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
    description: string | null;
    status: string;
    created_at: string;
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

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const statusBadgeClass = (status: string) =>
    status === 'confirmed'
        ? 'bg-green-400/20 text-green-700 dark:text-green-400'
        : 'bg-yellow-400/20 text-yellow-700 dark:text-yellow-400';

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

const calendarMonthLabel = computed(() =>
    calendarDate.value.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })
);

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

    const toDateStr = (y: number, m: number, d: number) =>
        `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;

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
</script>

<template>
    <Head title="Bookings" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Bookings</h2>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-muted-foreground" v-if="bookings.total != null">
                        {{ bookings.total }} total
                    </span>
                    <Button
                        size="sm"
                        @click="showAddDialog = true"
                        class="bg-yellow-400 text-black hover:bg-yellow-500 gap-1.5"
                    >
                        <Plus class="size-3.5" />
                        Add Booking
                    </Button>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-sidebar-border/70 dark:border-sidebar-border">
                <button
                    @click="activeTab = 'list'"
                    class="px-4 py-2 text-sm font-medium transition-colors -mb-px"
                    :class="activeTab === 'list'
                        ? 'border-b-2 border-yellow-400 text-foreground'
                        : 'text-muted-foreground hover:text-foreground'"
                >
                    List
                </button>
                <button
                    @click="activeTab = 'calendar'"
                    class="px-4 py-2 text-sm font-medium transition-colors -mb-px"
                    :class="activeTab === 'calendar'
                        ? 'border-b-2 border-yellow-400 text-foreground'
                        : 'text-muted-foreground hover:text-foreground'"
                >
                    Calendar
                </button>
            </div>

            <!-- List View -->
            <template v-if="activeTab === 'list'">
                <div
                    v-if="bookings.data.length === 0"
                    class="flex flex-1 items-center justify-center rounded-xl border border-sidebar-border/70 p-12 dark:border-sidebar-border"
                >
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
                                        :class="statusBadgeClass(booking.status)"
                                        class="rounded-full px-2 py-0.5 text-xs font-medium capitalize"
                                    >
                                        {{ booking.status }}
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
            </template>

            <!-- Calendar View -->
            <template v-if="activeTab === 'calendar'">
                <div class="rounded-xl border border-sidebar-border/70 dark:border-sidebar-border overflow-hidden">

                    <!-- Month navigation -->
                    <div class="flex items-center justify-between px-4 py-3 border-b border-sidebar-border/70 dark:border-sidebar-border bg-muted/30">
                        <button
                            @click="prevMonth"
                            class="p-1 rounded hover:bg-muted/60 transition-colors"
                        >
                            <ChevronLeft class="size-4" />
                        </button>
                        <span class="font-medium text-sm">{{ calendarMonthLabel }}</span>
                        <button
                            @click="nextMonth"
                            class="p-1 rounded hover:bg-muted/60 transition-colors"
                        >
                            <ChevronRight class="size-4" />
                        </button>
                    </div>

                    <!-- Day-of-week headers -->
                    <div class="grid grid-cols-7 border-b border-sidebar-border/70 dark:border-sidebar-border bg-muted/20">
                        <div
                            v-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']"
                            :key="day"
                            class="px-2 py-2 text-center text-xs font-medium text-muted-foreground"
                        >
                            {{ day }}
                        </div>
                    </div>

                    <!-- Day grid -->
                    <div class="grid grid-cols-7">
                        <div
                            v-for="(day, idx) in calendarDays"
                            :key="idx"
                            class="min-h-[96px] p-2 border-b border-sidebar-border/70 dark:border-sidebar-border"
                            :class="{
                                'border-r border-sidebar-border/70 dark:border-sidebar-border': (idx + 1) % 7 !== 0,
                                'border-b-0': idx >= 35,
                                'bg-muted/10': !day.currentMonth,
                            }"
                        >
                            <span
                                class="text-xs font-medium inline-flex size-6 items-center justify-center rounded-full mb-1"
                                :class="{
                                    'bg-yellow-400 text-black font-semibold': day.isToday,
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
                                    :class="booking.status === 'confirmed'
                                        ? 'bg-green-400/25 text-green-800 dark:text-green-300'
                                        : 'bg-yellow-400/25 text-yellow-800 dark:text-yellow-300'"
                                    class="text-xs rounded px-1 py-0.5 truncate leading-tight"
                                >
                                    {{ booking.event_name }}
                                </div>
                                <div v-if="day.bookings.length > 3" class="text-xs text-muted-foreground pl-1">
                                    +{{ day.bookings.length - 3 }} more
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Calendar legend -->
                <div class="flex items-center gap-4 text-xs text-muted-foreground">
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
                            <p v-if="form.errors.event_name" class="text-xs text-destructive">{{ form.errors.event_name }}</p>
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
                            <p v-if="form.errors.email" class="text-xs text-destructive">{{ form.errors.email }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="phone_number">Phone Number</Label>
                            <Input
                                id="phone_number"
                                v-model="form.phone_number"
                                placeholder="+1 234 567 8900"
                                :aria-invalid="!!form.errors.phone_number"
                            />
                            <p v-if="form.errors.phone_number" class="text-xs text-destructive">{{ form.errors.phone_number }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="event_date">Event Date</Label>
                            <Input
                                id="event_date"
                                type="date"
                                v-model="form.event_date"
                                :aria-invalid="!!form.errors.event_date"
                            />
                            <p v-if="form.errors.event_date" class="text-xs text-destructive">{{ form.errors.event_date }}</p>
                        </div>

                        <div class="space-y-1.5">
                            <Label for="location">Location</Label>
                            <Input
                                id="location"
                                v-model="form.location"
                                placeholder="Event location"
                                :aria-invalid="!!form.errors.location"
                            />
                            <p v-if="form.errors.location" class="text-xs text-destructive">{{ form.errors.location }}</p>
                        </div>

                        <div class="col-span-2 space-y-1.5">
                            <Label for="event_type">Event Type</Label>
                            <Input
                                id="event_type"
                                v-model="form.event_type"
                                placeholder="e.g. Wedding, Corporate, Birthday"
                                :aria-invalid="!!form.errors.event_type"
                            />
                            <p v-if="form.errors.event_type" class="text-xs text-destructive">{{ form.errors.event_type }}</p>
                        </div>

                        <div class="col-span-2 space-y-1.5">
                            <Label for="description">Description <span class="text-muted-foreground font-normal">(optional)</span></Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                placeholder="Additional notes about the booking"
                                rows="3"
                                class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs placeholder:text-muted-foreground focus-visible:outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] transition-[color,box-shadow] resize-none dark:bg-input/30"
                            />
                        </div>
                    </div>

                    <DialogFooter>
                        <DialogClose as-child>
                            <Button type="button" variant="secondary">Cancel</Button>
                        </DialogClose>
                        <Button
                            type="submit"
                            class="bg-yellow-400 text-black hover:bg-yellow-500"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Saving...' : 'Add Booking' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

    </AppLayout>
</template>
