<template>
  <div class="conversation-templates">
    <!-- Header -->
    <div class="px-4 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-600">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-slate-900 dark:text-white flex items-center gap-2">
          <FileText class="w-5 h-5" />
          {{ $t('Conversation Templates') }}
        </h3>
        <div class="flex items-center gap-2">
          <button @click="createTemplate" class="px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
            <Plus class="w-4 h-4 mr-1" />
            {{ $t('New Template') }}
          </button>
          <button @click="toggleView" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
            <Grid v-if="viewMode === 'list'" class="w-4 h-4" />
            <List v-else class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
      <div class="flex gap-3">
        <div class="flex-1 relative">
          <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" />
          <input
            v-model="searchQuery"
            type="text"
            class="w-full pl-10 pr-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
            :placeholder="$t('Search templates...')"
          />
        </div>
        <select v-model="selectedCategory" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
          <option value="">{{ $t('All Categories') }}</option>
          <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
        </select>
        <select v-model="selectedType" class="px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
          <option value="">{{ $t('All Types') }}</option>
          <option value="internal">{{ $t('Internal') }}</option>
          <option value="customer">{{ $t('Customer') }}</option>
        </select>
      </div>
    </div>

    <!-- Templates Grid/List -->
    <div class="flex-1 overflow-y-auto p-4">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-8">
        <Loader2 class="w-6 h-6 animate-spin text-blue-600" />
        <span class="ml-2 text-slate-600 dark:text-slate-400">{{ $t('Loading templates...') }}</span>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredTemplates.length === 0" class="text-center py-8">
        <FileText class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-600" />
        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No templates found') }}</h3>
        <p class="text-slate-500 dark:text-slate-400 mb-4">{{ $t('Create your first conversation template') }}</p>
        <button @click="createTemplate" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          {{ $t('Create Template') }}
        </button>
      </div>

      <!-- Templates List -->
      <div v-else :class="viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4' : 'space-y-3'">
        <div
          v-for="template in filteredTemplates"
          :key="template.id"
          class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden hover:shadow-md transition-shadow cursor-pointer"
          @click="selectTemplate(template)"
        >
          <!-- Template Header -->
          <div class="p-4 border-b border-slate-200 dark:border-slate-600">
            <div class="flex items-start justify-between">
              <div class="flex-1 min-w-0">
                <h4 class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ template.name }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ template.description }}</p>
              </div>
              <div class="flex items-center gap-1 ml-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="getTypeClass(template.type)">
                  {{ template.type === 'internal' ? $t('Internal') : $t('Customer') }}
                </span>
                <button @click.stop="editTemplate(template)" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                  <Edit class="w-3 h-3" />
                </button>
                <button @click.stop="deleteTemplate(template)" class="p-1 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                  <Trash2 class="w-3 h-3" />
                </button>
              </div>
            </div>
          </div>

          <!-- Template Content -->
          <div class="p-4">
            <div class="space-y-2">
              <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <Tag class="w-3 h-3" />
                <span>{{ template.category }}</span>
                <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
                <span>{{ template.usage_count || 0 }} {{ $t('uses') }}</span>
              </div>
              
              <div class="text-sm text-slate-700 dark:text-slate-300 line-clamp-3">
                {{ template.content }}
              </div>
              
              <div v-if="template.variables?.length" class="flex flex-wrap gap-1 mt-2">
                <span
                  v-for="variable in template.variables"
                  :key="variable"
                  class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300"
                >
                  {{ variable }}
                </span>
              </div>
            </div>
          </div>

          <!-- Template Actions -->
          <div class="px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-600">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <button @click.stop="previewTemplate(template)" class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                  {{ $t('Preview') }}
                </button>
                <button @click.stop="duplicateTemplate(template)" class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                  {{ $t('Duplicate') }}
                </button>
              </div>
              <button @click.stop="selectTemplate(template)" class="px-3 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                {{ $t('Use Template') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Template Editor Modal -->
    <div v-if="showEditor" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-slate-900 dark:text-white">
              {{ editingTemplate ? $t('Edit Template') : $t('Create Template') }}
            </h3>
            <button @click="closeEditor" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
              <X class="w-5 h-5" />
            </button>
          </div>
        </div>

        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
          <form @submit.prevent="saveTemplate" class="space-y-4">
            <!-- Template Name -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Template Name') }}</label>
              <input
                v-model="templateForm.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                :placeholder="$t('Enter template name')"
              />
            </div>

            <!-- Template Description -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Description') }}</label>
              <input
                v-model="templateForm.description"
                type="text"
                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                :placeholder="$t('Enter template description')"
              />
            </div>

            <!-- Template Type and Category -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Type') }}</label>
                <select v-model="templateForm.type" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                  <option value="internal">{{ $t('Internal') }}</option>
                  <option value="customer">{{ $t('Customer') }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Category') }}</label>
                <input
                  v-model="templateForm.category"
                  type="text"
                  class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                  :placeholder="$t('Enter category')"
                />
              </div>
            </div>

            <!-- Template Content -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Template Content') }}</label>
              <textarea
                v-model="templateForm.content"
                rows="8"
                required
                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                :placeholder="$t('Enter template content. Use {{variable}} for dynamic content.')"
              ></textarea>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ $t('Use {{variable}} syntax for dynamic content. Available variables: {{customer_name}}, {{ticket_uid}}, {{ticket_subject}}, {{agent_name}}') }}
              </p>
            </div>

            <!-- Variables Preview -->
            <div v-if="templateVariables.length > 0">
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Detected Variables') }}</label>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="variable in templateVariables"
                  :key="variable"
                  class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300"
                >
                  {{ variable }}
                </span>
              </div>
            </div>
          </form>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-600 flex justify-end gap-3">
          <button @click="closeEditor" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
            {{ $t('Cancel') }}
          </button>
          <button @click="saveTemplate" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
            {{ editingTemplate ? $t('Update Template') : $t('Create Template') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Template Preview Modal -->
    <div v-if="showPreview" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-slate-900 dark:text-white">{{ $t('Template Preview') }}</h3>
            <button @click="closePreview" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
              <X class="w-5 h-5" />
            </button>
          </div>
        </div>

        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
          <div class="space-y-4">
            <div>
              <h4 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Template Content') }}</h4>
              <div class="bg-slate-50 dark:bg-slate-700 p-4 rounded-lg">
                <div class="prose dark:prose-invert max-w-none" v-html="previewContent"></div>
              </div>
            </div>
            
            <div v-if="previewTemplate.variables?.length">
              <h4 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Variables') }}</h4>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="variable in previewTemplate.variables"
                  :key="variable"
                  class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300"
                >
                  {{ variable }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-600 flex justify-end gap-3">
          <button @click="closePreview" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
            {{ $t('Close') }}
          </button>
          <button @click="useTemplate(previewTemplate)" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
            {{ $t('Use Template') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  FileText,
  Plus,
  Grid,
  List,
  Search,
  Edit,
  Trash2,
  Tag,
  X,
  Loader2
} from 'lucide-vue-next'

export default {
  name: 'ConversationTemplates',
  components: {
    FileText,
    Plus,
    Grid,
    List,
    Search,
    Edit,
    Trash2,
    Tag,
    X,
    Loader2
  },
  props: {
    ticket: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      loading: false,
      viewMode: 'grid', // 'grid' or 'list'
      searchQuery: '',
      selectedCategory: '',
      selectedType: '',
      showEditor: false,
      showPreview: false,
      editingTemplate: null,
      previewTemplate: null,
      templateForm: {
        name: '',
        description: '',
        type: 'internal',
        category: '',
        content: ''
      },
      templates: [
        {
          id: 1,
          name: 'Initial Response',
          description: 'Standard initial response to customer',
          type: 'customer',
          category: 'Response',
          content: 'Hi {{customer_name}},\n\nThank you for contacting us regarding ticket #{{ticket_uid}} ({{ticket_subject}}). We have received your request and are looking into this matter.\n\nWe will provide you with an update within 24 hours.\n\nBest regards,\n{{agent_name}}',
          variables: ['customer_name', 'ticket_uid', 'ticket_subject', 'agent_name'],
          usage_count: 15
        },
        {
          id: 2,
          name: 'Status Update',
          description: 'Inform customer about status changes',
          type: 'customer',
          category: 'Update',
          content: 'Hi {{customer_name}},\n\nThis is an update regarding your ticket #{{ticket_uid}}.\n\nStatus: {{ticket_status}}\n\n{{status_message}}\n\nIf you have any questions, please don\'t hesitate to contact us.\n\nBest regards,\n{{agent_name}}',
          variables: ['customer_name', 'ticket_uid', 'ticket_status', 'status_message', 'agent_name'],
          usage_count: 8
        },
        {
          id: 3,
          name: 'Internal Discussion',
          description: 'Start internal team discussion',
          type: 'internal',
          category: 'Internal',
          content: 'Team,\n\nTicket #{{ticket_uid}} ({{ticket_subject}}) requires attention.\n\nPriority: {{ticket_priority}}\nCustomer: {{customer_name}}\n\n{{discussion_points}}\n\nPlease review and provide your input.\n\nThanks,\n{{agent_name}}',
          variables: ['ticket_uid', 'ticket_subject', 'ticket_priority', 'customer_name', 'discussion_points', 'agent_name'],
          usage_count: 12
        },
        {
          id: 4,
          name: 'Resolution Update',
          description: 'Inform customer about resolution',
          type: 'customer',
          category: 'Resolution',
          content: 'Hi {{customer_name}},\n\nGreat news! We have resolved your ticket #{{ticket_uid}}.\n\nResolution: {{resolution_details}}\n\nPlease let us know if you need any further assistance.\n\nBest regards,\n{{agent_name}}',
          variables: ['customer_name', 'ticket_uid', 'resolution_details', 'agent_name'],
          usage_count: 6
        }
      ]
    }
  },
  computed: {
    categories() {
      return [...new Set(this.templates.map(t => t.category))]
    },
    filteredTemplates() {
      let filtered = this.templates

      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(template =>
          template.name.toLowerCase().includes(query) ||
          template.description.toLowerCase().includes(query) ||
          template.content.toLowerCase().includes(query)
        )
      }

      if (this.selectedCategory) {
        filtered = filtered.filter(template => template.category === this.selectedCategory)
      }

      if (this.selectedType) {
        filtered = filtered.filter(template => template.type === this.selectedType)
      }

      return filtered
    },
    templateVariables() {
      const matches = this.templateForm.content.match(/\{\{([^}]+)\}\}/g)
      return matches ? [...new Set(matches.map(match => match.replace(/\{\{|\}\}/g, '')))] : []
    },
    previewContent() {
      if (!this.previewTemplate) return ''
      
      let content = this.previewTemplate.content
      
      // Replace variables with sample data
      const sampleData = {
        customer_name: 'John Doe',
        ticket_uid: this.ticket.uid || '12345',
        ticket_subject: this.ticket.subject || 'Sample Subject',
        agent_name: 'Support Team',
        ticket_status: 'In Progress',
        status_message: 'We are currently working on your request.',
        ticket_priority: 'High',
        discussion_points: 'This ticket requires technical expertise.',
        resolution_details: 'The issue has been identified and fixed.'
      }
      
      Object.entries(sampleData).forEach(([key, value]) => {
        content = content.replace(new RegExp(`\\{\\{${key}\\}\\}`, 'g'), value)
      })
      
      return content.replace(/\n/g, '<br>')
    }
  },
  methods: {
    toggleView() {
      this.viewMode = this.viewMode === 'grid' ? 'list' : 'grid'
    },
    createTemplate() {
      this.editingTemplate = null
      this.templateForm = {
        name: '',
        description: '',
        type: 'internal',
        category: '',
        content: ''
      }
      this.showEditor = true
    },
    editTemplate(template) {
      this.editingTemplate = template
      this.templateForm = { ...template }
      this.showEditor = true
    },
    deleteTemplate(template) {
      this.showDeleteConfirmation({
        title: this.$t('Delete Template'),
        message: this.$t('This action cannot be undone.'),
        itemName: template.name,
        itemType: 'template',
        deleteUrl: '', // This will be handled by the confirm callback
        deleteMethod: 'custom'
      }, () => {
        const index = this.templates.findIndex(t => t.id === template.id)
        if (index > -1) {
          this.templates.splice(index, 1)
        }
      });
    },
    duplicateTemplate(template) {
      const newTemplate = {
        ...template,
        id: Date.now(),
        name: `${template.name} (Copy)`,
        usage_count: 0
      }
      this.templates.push(newTemplate)
    },
    previewTemplate(template) {
      this.previewTemplate = template
      this.showPreview = true
    },
    closeEditor() {
      this.showEditor = false
      this.editingTemplate = null
      this.templateForm = {
        name: '',
        description: '',
        type: 'internal',
        category: '',
        content: ''
      }
    },
    closePreview() {
      this.showPreview = false
      this.previewTemplate = null
    },
    saveTemplate() {
      if (this.editingTemplate) {
        // Update existing template
        const index = this.templates.findIndex(t => t.id === this.editingTemplate.id)
        if (index > -1) {
          this.templates[index] = {
            ...this.templateForm,
            id: this.editingTemplate.id,
            variables: this.templateVariables,
            usage_count: this.editingTemplate.usage_count
          }
        }
      } else {
        // Create new template
        const newTemplate = {
          ...this.templateForm,
          id: Date.now(),
          variables: this.templateVariables,
          usage_count: 0
        }
        this.templates.push(newTemplate)
      }
      
      this.closeEditor()
    },
    selectTemplate(template) {
      this.$emit('template-selected', template)
    },
    useTemplate(template) {
      this.$emit('template-selected', template)
      this.closePreview()
    },
    getTypeClass(type) {
      return type === 'internal'
        ? 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300'
        : 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300'
    }
  }
}
</script>

<style scoped>
.conversation-templates {
  @apply flex flex-col h-full;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
