<script setup>
import { useForm } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    errors: Object,
    message: String,
});

const form = useForm({
    purchase_code: '',
});

const submit = () => {
    form.post(route('license.activate'), {
        onError: () => form.reset('purchase_code'),
    });
};
</script>

<template>


    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Activate Your Application</h2>

            <!-- Server messages -->
            <div v-if="props.message" class="mb-4 p-3 rounded-md bg-red-100 text-red-700">
                {{ props.message }}
            </div>
            <div v-if="form.hasErrors" class="mb-4 p-3 rounded-md bg-red-100 text-red-700">
                <div v-for="error in form.errors">{{ error }}</div>
            </div>

            <form @submit.prevent="submit">
                <div>
                    <label for="purchase_code" class="block text-sm font-medium text-gray-700">
                        Envato Purchase Code
                    </label>
                    <input
                        v-model="form.purchase_code"
                        id="purchase_code"
                        type="text"
                        required
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                    />
                </div>

                <div class="mt-6">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                        Activate License
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
