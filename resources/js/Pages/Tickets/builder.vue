<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="$t(title)" />

    <!-- Header -->
    <div class="bg-white dark:bg-slate-800 shadow-sm border-b border-slate-200 dark:border-slate-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Ticket Form Builder') }}</h1>
              <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $t('Create custom fields for your ticket forms') }}</p>
            </div>
            <div class="flex items-center gap-3">
              <span class="text-sm text-slate-500 dark:text-slate-400">
                {{ $t('Fields') }}: {{ fields.data?.length || 0 }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- Field Preview -->
        <div class="space-y-6">
          <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                  {{ $t('Form Preview') }}
                </h2>
                <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                  <Eye class="w-4 h-4" />
                  {{ $t('Live Preview') }}
                </div>
              </div>
            </div>
            <div class="p-6">
              <div v-if="fields.data && fields.data.length > 0" class="space-y-6">
                <div 
                  v-for="ticket_field in fields.data" 
                  :key="ticket_field.id"
                  class="group relative p-4 border border-slate-200 dark:border-slate-600 rounded-lg hover:border-blue-300 dark:hover:border-blue-500 transition-colors duration-200"
                >
                  <!-- Field Label -->
                  <label 
                    :for="!['time_picker', 'date_picker', 'checkbox'].includes(ticket_field.type) ? 'ticket_field_'+ticket_field.id : null" 
                    class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2"
                  >
                    {{ ticket_field.label }}
                    <span v-if="!ticket_field.required" class="text-slate-400 dark:text-slate-500 ml-1">({{ $t('optional') }})</span>
                    <span v-else class="text-red-500 ml-1">*</span>
                  </label>

                  <!-- Text Inputs -->
                  <input 
                    v-if="['text', 'email', 'number'].includes(ticket_field.type)" 
                    :type="ticket_field.type" 
                    :id="'ticket_field_'+ticket_field.id" 
                    :placeholder="ticket_field.placeholder" 
                    :required="ticket_field.required" 
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400"
                  />

                  <!-- Select Input -->
                  <select 
                    v-if="ticket_field.type === 'select'" 
                    :id="'ticket_field_'+ticket_field.id" 
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                  >
                    <option value="" disabled>{{ ticket_field.placeholder }}</option>
                    <option :value="option.value" v-for="(option, oi) in ticket_field.options" :key="oi">
                      {{ option.name }}
                    </option>
                  </select>

                  <!-- Checkbox Group -->
                  <div v-if="ticket_field.type === 'checkbox'" class="space-y-2">
                    <div 
                      v-for="(option, oi) in ticket_field.options" 
                      :key="oi"
                      class="flex items-center"
                    >
                      <input 
                        :id="'ticket_checkbox_'+ticket_field.id+'_field_'+oi" 
                        type="checkbox" 
                        :value="option.value" 
                        class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500 dark:ring-offset-slate-800 dark:bg-slate-700 dark:border-slate-600"
                      >
                      <label 
                        :for="'ticket_checkbox_'+ticket_field.id+'_field_'+oi" 
                        class="ml-2 text-sm text-slate-700 dark:text-slate-300"
                      >
                        {{ option.name }}
                      </label>
                    </div>
                  </div>

                  <!-- Textarea -->
                  <textarea 
                    v-if="ticket_field.type === 'textarea'" 
                    :id="'ticket_field_'+ticket_field.id" 
                    rows="3" 
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400" 
                    :placeholder="ticket_field.placeholder"
                  ></textarea>

                  <!-- File Input -->
                  <div v-if="ticket_field.type === 'file'" class="space-y-2">
                    <input 
                      :id="'ticket_field_'+ticket_field.id" 
                      type="file" 
                      class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-slate-700 dark:file:text-slate-300"
                    >
                  </div>

                  <!-- Field Hint -->
                  <p v-if="ticket_field.hint" class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    {{ ticket_field.hint }}
                  </p>

                  <!-- Field Actions -->
                  <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <div class="flex gap-1">
                      <button 
                        @click="modifyField(ticket_field)" 
                        class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded transition-colors duration-200"
                        :title="$t('Edit field')"
                      >
                        <Edit class="w-4 h-4" />
                      </button>
                      <button 
                        @click="deleteField(ticket_field)" 
                        class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded transition-colors duration-200"
                        :title="$t('Delete field')"
                      >
                        <Trash2 class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-12">
                <FormInput class="w-12 h-12 text-slate-400 dark:text-slate-500 mx-auto mb-4" />
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No fields yet') }}</h3>
                <p class="text-slate-500 dark:text-slate-400">{{ $t('Start by adding your first custom field') }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Field Builder -->
        <div class="space-y-6">
          <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
              <h2 class="text-lg font-semibold text-slate-900 dark:text-white">
                {{ modify_input ? $t('Edit Field') : $t('Add New Field') }}
              </h2>
            </div>
            <div class="p-6 space-y-6">
              <!-- Field Type Selection -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                  {{ $t('Field Type') }}
                </label>
                <div class="grid grid-cols-2 gap-3">
                  <div 
                    v-for="(input_type, it_key) in input_types" 
                    :key="it_key"
                    class="relative"
                  >
                    <input 
                      :id="'input_type_name_'+it_key" 
                      name="input_type" 
                      v-model="form.type" 
                      :value="input_type" 
                      type="radio" 
                      class="sr-only peer"
                    />
                    <label 
                      :for="'input_type_name_'+it_key" 
                      class="flex items-center justify-center p-3 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg cursor-pointer peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-700 dark:peer-checked:bg-blue-900/20 dark:peer-checked:border-blue-400 dark:peer-checked:text-blue-300 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200"
                    >
                      <component :is="getFieldIcon(input_type)" class="w-4 h-4 mr-2" />
                      {{ input_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                    </label>
                  </div>
                </div>
              </div>

              <!-- Required Field -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                  {{ $t('Required Field') }}
                </label>
                <div class="flex gap-4">
                  <label class="flex items-center cursor-pointer">
                    <input 
                      v-model="form.required" 
                      :value="1" 
                      type="radio" 
                      name="is_required" 
                      class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-slate-800 dark:bg-slate-700 dark:border-slate-600"
                    />
                    <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">{{ $t('Yes') }}</span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input 
                      v-model="form.required" 
                      :value="0" 
                      type="radio" 
                      name="is_required" 
                      class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-slate-800 dark:bg-slate-700 dark:border-slate-600"
                    />
                    <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">{{ $t('No') }}</span>
                  </label>
                </div>
              </div>

              <!-- Field Configuration -->
              <div class="space-y-4">
                <!-- Label -->
                <div>
                  <label for="label_name_new" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ $t('Field Label') }}
                  </label>
                  <input 
                    v-model="form.label" 
                    type="text" 
                    id="label_name_new" 
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400" 
                    :placeholder="$t('e.g. Full Name')" 
                    required 
                  />
                  <p v-if="form.errors.label" class="mt-1 text-sm text-red-600 dark:text-red-400">
                    {{ form.errors.label }}
                  </p>
                </div>

                <!-- Name -->
                <div>
                  <label for="name_new" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ $t('Field Name') }}
                  </label>
                  <input 
                    v-model="form.name" 
                    type="text" 
                    id="name_new" 
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400" 
                    :placeholder="$t('e.g. full_name')" 
                    required 
                  />
                  <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                    {{ form.errors.name }}
                  </p>
                </div>

                <!-- Placeholder -->
                <div v-if="!['checkbox', 'file'].includes(form.type)">
                  <label for="placeholder_new" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ $t('Placeholder Text') }}
                  </label>
                  <input 
                    v-model="form.placeholder" 
                    type="text" 
                    id="placeholder_new" 
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400" 
                    :placeholder="$t('e.g. Enter your full name')" 
                  />
                </div>

                <!-- Options for Select/Checkbox -->
                <div v-if="['checkbox', 'select'].includes(form.type)">
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ $t('Options') }}
                  </label>
                  <div class="space-y-2">
                    <div 
                      v-for="(option, oi) in form.options" 
                      :key="oi"
                      class="flex gap-2 items-center"
                    >
                      <input 
                        type="text" 
                        v-model="option.name" 
                        class="flex-1 px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400" 
                        :placeholder="$t('Option label')" 
                      />
                      <input 
                        type="text" 
                        v-model="option.value" 
                        class="flex-1 px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400" 
                        :placeholder="$t('Option value')" 
                      />
                      <button 
                        v-if="oi > 0" 
                        @click="deleteOption(form.options, oi)" 
                        type="button"
                        class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200"
                        :title="$t('Remove option')"
                      >
                        <Trash2 class="w-4 h-4" />
                      </button>
                    </div>
                    <button 
                      @click="addOption()" 
                      type="button"
                      class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 font-medium"
                    >
                      <Plus class="w-4 h-4" />
                      {{ $t('Add Option') }}
                    </button>
                  </div>
                </div>

                <!-- Hint for File -->
                <div v-if="form.type === 'file'">
                  <label for="hints_new" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ $t('File Hint') }}
                  </label>
                  <input 
                    v-model="form.hint" 
                    type="text" 
                    id="hints_new" 
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white dark:placeholder-slate-400" 
                    :placeholder="$t('e.g. Max file size: 5MB')" 
                  />
                </div>
              </div>

              <!-- Submit Button -->
              <button 
                @click="submitField(form)" 
                type="button"
                class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm hover:shadow-md"
              >
                <Save v-if="modify_input" class="w-4 h-4" />
                <Plus v-else class="w-4 h-4" />
                {{ modify_input ? $t('Update Field') : $t('Add Field') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <DeleteConfirmation
      v-if="!!delete_element"
      :data="{id: delete_element}"
      url="ticket-fields.delete"
      name="the field"
      @close-popup="deleteClose"
    />
  </div>
</template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import axios from 'axios'
import Layout from '@/Shared/Layout.vue'
import Icon from '@/Shared/Icon.vue'
import DeleteConfirmation from "@/Shared/DeleteConfirmation.vue"
import {
  Eye,
  Edit,
  Trash2,
  Save,
  Plus,
  FormInput,
  Type,
  FileText,
  CheckSquare,
  Upload,
  Hash
} from 'lucide-vue-next'

export default {
  metaInfo: { title: 'Ticket Form Builder' },
  components: {
    DeleteConfirmation,
    Link,
    Head,
    Icon,
    Eye,
    Edit,
    Trash2,
    Save,
    Plus,
    FormInput,
    Type,
    FileText,
    CheckSquare,
    Upload,
    Hash
  },
  layout: Layout,
  props: {
    title: {
      type: String,
    },
    fields: {
      type: Object,
    },
  },
  data() {
    return {
      delete_element: false,
      modify_input: false,
      input_types: ['text', 'textarea', 'select', 'checkbox', 'file', 'email', 'number'],
      new_input: {
        'type': 'text',
        'required': 0,
        label: '',
        name: '',
        'placeholder': '',
        id: null,
        'options': [{name: '', value: ''}],
        'hint': ''
      },
      form: this.$inertia.form({
        'type': 'text',
        'required': 0,
        label: '',
        name: '',
        'placeholder': '',
        id: null,
        'options': [{name: '', value: ''}],
        'hint': ''
      }),
    }
  },
  methods: {
    getFieldIcon(type) {
      const iconMap = {
        'text': Type,
        'email': Type,
        'number': Hash,
        'textarea': FileText,
        'select': CheckSquare,
        'checkbox': CheckSquare,
        'file': Upload
      }
      return iconMap[type] || Type
    },
    deleteClose() {
      this.delete_element = false
    },
    submitField(obj) {
      if (!['select', 'checkbox'].includes(this.form.type)) {
        this.form.options = null;
      }
      
      if (this.modify_input) {
        this.form.put(this.route('ticket-fields.update', this.form.id), {
          onSuccess: () => {
            this.resetForm()
          }
        })
      } else {
        this.form.post(this.route('ticket-fields.store'), {
          onSuccess: () => {
            this.resetForm()
          }
        })
      }
    },
    resetForm() {
      Object.assign(this.form, this.new_input)
      this.modify_input = false
    },
    modifyField(fieldObject) {
      Object.assign(this.form, fieldObject)
      this.modify_input = true
    },
    deleteField(fieldObject) {
      this.delete_element = fieldObject.id
    },
    addOption() {
      this.form.options.push({name: '', value: ''})
    },
    deleteOption(options, index) {
      options.splice(index, 1)
    }
  },
  created() {
    // Initialize form with default values
  }
}
</script>
