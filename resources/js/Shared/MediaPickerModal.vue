<script setup>
import {onMounted, ref, watch} from 'vue';
import axios from 'axios';
import NativeUploader from "./NativeUploader.vue";

import { X } from 'lucide-vue-next';
// --- PROPS & EMITS ---
const props = defineProps({
    acceptedTypes: {
        type: Array,
        default: () => ['image'],
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    zIndex: { // Keep the z-index prop from the TinyMCE fix
        type: Number,
        default: 50
    }
});
const emit = defineEmits(['close', 'select']);

// --- STATE ---
const folders = ref([]);
const media = ref({ data: [], links: [] });
const breadcrumbs = ref([]);
const currentFolderId = ref(null);
const isLoading = ref(true);
const selectedFiles = ref([]);

// --- METHOD: Handle Upload ---
const handleUploadComplete = () => {
    // After an upload, simply refetch the data for the current folder.
    fetchData(currentFolderId.value, false);
};

// --- METHOD: Fetch Data (no changes) ---
const fetchData = async (folderId = null, showLoadingIndicator = true) => {
    if (showLoadingIndicator) {
        isLoading.value = true;
    }
    try {
        const response = await axios.get(route('api.media.list'), {
            params: {
                folder_id: folderId,
                types: props.acceptedTypes,
            }
        });
        folders.value = response.data.folders;
        media.value = response.data.media;
        breadcrumbs.value = response.data.breadcrumbs;
    } catch (error) {
        console.error('Failed to fetch media:', error);
    } finally {
        if (showLoadingIndicator) {
            isLoading.value = false;
        }
    }
};

// --- Other methods (no changes needed) ---
const openFolder = (folderId) => {
    currentFolderId.value = folderId;
};

const toggleSelection = (file) => {
    if (!props.multiple) {
        emit('select', file);
        return;
    }
    const index = selectedFiles.value.findIndex(selected => selected.id === file.id);
    if (index === -1) {
        selectedFiles.value.push(file);
    } else {
        selectedFiles.value.splice(index, 1);
    }
};

const isSelected = (file) => {
    return selectedFiles.value.some(selected => selected.id === file.id);
};

const confirmSelection = () => {
    emit('select', selectedFiles.value);
};

const closeModal = () => {
    emit('close');
};

// --- LIFECYCLE & WATCHERS ---
onMounted(() => {
    fetchData(null, true);
});

watch(currentFolderId, (newFolderId) => {
    fetchData(newFolderId, true); // Explicitly show loading state
});

// --- HELPERS ---
const isImage = (mimeType) => mimeType && mimeType.startsWith('image/');

// Note: Drag-and-drop logic for moving files is removed from the modal
// to keep its purpose focused on selection and uploading.
</script>

<template>
    <div class="fixed inset-0 bg-black bg-opacity-60 z-[9999] flex justify-center items-center p-4" @click.self="closeModal">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl h-[80vh] flex flex-col z-[9999]">
            <!-- Modal Header -->
            <div class="p-4 border-b border-gray-300 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold">Select Media</h3>
                    <!-- Breadcrumbs -->
                    <nav class="flex items-center text-xs font-medium text-gray-500">
                        <template v-for="(crumb, index) in breadcrumbs" :key="crumb.id">
                            <span @click="openFolder(crumb.id)" class="cursor-pointer hover:text-indigo-600">
                                {{ crumb.name }}
                            </span>
                            <svg v-if="index < breadcrumbs.length - 1" class="mx-1.5 w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        </template>
                    </nav>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="closeModal" class="p-2 rounded-full hover:bg-gray-300 text-gray-400 hover:text-gray-800">
                        <X class="w-5 h-5 " />
                    </button>
                </div>
            </div>

            <!-- Modal Body (Grid) -->
            <div class="p-4 flex-grow overflow-y-auto">
                <div class="mb-6">
                    <NativeUploader
                        :upload-route="route('api.media.upload')"
                        :folder-id="currentFolderId"
                        @upload-complete="handleUploadComplete"
                    />
                </div>
                <div v-if="isLoading" class="flex justify-center items-center h-full">
                    <p>Loading...</p>
                </div>
                <div v-else class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-4">
                    <!-- Folders -->
                    <div v-for="folder in folders" :key="`folder-${folder.id}`"
                         @click="openFolder(folder.id)"
                         class="aspect-square flex flex-col items-center justify-center bg-gray-100 rounded-lg cursor-pointer hover:bg-indigo-100"
                         @drop="onDrop(folder, $event)" @dragover.prevent @dragenter.prevent>
                        <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                        <p class="w-full text-center px-2 mt-2 text-sm font-medium text-gray-700 truncate">{{ folder.name }}</p>
                    </div>

                    <!-- Media Files -->
                    <div v-for="file in media.data" :key="`file-${file.id}`" @click="toggleSelection(file)"
                         :draggable="true" @dragstart="onDragStart(file, $event)"
                         class="relative group border rounded-lg overflow-hidden shadow-sm cursor-pointer"
                         :class="{ 'ring-2 ring-indigo-500 ring-offset-2': isSelected(file) }">
                        <div v-if="isSelected(file)" class="absolute top-2 right-2 z-10 w-6 h-6 bg-indigo-600 rounded-full flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div class="aspect-square bg-gray-50 flex items-center justify-center">
                            <img v-if="isImage(file.mime_type)" :src="file.thumb_url || file.url" :alt="file.name" class="w-full h-full object-cover">
                            <div v-else class="text-center text-gray-400 p-2">
                                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                <span class="mt-2 block text-xs font-semibold uppercase">{{ file.extension }}</span>
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 p-1.5 bg-black bg-opacity-50 text-white text-xs">
                            <p class="truncate">{{ file.name }}</p>
                        </div>
                    </div>
                </div>
                <!-- TODO: Add Pagination -->
            </div>

            <div v-if="multiple" class="p-4 border-t bg-gray-50 flex justify-between items-center">
                <p class="text-sm text-gray-600">
                    {{ selectedFiles.length }} item(s) selected
                </p>
                <button @click="confirmSelection" class="px-6 py-2 bg-indigo-600 text-white rounded-md font-semibold text-xs uppercase hover:bg-indigo-700 disabled:opacity-50" :disabled="selectedFiles.length === 0">
                    Select Files
                </button>
            </div>
        </div>
    </div>
</template>
