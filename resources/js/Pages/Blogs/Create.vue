<template>
  <div>
    <Head :title="$t(title)" />
    <div class="bg-white rounded-md shadow overflow-hidden max-w-full">
      <form @submit.prevent="store">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.title" :error="form.errors.title" class="pr-6 pb-8 w-full lg:w-2/3" :label="$t('Title')" />

            <select-input v-model="form.type_id" :error="form.errors.type_id" class="pr-6 pb-8 w-full lg:w-1/3" :label="$t('Type')" :required="true">
                <option :value="null">{{ $t('Select type') }}</option>
                <option v-for="s in types" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select-input>

            <div class="pr-6 pb-8 w-full">
                <label class="form-label" >{{ $t('Details') }}:</label>
                <RichEditor v-model="form.details" class="mt-1" />
                <div v-if="form.errors.details" class="text-red-600 text-sm mt-1">{{ form.errors.details }}</div>
            </div>

            <file-input v-model="form.image" :error="form.errors.image" class="pb-8 pr-6 w-full lg:w-1/3" type="file" accept="image/*" :label="$t('Feature image')" />

        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end items-center">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">{{ $t('Create Post') }}</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import TextareaInput from '@/Shared/TextareaInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import FileInput from '@/Shared/FileInput.vue'
import { Link, Head } from '@inertiajs/vue3'

import RichEditor from '@/Shared/RichEditor.vue';

export default {
  metaInfo: { title: 'Create a new post' },
  components: {
      LoadingButton,
      SelectInput,
      TextareaInput,
      TextInput,
      FileInput,
      Link,
      Head,
      RichEditor,
        },
  layout: Layout,
  props: {
      title: String,
      types: Array
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        title: null,
          type_id: null,
          image: null,
        details: ''
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('posts.store'))
    },
  },
}
</script>
