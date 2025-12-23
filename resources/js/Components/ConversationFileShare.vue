<template>
  <div class="conversation-file-share">
    <!-- File Upload Area -->
    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
      <div class="flex items-center gap-3">
        <div class="flex-1">
          <div
            ref="dropZone"
            @drop="handleDrop"
            @dragover="handleDragOver"
            @dragenter="handleDragEnter"
            @dragleave="handleDragLeave"
            class="relative border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg p-6 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors"
            :class="{ 'border-blue-400 bg-blue-50 dark:bg-blue-900/20': isDragOver }"
          >
            <input
              ref="fileInput"
              type="file"
              multiple
              @change="handleFileSelect"
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
              accept="*/*"
            />
            
            <div class="space-y-2">
              <Upload class="w-8 h-8 mx-auto text-slate-400 dark:text-slate-500" />
              <div class="text-sm text-slate-600 dark:text-slate-400">
                <span class="font-medium">{{ $t('Drop files here') }}</span>
                <span class="block">{{ $t('or click to browse') }}</span>
              </div>
              <div class="text-xs text-slate-500 dark:text-slate-500">
                {{ $t('Supports all file types up to 10MB each') }}
              </div>
            </div>
          </div>
        </div>
        
        <div class="flex flex-col gap-2">
          <button @click="openFileDialog" class="px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
            <Upload class="w-4 h-4 mr-2" />
            {{ $t('Upload') }}
          </button>
          <button @click="openFolderDialog" class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-600 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-500 transition-colors">
            <FolderOpen class="w-4 h-4 mr-2" />
            {{ $t('Folder') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Selected Files Preview -->
    <div v-if="selectedFiles.length > 0" class="p-4 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-600">
      <div class="flex items-center justify-between mb-3">
        <h4 class="text-sm font-medium text-slate-900 dark:text-white">{{ $t('Selected Files') }} ({{ selectedFiles.length }})</h4>
        <button @click="clearFiles" class="text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
          {{ $t('Clear All') }}
        </button>
      </div>
      
      <div class="space-y-2">
        <div
          v-for="(file, index) in selectedFiles"
          :key="index"
          class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600"
        >
          <div class="flex-shrink-0">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="getFileTypeClass(file.type)">
              <component :is="getFileIcon(file.type)" class="w-5 h-5 text-white" />
            </div>
          </div>
          
          <div class="flex-1 min-w-0">
            <div class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ file.name }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-400">{{ formatFileSize(file.size) }}</div>
          </div>
          
          <div class="flex items-center gap-2">
            <div v-if="file.uploading" class="flex items-center gap-2">
              <Loader2 class="w-4 h-4 animate-spin text-blue-600" />
              <span class="text-xs text-slate-500 dark:text-slate-400">{{ file.progress }}%</span>
            </div>
            <div v-else-if="file.uploaded" class="flex items-center gap-2">
              <Check class="w-4 h-4 text-green-500" />
              <span class="text-xs text-green-600 dark:text-green-400">{{ $t('Uploaded') }}</span>
            </div>
            <div v-else-if="file.error" class="flex items-center gap-2">
              <X class="w-4 h-4 text-red-500" />
              <span class="text-xs text-red-600 dark:text-red-400">{{ file.error }}</span>
            </div>
            <button @click="removeFile(index)" class="p-1 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
              <X class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
      
      <div class="mt-4 flex justify-end gap-2">
        <button @click="clearFiles" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
          {{ $t('Cancel') }}
        </button>
        <button @click="uploadFiles" :disabled="uploading" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
          <Loader2 v-if="uploading" class="w-4 h-4 mr-2 animate-spin" />
          {{ $t('Upload Files') }}
        </button>
      </div>
    </div>

    <!-- Shared Files List -->
    <div class="flex-1 overflow-y-auto p-4">
      <div class="flex items-center justify-between mb-4">
        <h4 class="text-sm font-medium text-slate-900 dark:text-white">{{ $t('Shared Files') }}</h4>
        <div class="flex items-center gap-2">
          <button @click="toggleView" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
            <Grid v-if="viewMode === 'list'" class="w-4 h-4" />
            <List v-else class="w-4 h-4" />
          </button>
          <button @click="refreshFiles" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
            <RefreshCw class="w-4 h-4" />
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-8">
        <Loader2 class="w-6 h-6 animate-spin text-blue-600" />
        <span class="ml-2 text-slate-600 dark:text-slate-400">{{ $t('Loading files...') }}</span>
      </div>

      <!-- Empty State -->
      <div v-else-if="sharedFiles.length === 0" class="text-center py-8">
        <FileText class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-600" />
        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No files shared yet') }}</h3>
        <p class="text-slate-500 dark:text-slate-400">{{ $t('Upload files to share with the team') }}</p>
      </div>

      <!-- Files List -->
      <div v-else :class="viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4' : 'space-y-2'">
        <div
          v-for="file in sharedFiles"
          :key="file.id"
          class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden hover:shadow-md transition-shadow"
        >
          <!-- File Header -->
          <div class="p-4 border-b border-slate-200 dark:border-slate-600">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center" :class="getFileTypeClass(file.mime_type)">
                <component :is="getFileIcon(file.mime_type)" class="w-5 h-5 text-white" />
              </div>
              <div class="flex-1 min-w-0">
                <h5 class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ file.name }}</h5>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatFileSize(file.size) }}</p>
              </div>
            </div>
          </div>

          <!-- File Details -->
          <div class="p-4">
            <div class="space-y-2">
              <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span>{{ $t('Uploaded by') }}</span>
                <span>{{ file.uploaded_by?.name || 'Unknown' }}</span>
              </div>
              <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span>{{ $t('Date') }}</span>
                <span>{{ formatDate(file.created_at) }}</span>
              </div>
              <div v-if="file.download_count > 0" class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span>{{ $t('Downloads') }}</span>
                <span>{{ file.download_count }}</span>
              </div>
            </div>
          </div>

          <!-- File Actions -->
          <div class="px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-600">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <button @click="previewFile(file)" class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                  {{ $t('Preview') }}
                </button>
                <button @click="downloadFile(file)" class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                  {{ $t('Download') }}
                </button>
              </div>
              <button @click="shareFile(file)" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 transition-colors">
                {{ $t('Share') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- File Preview Modal -->
    <div v-if="showPreview" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-slate-900 dark:text-white">{{ previewFile?.name }}</h3>
            <button @click="closePreview" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
              <X class="w-5 h-5" />
            </button>
          </div>
        </div>

        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
          <div class="text-center">
            <div v-if="isImageFile(previewFile?.mime_type)" class="max-w-full">
              <img :src="previewFile?.url" :alt="previewFile?.name" class="max-w-full h-auto rounded-lg" />
            </div>
            <div v-else-if="isPdfFile(previewFile?.mime_type)" class="w-full h-96">
              <iframe :src="previewFile?.url" class="w-full h-full rounded-lg" frameborder="0"></iframe>
            </div>
            <div v-else class="text-slate-500 dark:text-slate-400">
              <FileText class="w-16 h-16 mx-auto mb-4" />
              <p>{{ $t('Preview not available for this file type') }}</p>
            </div>
          </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-600 flex justify-end gap-3">
          <button @click="closePreview" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
            {{ $t('Close') }}
          </button>
          <button @click="downloadFile(previewFile)" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
            {{ $t('Download') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  Upload,
  FolderOpen,
  FileText,
  Image,
  File,
  Video,
  Music,
  Archive,
  Code,
  Check,
  X,
  Loader2,
  Grid,
  List,
  RefreshCw
} from 'lucide-vue-next'
import moment from 'moment'

export default {
  name: 'ConversationFileShare',
  components: {
    Upload,
    FolderOpen,
    FileText,
    Image,
    File,
    Video,
    Music,
    Archive,
    Code,
    Check,
    X,
    Loader2,
    Grid,
    List,
    RefreshCw
  },
  props: {
    conversationId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      moment: moment,
      selectedFiles: [],
      sharedFiles: [],
      loading: false,
      uploading: false,
      isDragOver: false,
      viewMode: 'grid',
      showPreview: false,
      previewFile: null,
      // Sample data for demonstration
      sampleFiles: [
        {
          id: 1,
          name: 'document.pdf',
          size: 1024000,
          mime_type: 'application/pdf',
          url: '/files/document.pdf',
          uploaded_by: { name: 'John Doe' },
          created_at: new Date().toISOString(),
          download_count: 5
        },
        {
          id: 2,
          name: 'screenshot.png',
          size: 512000,
          mime_type: 'image/png',
          url: '/files/screenshot.png',
          uploaded_by: { name: 'Jane Smith' },
          created_at: new Date(Date.now() - 86400000).toISOString(),
          download_count: 3
        },
        {
          id: 3,
          name: 'spreadsheet.xlsx',
          size: 2048000,
          mime_type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
          url: '/files/spreadsheet.xlsx',
          uploaded_by: { name: 'Mike Johnson' },
          created_at: new Date(Date.now() - 172800000).toISOString(),
          download_count: 8
        }
      ]
    }
  },
  mounted() {
    this.loadSharedFiles()
  },
  methods: {
    async loadSharedFiles() {
      this.loading = true
      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 1000))
        this.sharedFiles = this.sampleFiles
      } catch (error) {
        console.error('Error loading shared files:', error)
      } finally {
        this.loading = false
      }
    },
    openFileDialog() {
      this.$refs.fileInput.click()
    },
    openFolderDialog() {
      // Open folder selection dialog
      console.log('Open folder dialog')
    },
    handleFileSelect(event) {
      const files = Array.from(event.target.files)
      this.addFiles(files)
    },
    handleDrop(event) {
      event.preventDefault()
      this.isDragOver = false
      const files = Array.from(event.dataTransfer.files)
      this.addFiles(files)
    },
    handleDragOver(event) {
      event.preventDefault()
      this.isDragOver = true
    },
    handleDragEnter(event) {
      event.preventDefault()
      this.isDragOver = true
    },
    handleDragLeave(event) {
      event.preventDefault()
      this.isDragOver = false
    },
    addFiles(files) {
      const validFiles = files.filter(file => {
        if (file.size > 10 * 1024 * 1024) { // 10MB limit
          console.warn(`File ${file.name} is too large`)
          return false
        }
        return true
      })
      
      validFiles.forEach(file => {
        this.selectedFiles.push({
          ...file,
          uploading: false,
          uploaded: false,
          error: null,
          progress: 0
        })
      })
    },
    removeFile(index) {
      this.selectedFiles.splice(index, 1)
    },
    clearFiles() {
      this.selectedFiles = []
    },
    async uploadFiles() {
      this.uploading = true
      
      for (let i = 0; i < this.selectedFiles.length; i++) {
        const file = this.selectedFiles[i]
        file.uploading = true
        file.progress = 0
        
        try {
          // Simulate upload progress
          for (let progress = 0; progress <= 100; progress += 10) {
            file.progress = progress
            await new Promise(resolve => setTimeout(resolve, 100))
          }
          
          // Simulate successful upload
          file.uploaded = true
          file.uploading = false
          
          // Add to shared files
          this.sharedFiles.unshift({
            id: Date.now() + i,
            name: file.name,
            size: file.size,
            mime_type: file.type,
            url: URL.createObjectURL(file),
            uploaded_by: { name: 'Current User' },
            created_at: new Date().toISOString(),
            download_count: 0
          })
          
        } catch (error) {
          file.error = 'Upload failed'
          file.uploading = false
        }
      }
      
      this.uploading = false
      this.selectedFiles = []
    },
    previewFile(file) {
      this.previewFile = file
      this.showPreview = true
    },
    closePreview() {
      this.showPreview = false
      this.previewFile = null
    },
    downloadFile(file) {
      const link = document.createElement('a')
      link.href = file.url
      link.download = file.name
      link.click()
    },
    shareFile(file) {
      // Share file functionality
      console.log('Sharing file:', file)
    },
    refreshFiles() {
      this.loadSharedFiles()
    },
    toggleView() {
      this.viewMode = this.viewMode === 'grid' ? 'list' : 'grid'
    },
    getFileIcon(mimeType) {
      if (!mimeType) return File
      
      if (mimeType.startsWith('image/')) return Image
      if (mimeType.startsWith('video/')) return Video
      if (mimeType.startsWith('audio/')) return Music
      if (mimeType.includes('pdf')) return FileText
      if (mimeType.includes('zip') || mimeType.includes('rar')) return Archive
      if (mimeType.includes('text/') || mimeType.includes('javascript') || mimeType.includes('json')) return Code
      
      return File
    },
    getFileTypeClass(mimeType) {
      if (!mimeType) return 'bg-slate-500'
      
      if (mimeType.startsWith('image/')) return 'bg-green-500'
      if (mimeType.startsWith('video/')) return 'bg-purple-500'
      if (mimeType.startsWith('audio/')) return 'bg-pink-500'
      if (mimeType.includes('pdf')) return 'bg-red-500'
      if (mimeType.includes('zip') || mimeType.includes('rar')) return 'bg-orange-500'
      if (mimeType.includes('text/') || mimeType.includes('javascript') || mimeType.includes('json')) return 'bg-blue-500'
      
      return 'bg-slate-500'
    },
    isImageFile(mimeType) {
      return mimeType && mimeType.startsWith('image/')
    },
    isPdfFile(mimeType) {
      return mimeType && mimeType.includes('pdf')
    },
    formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes'
      
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    },
    formatDate(date) {
      return moment(date).format('MMM D, YYYY')
    }
  }
}
</script>

<style scoped>
.conversation-file-share {
  @apply flex flex-col h-full;
}
</style>
