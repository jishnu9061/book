<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <Link class="text-indigo-400 hover:text-indigo-600" :href="this.route('cities')">{{ $t('City') }}</Link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <div class="bg-white rounded-md shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="update">
          <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
              <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" :label="$t('Name')" />
          </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <button class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">{{ $t('Delete') }}
            {{ $t('City') }}</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">{{ $t('Update') }}
            {{ $t('City') }}</loading-button>
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
import { Link } from '@inertiajs/vue3'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  components: {
    LoadingButton,
    TextInput,
    Link,
    DeleteConfirmation,
  },
  props: {
    city: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: this.city.name,
      }),
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
        this.form.put(this.route('cities.update', this.city.id))
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
        title: 'Delete City',
        message: 'This action cannot be undone.',
        itemName: this.city.name,
        itemType: 'city',
        deleteUrl: this.route('cities.destroy', this.city.id),
        deleteMethod: 'delete'
      });
    }
  }
}
</script>