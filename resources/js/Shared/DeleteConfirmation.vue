<script setup>
import { ref, computed } from 'vue';
import { useForm } from "@inertiajs/vue3";
import { router } from '@inertiajs/vue3';
import { 
    AlertTriangle, 
    X, 
    Trash2, 
    User, 
    FileText, 
    MessageSquare, 
    Building, 
    Tag, 
    Globe,
    BookOpen,
    HelpCircle,
    Settings,
    MapPin
} from 'lucide-vue-next';

const emit = defineEmits(['close', 'confirm']);

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: 'Are you sure?'
    },
    message: {
        type: String,
        default: 'This action cannot be undone.'
    },
    itemName: {
        type: String,
        default: 'this item'
    },
    itemType: {
        type: String,
        default: 'item'
    },
    deleteUrl: {
        type: String,
        required: true
    },
    deleteMethod: {
        type: String,
        default: 'delete'
    },
    deleteData: {
        type: Object,
        default: () => ({})
    },
    confirmButtonText: {
        type: String,
        default: 'Delete'
    },
    cancelButtonText: {
        type: String,
        default: 'Cancel'
    },
    isLoading: {
        type: Boolean,
        default: false
    }
});

const isDeleting = ref(false);

// Get appropriate icon based on item type
const getItemIcon = computed(() => {
    const iconMap = {
        'user': User,
        'customer': User,
        'contact': User,
        'ticket': FileText,
        'conversation': MessageSquare,
        'chat': MessageSquare,
        'organization': Building,
        'department': Building,
        'category': Tag,
        'type': Tag,
        'priority': Tag,
        'status': Tag,
        'role': Settings,
        'language': Globe,
        'blog': FileText,
        'post': FileText,
        'knowledge': BookOpen,
        'faq': HelpCircle,
        'note': FileText,
        'template': FileText,
        'rule': Settings,
        'city': MapPin,
        'default': AlertTriangle
    };
    
    return iconMap[props.itemType.toLowerCase()] || iconMap.default;
});

const handleConfirm = async () => {
    if (isDeleting.value) return;
    
    isDeleting.value = true;
    
    try {
        if (props.deleteMethod === 'delete') {
            await router.delete(props.deleteUrl, {
                data: props.deleteData,
                onSuccess: () => {
                    emit('confirm');
                    emit('close');
                },
                onError: () => {
                    isDeleting.value = false;
                },
                onFinish: () => {
                    isDeleting.value = false;
                }
            });
        } else if (props.deleteMethod === 'post') {
            const form = useForm(props.deleteData);
            await form.post(props.deleteUrl, {
                onSuccess: () => {
                    emit('confirm');
                    emit('close');
                },
                onError: () => {
                    isDeleting.value = false;
                },
                onFinish: () => {
                    isDeleting.value = false;
                }
            });
        }
    } catch (error) {
        console.error('Delete error:', error);
        isDeleting.value = false;
    }
};

const handleClose = () => {
    if (!isDeleting.value) {
        emit('close');
    }
};
</script>

<template>
    <!-- Modal Backdrop -->
    <div 
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="handleClose"
    >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 transition-opacity" aria-hidden="true"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div 
                class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md transform transition-all"
                @click.stop
            >
                <!-- Close Button -->
                <button
                    @click="handleClose"
                    :disabled="isDeleting"
                    class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <X class="w-5 h-5" />
                </button>

                <!-- Modal Content -->
                <div class="p-8">
                    <!-- Icon -->
                    <div class="flex justify-center mb-6">
                        <div class="w-16 h-16 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center">
                            <component 
                                :is="getItemIcon" 
                                class="w-8 h-8 text-red-600 dark:text-red-400" 
                            />
                        </div>
                    </div>

                    <!-- Title -->
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white text-center mb-4">
                        {{ title }}
                    </h3>

                    <!-- Message -->
                    <div class="text-slate-600 dark:text-slate-300 text-center mb-8">
                        <p class="mb-2">
                            Do you really want to delete <span class="font-semibold text-slate-900 dark:text-white">{{ itemName }}</span>?
                        </p>
                        <p class="text-sm">
                            {{ message }}
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 justify-center">
                        <!-- Cancel Button -->
                        <button
                            @click="handleClose"
                            :disabled="isDeleting"
                            class="px-6 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-medium hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2"
                        >
                            {{ cancelButtonText }}
                        </button>

                        <!-- Delete Button -->
                        <button
                            @click="handleConfirm"
                            :disabled="isDeleting"
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 flex items-center gap-2"
                        >
                            <Trash2 v-if="!isDeleting" class="w-4 h-4" />
                            <div v-if="isDeleting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                            {{ isDeleting ? 'Deleting...' : confirmButtonText }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
