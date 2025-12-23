<template>
  <div class="conversation-export">
    <!-- Export Header -->
    <div class="px-4 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-600">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-slate-900 dark:text-white flex items-center gap-2">
          <Download class="w-5 h-5" />
          {{ $t('Export Conversations') }}
        </h3>
        <button @click="closeExport" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
          <X class="w-5 h-5" />
        </button>
      </div>
    </div>

    <!-- Export Options -->
    <div class="p-4 space-y-6">
      <!-- Export Format -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">{{ $t('Export Format') }}</label>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div
            v-for="format in exportFormats"
            :key="format.id"
            @click="selectedFormat = format.id"
            class="relative cursor-pointer rounded-lg border-2 p-4 transition-colors"
            :class="selectedFormat === format.id 
              ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' 
              : 'border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500'"
          >
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="format.color">
                <component :is="format.icon" class="w-5 h-5 text-white" />
              </div>
              <div>
                <h4 class="text-sm font-medium text-slate-900 dark:text-white">{{ format.name }}</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ format.description }}</p>
              </div>
            </div>
            <div v-if="selectedFormat === format.id" class="absolute top-2 right-2">
              <Check class="w-5 h-5 text-blue-600" />
            </div>
          </div>
        </div>
      </div>

      <!-- Date Range -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">{{ $t('Date Range') }}</label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">{{ $t('From Date') }}</label>
            <input
              v-model="dateRange.from"
              type="date"
              class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">{{ $t('To Date') }}</label>
            <input
              v-model="dateRange.to"
              type="date"
              class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
            />
          </div>
        </div>
        <div class="mt-2 flex flex-wrap gap-2">
          <button
            v-for="preset in datePresets"
            :key="preset.value"
            @click="setDatePreset(preset.value)"
            class="px-3 py-1 text-xs font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 rounded-full hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors"
          >
            {{ preset.label }}
          </button>
        </div>
      </div>

      <!-- Conversation Types -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">{{ $t('Conversation Types') }}</label>
        <div class="space-y-2">
          <label class="flex items-center">
            <input v-model="conversationTypes.internal" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">{{ $t('Internal Conversations') }}</span>
          </label>
          <label class="flex items-center">
            <input v-model="conversationTypes.customer" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">{{ $t('Customer Conversations') }}</span>
          </label>
        </div>
      </div>

      <!-- Export Options -->
      <div>
        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">{{ $t('Export Options') }}</label>
        <div class="space-y-3">
          <label class="flex items-center">
            <input v-model="exportOptions.includeAttachments" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">{{ $t('Include Attachments') }}</span>
          </label>
          <label class="flex items-center">
            <input v-model="exportOptions.includeMetadata" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">{{ $t('Include Metadata') }}</span>
          </label>
          <label class="flex items-center">
            <input v-model="exportOptions.includeSystemMessages" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">{{ $t('Include System Messages') }}</span>
          </label>
          <label class="flex items-center">
            <input v-model="exportOptions.includeTimestamps" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">{{ $t('Include Timestamps') }}</span>
          </label>
        </div>
      </div>

      <!-- Export Preview -->
      <div v-if="exportPreview" class="bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4">
        <h4 class="text-sm font-medium text-slate-900 dark:text-white mb-2">{{ $t('Export Preview') }}</h4>
        <div class="text-sm text-slate-600 dark:text-slate-400 space-y-1">
          <div class="flex justify-between">
            <span>{{ $t('Total Conversations') }}:</span>
            <span>{{ exportPreview.totalConversations }}</span>
          </div>
          <div class="flex justify-between">
            <span>{{ $t('Total Messages') }}:</span>
            <span>{{ exportPreview.totalMessages }}</span>
          </div>
          <div class="flex justify-between">
            <span>{{ $t('Date Range') }}:</span>
            <span>{{ formatDateRange(exportPreview.dateRange) }}</span>
          </div>
          <div class="flex justify-between">
            <span>{{ $t('Estimated Size') }}:</span>
            <span>{{ formatFileSize(exportPreview.estimatedSize) }}</span>
          </div>
        </div>
      </div>

      <!-- Export Actions -->
      <div class="flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-600">
        <button @click="closeExport" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
          {{ $t('Cancel') }}
        </button>
        <button @click="generatePreview" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
          {{ $t('Preview Export') }}
        </button>
        <button @click="startExport" :disabled="exporting" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
          <Loader2 v-if="exporting" class="w-4 h-4 mr-2 animate-spin" />
          {{ $t('Export') }}
        </button>
      </div>
    </div>

    <!-- Export Progress Modal -->
    <div v-if="showProgress" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600">
          <h3 class="text-lg font-medium text-slate-900 dark:text-white">{{ $t('Exporting Conversations') }}</h3>
        </div>

        <div class="p-6">
          <div class="space-y-4">
            <div class="flex items-center justify-between text-sm text-slate-600 dark:text-slate-400">
              <span>{{ $t('Progress') }}</span>
              <span>{{ exportProgress }}%</span>
            </div>
            
            <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
              <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" :style="{ width: `${exportProgress}%` }"></div>
            </div>
            
            <div class="text-sm text-slate-500 dark:text-slate-400">
              {{ exportStatus }}
            </div>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-600 flex justify-end">
          <button @click="cancelExport" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
            {{ $t('Cancel') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  Download,
  X,
  Check,
  Loader2,
  FileText,
  FileSpreadsheet,
  Archive
} from 'lucide-vue-next'
import moment from 'moment'

export default {
  name: 'ConversationExport',
  components: {
    Download,
    X,
    Check,
    Loader2,
    FileText,
    FileSpreadsheet,
    Archive
  },
  props: {
    ticketId: {
      type: [String, Number],
      required: true
    },
    conversations: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      moment: moment,
      selectedFormat: 'pdf',
      dateRange: {
        from: '',
        to: ''
      },
      conversationTypes: {
        internal: true,
        customer: true
      },
      exportOptions: {
        includeAttachments: true,
        includeMetadata: true,
        includeSystemMessages: false,
        includeTimestamps: true
      },
      exportPreview: null,
      exporting: false,
      showProgress: false,
      exportProgress: 0,
      exportStatus: '',
      exportFormats: [
        {
          id: 'pdf',
          name: 'PDF',
          description: 'Portable Document Format',
          icon: FileText,
          color: 'bg-red-500'
        },
        {
          id: 'excel',
          name: 'Excel',
          description: 'Microsoft Excel Spreadsheet',
          icon: FileSpreadsheet,
          color: 'bg-green-500'
        },
        {
          id: 'csv',
          name: 'CSV',
          description: 'Comma Separated Values',
          icon: FileText,
          color: 'bg-blue-500'
        },
        {
          id: 'json',
          name: 'JSON',
          description: 'JavaScript Object Notation',
          icon: FileText,
          color: 'bg-purple-500'
        },
        {
          id: 'zip',
          name: 'ZIP Archive',
          description: 'Compressed Archive with Attachments',
          icon: Archive,
          color: 'bg-orange-500'
        }
      ],
      datePresets: [
        { label: 'Last 7 days', value: '7d' },
        { label: 'Last 30 days', value: '30d' },
        { label: 'Last 90 days', value: '90d' },
        { label: 'This year', value: 'year' },
        { label: 'All time', value: 'all' }
      ]
    }
  },
  mounted() {
    this.setDefaultDateRange()
  },
  methods: {
    setDefaultDateRange() {
      const today = new Date()
      const thirtyDaysAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000)
      
      this.dateRange.from = thirtyDaysAgo.toISOString().split('T')[0]
      this.dateRange.to = today.toISOString().split('T')[0]
    },
    
    setDatePreset(preset) {
      const today = new Date()
      let fromDate = new Date()
      
      switch (preset) {
        case '7d':
          fromDate.setDate(today.getDate() - 7)
          break
        case '30d':
          fromDate.setDate(today.getDate() - 30)
          break
        case '90d':
          fromDate.setDate(today.getDate() - 90)
          break
        case 'year':
          fromDate = new Date(today.getFullYear(), 0, 1)
          break
        case 'all':
          fromDate = new Date('2020-01-01')
          break
      }
      
      this.dateRange.from = fromDate.toISOString().split('T')[0]
      this.dateRange.to = today.toISOString().split('T')[0]
    },
    
    async generatePreview() {
      try {
        // Simulate preview generation
        await new Promise(resolve => setTimeout(resolve, 1000))
        
        this.exportPreview = {
          totalConversations: this.conversations.length,
          totalMessages: this.conversations.reduce((total, conv) => total + (conv.messages?.length || 0), 0),
          dateRange: {
            from: this.dateRange.from,
            to: this.dateRange.to
          },
          estimatedSize: this.calculateEstimatedSize()
        }
      } catch (error) {
        console.error('Error generating preview:', error)
      }
    },
    
    calculateEstimatedSize() {
      // Rough estimation based on conversation count and options
      let baseSize = this.conversations.length * 1024 // 1KB per conversation
      
      if (this.exportOptions.includeAttachments) {
        baseSize *= 2 // Double for attachments
      }
      
      if (this.exportOptions.includeMetadata) {
        baseSize *= 1.5 // 50% more for metadata
      }
      
      return baseSize
    },
    
    async startExport() {
      this.exporting = true
      this.showProgress = true
      this.exportProgress = 0
      this.exportStatus = this.$t('Preparing export...')
      
      try {
        // Simulate export process
        const steps = [
          { progress: 20, status: this.$t('Collecting conversations...') },
          { progress: 40, status: this.$t('Processing messages...') },
          { progress: 60, status: this.$t('Generating file...') },
          { progress: 80, status: this.$t('Finalizing export...') },
          { progress: 100, status: this.$t('Export complete!') }
        ]
        
        for (const step of steps) {
          await new Promise(resolve => setTimeout(resolve, 1000))
          this.exportProgress = step.progress
          this.exportStatus = step.status
        }
        
        // Simulate file download
        await this.downloadExport()
        
        this.closeExport()
        
      } catch (error) {
        console.error('Error during export:', error)
        this.exportStatus = this.$t('Export failed')
      } finally {
        this.exporting = false
      }
    },
    
    async downloadExport() {
      // Simulate file download
      const filename = `conversations_export_${moment().format('YYYY-MM-DD')}.${this.selectedFormat}`
      const content = this.generateExportContent()
      
      const blob = new Blob([content], { type: this.getMimeType() })
      const url = URL.createObjectURL(blob)
      
      const link = document.createElement('a')
      link.href = url
      link.download = filename
      link.click()
      
      URL.revokeObjectURL(url)
    },
    
    generateExportContent() {
      // Generate content based on selected format
      switch (this.selectedFormat) {
        case 'pdf':
          return this.generatePdfContent()
        case 'excel':
          return this.generateExcelContent()
        case 'csv':
          return this.generateCsvContent()
        case 'json':
          return this.generateJsonContent()
        case 'zip':
          return this.generateZipContent()
        default:
          return ''
      }
    },
    
    generatePdfContent() {
      // Simple PDF content generation (in real app, use a PDF library)
      return `PDF Export of Conversations\n\nTicket ID: ${this.ticketId}\nDate Range: ${this.dateRange.from} to ${this.dateRange.to}\n\n${this.conversations.map(conv => `Conversation: ${conv.subject}\nMessages: ${conv.messages?.length || 0}\n`).join('\n')}`
    },
    
    generateExcelContent() {
      // Simple CSV content (in real app, use Excel library)
      const headers = ['Conversation ID', 'Subject', 'Type', 'Messages', 'Created At']
      const rows = this.conversations.map(conv => [
        conv.id,
        conv.subject,
        conv.type,
        conv.messages?.length || 0,
        conv.created_at
      ])
      
      return [headers, ...rows].map(row => row.join(',')).join('\n')
    },
    
    generateCsvContent() {
      return this.generateExcelContent() // Same as Excel for simplicity
    },
    
    generateJsonContent() {
      return JSON.stringify({
        ticket_id: this.ticketId,
        export_date: new Date().toISOString(),
        date_range: this.dateRange,
        conversations: this.conversations
      }, null, 2)
    },
    
    generateZipContent() {
      // Simple text representation (in real app, create actual ZIP)
      return `ZIP Archive of Conversations\n\nTicket ID: ${this.ticketId}\nDate Range: ${this.dateRange.from} to ${this.dateRange.to}\n\nThis would contain:\n- Conversation files\n- Attachments\n- Metadata`
    },
    
    getMimeType() {
      const mimeTypes = {
        pdf: 'application/pdf',
        excel: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        csv: 'text/csv',
        json: 'application/json',
        zip: 'application/zip'
      }
      return mimeTypes[this.selectedFormat] || 'text/plain'
    },
    
    cancelExport() {
      this.exporting = false
      this.showProgress = false
      this.exportProgress = 0
      this.exportStatus = ''
    },
    
    closeExport() {
      this.$emit('close')
    },
    
    formatDateRange(range) {
      if (!range.from || !range.to) return this.$t('Not specified')
      return `${moment(range.from).format('MMM D, YYYY')} - ${moment(range.to).format('MMM D, YYYY')}`
    },
    
    formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes'
      
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }
  }
}
</script>

<style scoped>
.conversation-export {
  @apply flex flex-col h-full;
}
</style>
