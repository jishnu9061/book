<template>
  <div class="relative">
    <!-- Attachment Menu -->
    <div 
      v-if="showAttachmentMenu" 
      class="absolute bottom-full left-0 mb-2 w-80 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden z-50"
    >
      <!-- Header -->
      <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('Attach File') }}</h3>
          <button 
            @click="closeMenu"
            class="p-1 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors duration-200"
          >
            <X class="w-4 h-4" />
          </button>
        </div>
      </div>

      <!-- Upload Area -->
      <div class="p-4">
        <!-- Drag & Drop Zone -->
        <div 
          ref="dropZone"
          @drop="handleDrop"
          @dragover="handleDragOver"
          @dragenter="handleDragEnter"
          @dragleave="handleDragLeave"
          :class="[
            'border-2 border-dashed rounded-lg p-6 text-center transition-all duration-200 cursor-pointer',
            isDragOver 
              ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' 
              : 'border-slate-300 dark:border-slate-600 hover:border-slate-400 dark:hover:border-slate-500'
          ]"
          @click="triggerFileInput"
        >
          <div class="space-y-3">
            <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto">
              <Upload class="w-6 h-6 text-slate-500 dark:text-slate-400" />
            </div>
            <div>
              <p class="text-sm font-medium text-slate-900 dark:text-white">
                {{ isDragOver ? $t('Drop files here') : $t('Click to upload or drag & drop') }}
              </p>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ $t('Images, documents, videos up to 10MB') }}
              </p>
            </div>
          </div>
        </div>

        <!-- File Input (Hidden) -->
        <input 
          ref="fileInput"
          type="file"
          multiple
          accept="image/*,video/*,.pdf,.doc,.docx,.txt,.zip,.rar"
          @change="handleFileSelect"
          class="hidden"
        />

        <!-- Quick Upload Options -->
        <div class="mt-4 grid grid-cols-2 gap-2">
          <button 
            @click="triggerFileInput"
            class="flex items-center gap-2 p-3 text-left bg-slate-50 dark:bg-slate-700 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200"
          >
            <File class="w-4 h-4 text-slate-500 dark:text-slate-400" />
            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $t('Files') }}</span>
          </button>
          <button 
            @click="triggerImageInput"
            class="flex items-center gap-2 p-3 text-left bg-slate-50 dark:bg-slate-700 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors duration-200"
          >
            <Image class="w-4 h-4 text-slate-500 dark:text-slate-400" />
            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $t('Images') }}</span>
          </button>
        </div>

        <!-- Selected Files Preview -->
        <div v-if="selectedFiles.length > 0" class="mt-4 space-y-2">
          <h4 class="text-sm font-medium text-slate-900 dark:text-white">{{ $t('Selected Files') }}</h4>
          <div class="space-y-2 max-h-32 overflow-y-auto">
            <div 
              v-for="(file, index) in selectedFiles" 
              :key="index"
              class="flex items-center gap-3 p-2 bg-slate-50 dark:bg-slate-700 rounded-lg"
            >
              <!-- File Icon -->
              <div class="w-8 h-8 bg-slate-200 dark:bg-slate-600 rounded flex items-center justify-center flex-shrink-0">
                <component :is="getFileIcon(file.type)" class="w-4 h-4 text-slate-500 dark:text-slate-400" />
              </div>
              
              <!-- File Info -->
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ file.name }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ formatFileSize(file.size) }}</p>
              </div>
              
              <!-- Remove Button -->
              <button 
                @click="removeFile(index)"
                class="p-1 text-slate-400 hover:text-red-500 transition-colors duration-200"
              >
                <X class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>

        <!-- Upload Progress -->
        <div v-if="uploading" class="mt-4">
          <div class="flex items-center gap-2 mb-2">
            <div class="w-4 h-4 border-2 border-primary-500 border-t-transparent rounded-full animate-spin"></div>
            <span class="text-sm text-slate-700 dark:text-slate-300">{{ $t('Uploading...') }}</span>
          </div>
          <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
            <div 
              class="bg-primary-500 h-2 rounded-full transition-all duration-300"
              :style="{ width: uploadProgress + '%' }"
            ></div>
          </div>
        </div>

        <!-- Actions -->
        <div class="mt-4 flex gap-2">
          <button 
            @click="closeMenu"
            class="flex-1 px-4 py-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors duration-200"
          >
            {{ $t('Cancel') }}
          </button>
          <button 
            @click="uploadFiles"
            :disabled="selectedFiles.length === 0 || uploading"
            class="flex-1 px-4 py-2 bg-primary-600 text-white text-sm rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
          >
            {{ $t('Attach') }} ({{ selectedFiles.length }})
          </button>
        </div>
      </div>
    </div>

    <!-- Backdrop -->
    <div 
      v-if="showAttachmentMenu" 
      @click="closeMenu"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { 
  Upload, 
  X, 
  File, 
  Image, 
  FileText, 
  Video, 
  Music, 
  Archive,
  FileImage,
  FileVideo,
  FileAudio
} from 'lucide-vue-next'
import axios from 'axios'

export default {
  name: 'AttachmentUpload',
  components: {
    Upload,
    X,
    File,
    Image,
    FileText,
    Video,
    Music,
    Archive,
    FileImage,
    FileVideo,
    FileAudio
  },
  props: {
    showAttachmentMenu: {
      type: Boolean,
      default: false
    },
    conversationId: {
      type: [String, Number],
      required: true
    }
  },
  emits: ['close', 'files-uploaded'],
  setup(props, { emit }) {
    const dropZone = ref(null)
    const fileInput = ref(null)
    const isDragOver = ref(false)
    const selectedFiles = ref([])
    const uploading = ref(false)
    const uploadProgress = ref(0)

    const closeMenu = () => {
      emit('close')
      selectedFiles.value = []
      isDragOver.value = false
      uploading.value = false
      uploadProgress.value = 0
    }

    const triggerFileInput = () => {
      fileInput.value.click()
    }

    const triggerImageInput = () => {
      const input = document.createElement('input')
      input.type = 'file'
      input.accept = 'image/*'
      input.multiple = true
      input.onchange = (e) => handleFileSelect(e)
      input.click()
    }

    const handleFileSelect = (event) => {
      const files = Array.from(event.target.files)
      addFiles(files)
    }

    const handleDrop = (event) => {
      event.preventDefault()
      isDragOver.value = false
      const files = Array.from(event.dataTransfer.files)
      addFiles(files)
    }

    const handleDragOver = (event) => {
      event.preventDefault()
    }

    const handleDragEnter = (event) => {
      event.preventDefault()
      isDragOver.value = true
    }

    const handleDragLeave = (event) => {
      event.preventDefault()
      if (!dropZone.value.contains(event.relatedTarget)) {
        isDragOver.value = false
      }
    }

    const addFiles = (files) => {
      const validFiles = files.filter(file => {
        // Check file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
          alert(`File ${file.name} is too large. Maximum size is 10MB.`)
          return false
        }
        return true
      })

      selectedFiles.value = [...selectedFiles.value, ...validFiles]
    }

    const removeFile = (index) => {
      selectedFiles.value.splice(index, 1)
    }

    const getFileIcon = (fileType) => {
      if (fileType.startsWith('image/')) return FileImage
      if (fileType.startsWith('video/')) return FileVideo
      if (fileType.startsWith('audio/')) return FileAudio
      if (fileType.includes('pdf')) return FileText
      if (fileType.includes('zip') || fileType.includes('rar')) return Archive
      return File
    }

    const formatFileSize = (bytes) => {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }

    const uploadFiles = async () => {
      if (selectedFiles.value.length === 0) return

      uploading.value = true
      uploadProgress.value = 0

      try {
        const formData = new FormData()
        formData.append('conversation_id', props.conversationId)
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'))

        selectedFiles.value.forEach((file, index) => {
          formData.append(`files[${index}]`, file)
        })

        const response = await axios.post('/chat/upload-attachments', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          onUploadProgress: (progressEvent) => {
            uploadProgress.value = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          }
        })

        if (response.data.success) {
          emit('files-uploaded', response.data.attachments)
          closeMenu()
        } else {
          throw new Error(response.data.message || 'Upload failed')
        }
      } catch (error) {
        console.error('Upload error:', error)
        alert('Failed to upload files. Please try again.')
      } finally {
        uploading.value = false
        uploadProgress.value = 0
      }
    }

    return {
      dropZone,
      fileInput,
      isDragOver,
      selectedFiles,
      uploading,
      uploadProgress,
      closeMenu,
      triggerFileInput,
      triggerImageInput,
      handleFileSelect,
      handleDrop,
      handleDragOver,
      handleDragEnter,
      handleDragLeave,
      removeFile,
      getFileIcon,
      formatFileSize,
      uploadFiles
    }
  }
}
</script>

<style scoped>
/* Custom scrollbar for file list */
.max-h-32::-webkit-scrollbar {
  width: 4px;
}

.max-h-32::-webkit-scrollbar-track {
  background: transparent;
}

.max-h-32::-webkit-scrollbar-thumb {
  background-color: rgb(203 213 225);
  border-radius: 2px;
}

.dark .max-h-32::-webkit-scrollbar-thumb {
  background-color: rgb(71 85 105);
}
</style>
