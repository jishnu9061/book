<script setup>
import { ref, onMounted } from 'vue';
import { usePage, router, Link } from '@inertiajs/vue3';
import { BellIcon, CheckCircleIcon, EllipsisVerticalIcon } from '@heroicons/vue/24/outline';
import Icon from "@/Shared/Icon.vue";

// Reactive state for local component
const showDropdown = ref(false);
const showOptionsMenu = ref(false);
const isBellShaking = ref(false); // For animation

const page = usePage();

// Get initial data from Inertia
const notifications = ref(page.props.notifications || []);
const notificationCount = ref(page.props.notification_count || 0);

const markAsReadAndVisit = (notification) => {
    router.post(route('notifications.read', notification.id), {}, {
        onSuccess: () => {
            showDropdown.value = false;
        },
    });
};

const markAllAsRead = () => {
    router.post(route('notifications.markAllAsRead'), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Manually update frontend state to reflect change immediately
            notifications.value.forEach(n => n.read_at = new Date().toISOString());
            notificationCount.value = 0;
            showOptionsMenu.value = false;
            showDropdown.value = false;
        }
    })
}

onMounted(() => {
    if (window.Echo && page.props.auth?.user) {
        // Check if Echo has authentication configured
        if (window.Echo.options.authEndpoint) {
            console.log('🔔 NotificationBell: Using private channel with authentication')
            window.Echo.private(`App.Models.User.${page.props.auth.user.id}`)
                .notification((notification) => {
                    // Manually add id and data wrapper to match structure of database notifications
                    const newNotification = {
                        id: notification.id, // Pusher doesn't send the UUID, so we need to generate it or get it from the payload
                        data: { ...notification },
                        read_at: null,
                    };
                    notifications.value.unshift(newNotification);
                notificationCount.value++;

                // Trigger bell animation
                isBellShaking.value = true;
                setTimeout(() => {
                    isBellShaking.value = false;
                }, 700); // Duration should match animation duration
            });
        } else {
            console.log('🔔 NotificationBell: Authentication not configured, skipping private channel subscription')
        }
    }
});
</script>

<template>
    <div class="relative flex">
        <button @click="showDropdown = !showDropdown" type="button" class="relative rounded-full text-gray-600 hover:text-black focus:outline-none">
            <span class="sr-only">View notifications</span>
            <Icon name="notification" class="h-7 w-7" :class="{ 'animate-bell-shake': isBellShaking }" aria-hidden="true" />
            <span v-if="notificationCount > 0" class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">{{ notificationCount }}</span>
        </button>

        <!-- Dropdown -->
        <div v-if="showDropdown" @click.away="showDropdown = false" class="absolute right-0 z-10 top-7 w-80 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
            <div class="flex justify-between items-center px-4 py-2 text-sm text-gray-700 font-bold border-b">
                <span>Notifications</span>
                <!-- 3-Dot Options Menu -->
                <div class="relative">
                    <button @click.stop="showOptionsMenu = !showOptionsMenu" class="p-1 rounded-full hover:bg-gray-200">
                        <EllipsisVerticalIcon class="h-5 w-5" />
                    </button>
                    <div v-if="showOptionsMenu" @click.away="showOptionsMenu = false" class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5">
                        <a @click.prevent="markAllAsRead" href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mark all as read</a>
                        <Link :href="route('notifications.index')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">All Notifications</Link>
                    </div>
                </div>
            </div>
            <div v-if="notifications.length > 0" class="max-h-96 overflow-y-auto">
                <a v-for="notification in notifications" :key="notification.id" @click.prevent="markAsReadAndVisit(notification)" href="#" class="block px-4 py-3 text-sm text-gray-600 hover:bg-gray-100 border-b" :class="{'font-semibold': !notification.read_at}">
                    <p class="text-gray-800">{{ notification.data.message }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $t('Ticket') }}: {{ notification.data.ticket_subject }}</p>
                </a>
            </div>
            <div v-else class="px-4 py-5 text-center">
                <CheckCircleIcon class="h-8 w-8 text-green-400 mx-auto mb-2" />
                <p class="text-sm text-gray-500">You're all caught up!</p>
            </div>
        </div>
    </div>
</template>
