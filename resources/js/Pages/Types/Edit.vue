<template>
    <div>
        <Head :title="$t(title)" />
        <div class="bg-white rounded-md shadow overflow-hidden max-w-3xl">
            <form @submit.prevent="update">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" :label="$t('Name')" />
                </div>
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
                    <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">{{ $t('Delete') }}</button>
                    <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">{{ $t('Update') }}</loading-button>
                </div>
            </form>
        </div>
        <!-- Delete Confirmation Modal -->
        <DeleteConfirmation
            :show="showDeleteModal"
            :title="deleteConfig.title"
            :message="deleteConfig.message"
            :item-name="deleteConfig.itemName"
            :item-type="deleteConfig.itemType"
            :delete-url="deleteConfig.deleteUrl"
            :delete-method="deleteConfig.deleteMethod"
            :delete-data="deleteConfig.deleteData"
            :confirm-button-text="deleteConfig.confirmButtonText"
            :cancel-button-text="deleteConfig.cancelButtonText"
            @close="hideDeleteConfirmation"
            @confirm="confirmDelete"
        />
    </div>


</template>

<script>
import Layout from '@/Shared/Layout.vue'
import { Link, Head } from '@inertiajs/vue3'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'

export default {
    layout: Layout,
    metaInfo() {
        return { title: this.form.name }
    },
    components: {
        LoadingButton,
        Layout,
        TextInput,
        Link,
        Head,
        DeleteConfirmation,
    },
    props: {
        type: Object,
        title: String,
    },
    remember: 'form',
    data() {
        return {form: this.$inertia.form({
                name: this.type.name,
            }),
            // Delete confirmation
            showDeleteModal: false,
            deleteConfig: {
                title: 'Are you sure?',
                message: 'This action cannot be undone.',
                itemName: 'this item',
                itemType: 'item',
                deleteUrl: '',
                deleteMethod: 'delete',
                deleteData: {},
                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel'
            }
        }
    },
    methods: {
        update() {
            this.form.put(this.route('types.update', this.type.id))
        },
        showDeleteConfirmation(config, onConfirmCallback = null) {
            Object.assign(this.deleteConfig, config);
            this.showDeleteModal = true;
            if (onConfirmCallback) {
                this.deleteConfig.onConfirmCallback = onConfirmCallback;
            }
        },
        hideDeleteConfirmation() {
            this.showDeleteModal = false;
            this.deleteConfig.onConfirmCallback = null;
        },
        confirmDelete() {
            if (this.deleteConfig.onConfirmCallback) {
                this.deleteConfig.onConfirmCallback();
            }
            this.hideDeleteConfirmation();
        },
        destroy() {
            this.showDeleteConfirmation({
                title: 'Delete Type',
                message: 'This action cannot be undone.',
                itemName: this.type.name,
                itemType: 'type',
                deleteUrl: this.route('types.destroy', this.type.id),
                deleteMethod: 'delete'
            });
        },
        restore() {
            if (confirm('Are you sure you want to restore this type?')) {
                this.$inertia.put(this.route('types.restore', this.type.id))
            }
        },
    },
}
</script>
