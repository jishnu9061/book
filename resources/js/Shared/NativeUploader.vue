<script setup>
import {ref, computed} from 'vue';

// --- PROPS & EMITS ---
const props = defineProps({
    uploadRoute: {
        type: String,
        required: true,
    },
    folderId: {
        type: [Number, null],
        default: null,
    },
    maxFileSize: {
        type: Number,
        default: 10 * 1024 * 1024, // 10MB in bytes
    },
    acceptedTypes: {
        type: Array,
        default: () => ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    },
    maxFiles: {
        type: Number,
        default: 10,
    },
});
const emit = defineEmits(['upload-complete', 'upload-error', 'upload-progress']);

// --- STATE ---
const isDragging = ref(false);
const filesToUpload = ref([]);
const fileInput = ref(null);
const isUploading = ref(false);

// --- COMPUTED ---
const hasFiles = computed(() => filesToUpload.value.length > 0);
const canAddMoreFiles = computed(() => filesToUpload.value.length < props.maxFiles);
const totalProgress = computed(() => {
    if (filesToUpload.value.length === 0) return 0;
    const total = filesToUpload.value.reduce((sum, file) => sum + file.progress, 0);
    return Math.round(total / filesToUpload.value.length);
});

// --- LOGIC ---
const handleFileChange = (event) => {
    const files = event.target.files || event.dataTransfer.files;
    addFiles(Array.from(files));
    // Reset file input
    if (event.target) event.target.value = '';
};

const addFiles = (fileList) => {
    if (!canAddMoreFiles.value) {
        emit('upload-error', `Maximum ${props.maxFiles} files allowed`);
        return;
    }

    const validFiles = [];
    const errors = [];

    fileList.forEach(file => {
        // Check file size
        if (file.size > props.maxFileSize) {
            errors.push(`${file.name}: File too large (max ${formatBytes(props.maxFileSize)})`);
            return;
        }

        // Check file type
        if (!props.acceptedTypes.includes(file.type)) {
            errors.push(`${file.name}: File type not supported`);
            return;
        }

        // Check if file already exists
        const exists = filesToUpload.value.some(f => f.file.name === file.name && f.file.size === file.size);
        if (exists) {
            errors.push(`${file.name}: File already in queue`);
            return;
        }

        validFiles.push(file);
    });

    // Show errors if any
    if (errors.length > 0) {
        emit('upload-error', errors.join(', '));
    }

    // Add valid files
    if (validFiles.length > 0) {
        const newFiles = validFiles.map(file => ({
            id: Math.random().toString(36).substr(2, 9),
            file: file,
            progress: 0,
            status: 'pending',
            error: null,
            totalSizeFormatted: formatBytes(file.size),
            uploadedSizeFormatted: '0 Bytes',
            uploadSpeed: '0 KB/s',
            timeRemaining: 'Calculating...',
        }));

        filesToUpload.value = [...filesToUpload.value, ...newFiles];
        uploadAllFiles();
    }
};

const uploadAllFiles = () => {
    const pendingFiles = filesToUpload.value.filter(f => f.status === 'pending');
    if (pendingFiles.length === 0) return;

    isUploading.value = true;
    pendingFiles.forEach(fileWrapper => {
        uploadFile(fileWrapper);
    });
};

const formatBytes = (bytes, decimals = 2) => {
    if (!+bytes) return '0 Bytes';
    const k = 1024;
    const dm = decimals < 0 ? 0 : decimals;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
};

const formatSpeed = (bytesPerSecond) => {
    return formatBytes(bytesPerSecond) + '/s';
};

const uploadFile = (fileWrapper) => {
    const formData = new FormData();
    formData.append('file', fileWrapper.file);
    formData.append('folder_id', props.folderId || '');

    fileWrapper.status = 'uploading';
    const startTime = Date.now();
    let lastLoaded = 0;

    axios.post(props.uploadRoute, formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
        onUploadProgress: (progressEvent) => {
            const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
            fileWrapper.progress = percentCompleted;
            fileWrapper.uploadedSizeFormatted = formatBytes(progressEvent.loaded);

            // Calculate upload speed
            const currentTime = Date.now();
            const timeElapsed = (currentTime - startTime) / 1000; // seconds
            if (timeElapsed > 0) {
                const bytesPerSecond = progressEvent.loaded / timeElapsed;
                fileWrapper.uploadSpeed = formatSpeed(bytesPerSecond);

                // Calculate time remaining
                const remainingBytes = progressEvent.total - progressEvent.loaded;
                const timeRemaining = remainingBytes / bytesPerSecond;
                if (timeRemaining < 60) {
                    fileWrapper.timeRemaining = `${Math.round(timeRemaining)}s`;
                } else {
                    fileWrapper.timeRemaining = `${Math.round(timeRemaining / 60)}m ${Math.round(timeRemaining % 60)}s`;
                }
            }

            emit('upload-progress', {
                fileId: fileWrapper.id,
                progress: percentCompleted,
                uploaded: progressEvent.loaded,
                total: progressEvent.total
            });
        },
    }).then(response => {
        fileWrapper.uploadedSizeFormatted = fileWrapper.totalSizeFormatted;
        fileWrapper.status = 'complete';
        fileWrapper.uploadSpeed = '';
        fileWrapper.timeRemaining = '';

        // Check if all files are complete
        const allComplete = filesToUpload.value.every(f => f.status === 'complete' || f.status === 'error');
        if (allComplete) {
            isUploading.value = false;
            emit('upload-complete');
        }
    }).catch(error => {
        fileWrapper.status = 'error';
        isUploading.value = false;

        let errorMessage = 'An upload error occurred.';
        if (error.response) {
            if (error.response.data && error.response.data.errors) {
                errorMessage = Object.values(error.response.data.errors)[0][0];
            } else if (error.response.data && error.response.data.message) {
                errorMessage = error.response.data.message;
            } else if (error.response.status === 413) {
                errorMessage = 'File too large for upload';
            } else if (error.response.status === 422) {
                errorMessage = 'Invalid file format';
            }
        } else if (error.code === 'NETWORK_ERROR') {
            errorMessage = 'Network error. Please check your connection.';
        }

        fileWrapper.error = errorMessage;
        emit('upload-error', errorMessage);
    });
};

const removeFile = (fileId) => {
    filesToUpload.value = filesToUpload.value.filter(fw => fw.id !== fileId);

    // Check if all files are removed
    if (filesToUpload.value.length === 0) {
        isUploading.value = false;
    }
};

const clearAllFiles = () => {
    filesToUpload.value = [];
    isUploading.value = false;
};

const retryUpload = (fileId) => {
    const fileWrapper = filesToUpload.value.find(f => f.id === fileId);
    if (fileWrapper && fileWrapper.status === 'error') {
        fileWrapper.status = 'pending';
        fileWrapper.error = null;
        fileWrapper.progress = 0;
        uploadFile(fileWrapper);
    }
};

const openFileDialog = () => {
    fileInput.value?.click();
};

// --- DRAG & DROP HANDLERS ---
const onDragEnter = (event) => {
    event.preventDefault();
    event.stopPropagation();
    isDragging.value = true;
};

const onDragOver = (event) => {
    event.preventDefault();
    event.stopPropagation();
};

const onDragLeave = (event) => {
    event.preventDefault();
    event.stopPropagation();
    // Only set dragging to false if we're leaving the drop zone entirely
    if (!event.currentTarget.contains(event.relatedTarget)) {
        isDragging.value = false;
    }
};

const onDrop = (event) => {
    event.preventDefault();
    event.stopPropagation();
    isDragging.value = false;
    handleFileChange(event);
};
</script>

<template>
    <div
        class="border-2 border-dashed rounded-lg p-4 mb-6 text-center transition-all duration-300"
        :class="[
            isDragging
                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 scale-105'
                : 'border-gray-300 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500',
            'bg-gray-50 dark:bg-gray-700'
        ]"
        @dragenter="onDragEnter"
        @dragover="onDragOver"
        @dragleave="onDragLeave"
        @drop="onDrop"
    >
        <!-- Hidden file input -->
        <input
            ref="fileInput"
            type="file"
            multiple
            class="hidden"
            :accept="acceptedTypes.join(',')"
            @change="handleFileChange"
        >

        <!-- Upload Area -->
        <div v-if="!hasFiles">
            <div class="text-center">
                <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 dark:bg-gray-600 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Upload Files</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Drag and drop files here, or
                    <button
                        type="button"
                        class="font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 focus:outline-none focus:underline"
                        @click="openFileDialog"
                    >
                        browse
                    </button>
                </p>

                <!-- File Type Info -->
                <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                    <p>Supported: Images (JPG, PNG, GIF, SVG), Documents (PDF, DOC, DOCX)</p>
                    <p>Maximum: {{ formatBytes(maxFileSize) }} per file • Up to {{ maxFiles }} files</p>
                </div>
            </div>
        </div>

        <!-- Upload Progress Header -->
        <div v-if="hasFiles" class="mb-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ isUploading ? 'Uploading Files' : 'Upload Queue' }}
                </h3>
                <div class="flex items-center gap-2">
                    <button
                        v-if="!isUploading"
                        @click="openFileDialog"
                        class="px-3 py-1 text-sm text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-md transition-colors"
                    >
                        Add More
                    </button>
                    <button
                        @click="clearAllFiles"
                        class="px-3 py-1 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md transition-colors"
                    >
                        Clear All
                    </button>
                </div>
            </div>

            <!-- Overall Progress Bar -->
            <div v-if="isUploading" class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mb-2">
                <div
                    class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                    :style="{ width: totalProgress + '%' }"
                ></div>
            </div>
        </div>

        <!-- Upload Queue / Progress List -->
        <div v-if="hasFiles" class="space-y-3 text-left">
            <div
                v-for="fileWrapper in filesToUpload"
                :key="fileWrapper.id"
                class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4"
            >
                <!-- Status Icon -->
                <div class="flex-shrink-0">
                    <div v-if="fileWrapper.status === 'complete'" class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div v-else-if="fileWrapper.status === 'error'" class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div v-else-if="fileWrapper.status === 'uploading'" class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <div v-else class="w-8 h-8 rounded-full bg-gray-300 dark:bg-gray-600 text-gray-600 dark:text-gray-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- File Info and Progress -->
                <div class="flex-grow overflow-hidden min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ fileWrapper.file.name }}</p>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ fileWrapper.totalSizeFormatted }}</span>
                    </div>

                    <!-- Progress Bar -->
                    <div v-if="fileWrapper.status === 'uploading'" class="space-y-2">
                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                            <div
                                class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                :style="{ width: fileWrapper.progress + '%' }"
                            ></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ fileWrapper.progress }}%</span>
                            <div class="flex items-center gap-3">
                                <span v-if="fileWrapper.uploadSpeed">{{ fileWrapper.uploadSpeed }}</span>
                                <span v-if="fileWrapper.timeRemaining">{{ fileWrapper.timeRemaining }} left</span>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-else-if="fileWrapper.status === 'error'" class="space-y-2">
                        <p class="text-xs text-red-600 dark:text-red-400">{{ fileWrapper.error }}</p>
                        <button
                            @click="retryUpload(fileWrapper.id)"
                            class="text-xs text-blue-600 dark:text-blue-400 hover:underline"
                        >
                            Retry upload
                        </button>
                    </div>

                    <!-- Success Message -->
                    <div v-else-if="fileWrapper.status === 'complete'" class="text-xs text-green-600 dark:text-green-400">
                        Upload complete
                    </div>

                    <!-- Pending Message -->
                    <div v-else class="text-xs text-gray-500 dark:text-gray-400">
                        Waiting to upload...
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex-shrink-0 flex items-center gap-2">
                    <button
                        v-if="fileWrapper.status === 'error'"
                        @click="retryUpload(fileWrapper.id)"
                        class="p-1 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded"
                        title="Retry upload"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                    <button
                        @click="removeFile(fileWrapper.id)"
                        class="p-1 text-gray-400 dark:text-gray-500 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded"
                        title="Remove file"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
