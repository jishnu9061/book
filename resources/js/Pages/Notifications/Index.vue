<script setup>
import Layout from '@/Shared/Layout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircleIcon, EnvelopeIcon } from '@heroicons/vue/24/solid';
import Pagination from "@/Shared/Pagination.vue";

defineProps({
    notifications: Object,
});
</script>

<template>
    <Head title="All Notifications" />

    <Layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                All Notifications
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="notification in notifications.data" :key="notification.id">
                            <!--
                                **** THE CHANGE IS HERE ****
                                Instead of a simple <Link :href="notification.data.url">, we now use
                                a link that performs a POST request to our 'markAsRead' route.
                                - :href: Points to the 'notifications.read' route with the notification ID.
                                - method="post": Tells Inertia to make a POST request.
                                - as="button": Renders the component as a <button> for semantics, but we style it to look like a full-width link.
                                - class="block w-full ...": Makes the entire list item clickable.
                                The backend controller will handle the redirect to the ticket page automatically.
                            -->
                            <Link
                                :href="route('notifications.read', notification.id)"
                                method="post"
                                as="button"
                                type="button"
                                class="block w-full text-left hover:bg-gray-50 focus:outline-none focus:bg-gray-100 transition"
                                :class="{ 'bg-blue-50': !notification.read_at }"
                            >
                                <div class="flex items-center px-4 py-4 sm:px-6">
                                    <div class="min-w-0 flex-1 flex items-center">
                                        <div class="flex-shrink-0">
                                            <EnvelopeIcon v-if="notification.read_at" class="h-8 w-8 rounded-full text-gray-400" />
                                            <span v-else class="relative flex h-8 w-8">
                                                <EnvelopeIcon class="h-8 w-8 rounded-full text-blue-500" />
                                                <span class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full bg-blue-400 ring-2 ring-white"></span>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 px-4 md:grid md:grid-cols-2 md:gap-4">
                                            <div>
                                                <p class="text-sm truncate" :class="{'font-bold text-gray-800': !notification.read_at, 'text-gray-600': notification.read_at}">{{ notification.data.message }}</p>
                                                <p class="mt-2 flex items-center text-sm text-gray-500">
                                                    {{ $t('Ticket') }}<span>: {{ notification.data.ticket_subject }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Link>
                        </li>
                        <li v-if="notifications.data.length === 0" class="text-center py-10">
                            <CheckCircleIcon class="h-12 w-12 text-green-400 mx-auto" />
                            <p class="mt-2 text-sm text-gray-600">No notifications yet.</p>
                        </li>
                    </ul>

                </div>

                <div class="mt-6">
                    <Pagination :links="notifications.links" />
                </div>
            </div>
        </div>
    </Layout>
</template>

<style scoped>
/* Optional: A subtle highlight for unread messages */
.bg-blue-50 {
    background-color: #EFF6FF;
}
</style>
