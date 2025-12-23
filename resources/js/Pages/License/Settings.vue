<script setup>
import { useForm, Head, usePage } from '@inertiajs/vue3';
import Layout from '@/Shared/Layout.vue';
import { defineProps } from 'vue';


const props = defineProps({
    activatedDomain: String,
    licenseKey: String,
    title: String,
    error: String,
    auth: Object,
});

const form = useForm({});

function deactivate() {
    if (
        confirm(
            'Are you sure you want to deactivate this license? The application will stop working on this domain until a new license is activated.'
        )
    ) {
        form.post(route('license.deactivate'));
    }
}

</script>

<template>
    <Head :title="$t(title)" />
<Layout v-if="auth.user">
    <div class="p-8">
        <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
            <div v-if="error" class="mb-4 p-4 rounded-md bg-red-100 text-red-800 border border-red-200">
                <h3 class="font-bold">Attention Required</h3>
                <p>{{ error }}</p>
            </div>
            <h2 class="text-xl font-bold mb-4">License Information</h2>
            <div class="space-y-3">
                <div>
                    <span class="font-semibold text-gray-600">Status:</span>
                    <span class="ml-2 text-green-600 font-bold">Active</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">Activated Domain:</span>
                    <span class="ml-2">{{ activatedDomain }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600">Purchase Code:</span>
                    <span class="ml-2 font-mono">{{ licenseKey }}</span>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t">
                <h3 class="font-bold text-lg text-red-700">Deactivate License</h3>
                <p class="text-sm text-gray-600 mt-2">
                    To use your license on a different domain, you must first deactivate it. Once deactivated, this installation will no longer be functional until a valid license is activated again.
                </p>
                <div class="mt-4">
                    <button
                        @click="deactivate"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
                    >
                        Deactivate License
                    </button>
                </div>
            </div>
        </div>
    </div>
</Layout>
    <div v-else>
        <div class="p-8">
            <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow">
                <div v-if="error" class="mb-4 p-4 rounded-md bg-red-100 text-red-800 border border-red-200">
                    <h3 class="font-bold">Attention Required</h3>
                    <p>{{ error }}</p>
                </div>
                <h2 class="text-xl font-bold mb-4">License Information</h2>
                <div class="space-y-3">
                    <div>
                        <span class="font-semibold text-gray-600">Status:</span>
                        <span class="ml-2 text-green-600 font-bold">Active</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Activated Domain:</span>
                        <span class="ml-2">{{ activatedDomain }}</span>
                    </div>
                    <div>
                        <span class="font-semibold text-gray-600">Purchase Code:</span>
                        <span class="ml-2 font-mono">{{ licenseKey }}</span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t">
                    <h3 class="font-bold text-lg text-red-700">Deactivate License</h3>
                    <p class="text-sm text-gray-600 mt-2">
                        Deactivating your license will release it from this domain ({{ activatedDomain }}), allowing you to use it on a different domain. Once deactivated, this installation will no longer be functional until a valid license is activated again.
                    </p>
                    <div class="mt-4">
                        <button
                            @click="deactivate"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 disabled:opacity-50"
                        >
                            Deactivate License on This Domain
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
