<!-- resources/js/Components/RichEditor.vue -->
<script setup>
import {computed, onUnmounted, ref, watchEffect} from 'vue';
import {nanoid} from "nanoid";
import Editor from '@tinymce/tinymce-vue';
import MediaPickerModal from "./MediaPickerModal.vue";
// --- Static Imports for Modern TinyMCE for Maximum Reliability ---
import 'tinymce/tinymce';
import 'tinymce/themes/silver/theme';
import 'tinymce/icons/default/icons';
import 'tinymce/models/dom/model';

import 'tinymce/plugins/preview';
import 'tinymce/plugins/importcss';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/autosave';
import 'tinymce/plugins/save';
import 'tinymce/plugins/directionality';
import 'tinymce/plugins/code';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/visualchars';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/image';
import 'tinymce/plugins/link';
import 'tinymce/plugins/media';
import 'tinymce/plugins/codesample';
import 'tinymce/plugins/table';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/pagebreak';
import 'tinymce/plugins/nonbreaking';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/wordcount';
import 'tinymce/plugins/help';
import 'tinymce/plugins/quickbars';
import 'tinymce/plugins/emoticons';
import Icon from "./Icon.vue";


// -----------------------------------------------------------------

const isEditorVisible = ref(true);


const props = defineProps({
    id: {
        type: String,
        default: () => `rich-editor-${nanoid()}`
    },
    modelValue: {
        type: String,
        default: ''
    },
    enableMedia: {
        type: Boolean,
        default: true
    },
    height: {
        type: [String, Number],
        default: 500
    },
    compact: {
        type: Boolean,
        default: false
    },
});

const emit = defineEmits(['update:modelValue']);

const isDark = ref(window.matchMedia('(prefers-color-scheme: dark)').matches);
watchEffect(() => {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    const handleChange = () => isDark.value = mediaQuery.matches;
    mediaQuery.addEventListener('change', handleChange);
    onUnmounted(() => mediaQuery.removeEventListener('change', handleChange));
});

const showUIBlockModal = ref(false);
const editorInstance = ref(null);


const showMediaModal = ref(false);
const tinymceCallback = ref(null);
const filePickerMeta = ref(null);

// Full toolbar configuration
const toolbarFull = 'undo redo bold italic underline strikethrough fontfamily fontsize blocks alignleft aligncenter alignright alignjustify | insertUIBlock | outdent indent numlist bullist | forecolor backcolor removeformat pagebreak | charmap emoticons fullscreen preview | anchor codesample ltr rtl | code';

// Compact toolbar configuration (essential tools only)
const toolbarCompact = 'bold italic underline | alignleft aligncenter alignright | numlist bullist | link | code';

const toolbars = computed(() => {
    if (props.compact) {
        // In compact mode, only show media if explicitly enabled
        return props.enableMedia ? `${toolbarCompact} | image link` : toolbarCompact;
    } else {
        // Full mode with optional media
        const mediaPart = ' image media link';
        return props.enableMedia ? `${toolbarFull} |${mediaPart}` : toolbarFull;
    }
});

const currentValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});



/**
 * This function is called when MediaPickerModal emits the 'select' event.
 * @param {object} file - The file object {id, url, name} from our component.
 */
function onFileSelected(file) {
    if (!file) { // Handle case where modal is closed without selection
        showMediaModal.value = false;
        tinymceCallback.value = null;
        return;
    }

    let content;
    if (file.mime_type.startsWith('image/')) {
        content = `<img src="${file.url}" alt="${file.name}" />`;
    } else {
        // For other files (PDFs, etc.), insert a link.
        content = `<a href="${file.url}" target="_blank">${file.name}</a>`;
    }

    const activeEditor = tinymce.get(props.id);
    if(activeEditor){
        if (tinymceCallback.value) {
            tinymceCallback.value(file.url, { alt: file.name });
        } else if (editorInstance.value) {
            editorInstance.value.execCommand('mceInsertContent', false, content);
        }
    }


    // Clean up
    showMediaModal.value = false;
    tinymceCallback.value = null;
    filePickerMeta.value = null;
}

function triggerMediaManager(meta = null) {
    tinymceCallback.value = null;
    filePickerMeta.value = meta; // Store what kind of file we're looking for
    showMediaModal.value = true;
}

// --- FINALIZED CONFIGURATION FOR TINYMCE v6/v7 ---
const editorConfig = computed(() => ({
    // Use local TinyMCE assets
    base_url: '/js/tinymce',
    suffix: '.min',
    skin_url: '/js/tinymce/skins/ui/oxide' + (isDark.value ? '-dark' : ''),
    skin: isDark.value ? 'oxide-dark' : 'oxide',
    content_css: [
        '/css/tinymce.css',
        isDark.value ? 'dark' : ''
    ].filter(Boolean),

    // Fallback configuration
    init_instance_callback: (editor) => {
    },

    // License and promotion for self-hosted open-source version
    license_key: 'gpl',
    promotion: false,

    // Schema settings to allow our custom tag and placeholder attributes
    extended_valid_elements: 'shortcode',

    protect: [
        /\<shortcode\>\[.*?\]\<\/shortcode\>/g
    ],

    noneditable_class: 'ui-block-shortcode',

    plugins: props.compact 
        ? 'autolink code image link lists quickbars' 
        : 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help quickbars emoticons',
    menubar: props.compact ? false : 'file edit view insert format tools table help',
    toolbar_mode: 'wrap',
    toolbar: toolbars.value,
    height: props.height,


    // The setup function is the single source of all dynamic editor logic
    setup: (editor) => {
        // Add error handling
        editor.on('error', (e) => {
            console.error('TinyMCE Error:', e);
        });

        // Handle load errors
        editor.on('LoadError', (e) => {
            console.error('TinyMCE Load Error:', e);
        });


        // The 'init' event is the correct place for filters and instance capture
        editor.on('init', () => {
            // Capture the fully initialized editor instance
            editorInstance.value = editor;

            // Emit content change for word count (auto-save disabled)
            editor.on('input change undo redo', () => {
                emit('update:modelValue', editor.getContent());
            });

            // PARSER: Converts <shortcode> from DB into a placeholder div on load
            editor.parser.addNodeFilter('shortcode', (nodes) => {
                for (const node of nodes) {
                    const shortcodeText = node.firstChild ? node.firstChild.value : '';
                    const label = `UI Block: ${shortcodeText.split(' ')[0].substring(1).replace(']', '')}`;

                    node.name = 'div';
                    node.attr('class', 'ui-block-shortcode');
                    node.attr('contenteditable', 'false');
                    // We don't need data-shortcode anymore, as the real content is protected.

                    node.empty();
                    node.append(editor.getDoc().createTextNode(label));
                }
            });

            // SERIALIZER: Converts the placeholder div back to <shortcode> on save
            editor.serializer.addNodeFilter('div', (nodes) => {
                for (const node of nodes) {
                    if (node.attr('class') === 'ui-block-shortcode') {
                        const shortcodeText = node.attr('data-shortcode');
                        node.name = 'shortcode';
                        // Clean up all placeholder attributes
                        node.attr('class', null);
                        node.attr('data-shortcode', null);
                        node.attr('contenteditable', null);
                        node.attr('style', null);
                        node.empty();
                        node.append(editor.getDoc().createTextNode(shortcodeText));
                    }
                }
            });
        });
    },


    file_picker_callback: (callback, value, meta) => {
        tinymceCallback.value = callback;
        filePickerMeta.value = meta; // Store meta info
        showMediaModal.value = true;
    },

    file_picker_types: 'file image media',

    image_advtab: true,
    importcss_append: true,
    quickbars_selection_toolbar: props.compact 
        ? 'bold italic | quicklink' 
        : 'bold italic | quicklink h2 h3 blockquote | forecolor backcolor',
    quickbars_insert_toolbar: false,


    forced_root_block: 'p',
    newline_behavior: 'linebreak',
    remove_linebreaks: false,
    cleanup: false,
    verify_html: false,
    entity_encoding: 'raw'

}));

onUnmounted(() => {
    if (editorInstance.value) {
        try {
            const editor = tinymce.get(editorInstance.value.id);
            if (editor) {
                editor.destroy();
            }
        } catch (error) {
            console.warn('Error destroying TinyMCE editor:', error);
        }
        editorInstance.value = null;
    }
});


const mediaPickerAcceptedTypes = computed(() => {
    if (!filePickerMeta.value) return ['image', 'video', 'pdf']; // Default for custom button
    switch (filePickerMeta.value.filetype) {
        case 'image':
            return ['image'];
        case 'media':
            return ['video'];
        case 'file':
            return ['pdf']; // Or whatever you consider a 'file'
        default:
            return ['image', 'video', 'pdf'];
    }
});
</script>
<template>
    <div :class="{ 'tinymce-dark': isDark, 'compact': compact }" class="rich-editor-wrapper">
        <!-- The modern wrapper uses v-model directly -->
        <div v-if="enableMedia && !compact" class="rich-editor-toolbar">
            <button
                type="button"
                @click="triggerMediaManager"
                class="btn btn-primary rich-editor-button"
            >
                <Icon name="media" class="w-4 h-4" />
                Add Media
            </button>
        </div>
        <MediaPickerModal
            v-if="showMediaModal"
            :accepted-types="mediaPickerAcceptedTypes"
            @close="showMediaModal = false"
            @select="onFileSelected"
        />
        <div class="editor-content-area">
          <Editor
              :id="id"
              :init="editorConfig"
              v-model="currentValue"
          />
        </div>
    </div>
</template>


<style>
/*
This CSS MUST be in your GLOBAL stylesheet that is loaded via the `content_css` prop.
e.g., in `resources/css/admin.scss`
*/

/* Inside the TinyMCE editor iframe */
.tox-tinymce{
    border: none !important;
}
.ui-block-shortcode {
    background: #f3f4f6;
    padding: 1rem;
    border: 1px dashed #cbd5e1;
    border-radius: 4px;
    margin: 1rem 0;
    cursor: default;
    user-select: none; /* This is key for the non-editable "feel" */
}

/* Dark mode styles */
body.dark .ui-block-shortcode {
    background: #374151;
    border-color: #4b5563;
    color: #d1d5db;
}

.rich-editor-wrapper {
    border: 1px solid #ccc;
    border-radius: 4px;
    /* The wrapper should manage the layout */
    display: flex;
    flex-direction: column;
}

.rich-editor-toolbar {
    padding: 8px;
    background-color: #f0f0f0;
    border-bottom: 1px solid #ccc;
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
    display: flex;
    gap: 8px; /* Adds space between buttons */
}

.rich-editor-button, .rich-editor-button-secondary {
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    display: flex !important;
    width: auto !important;
    gap: 6px;
    align-items: center;
}

/* --- Dark Mode Styles --- */
.tinymce-dark .rich-editor-wrapper {
    border-color: #555;
}
.tinymce-dark .rich-editor-toolbar {
    background-color: #333;
    border-bottom-color: #555;
}
.tinymce-dark .rich-editor-button-secondary {
    background-color: #444;
    border: 1px solid #666;
}
.tinymce-dark .rich-editor-button-secondary:hover {
    background-color: #555;
}
.source-code-textarea.dark {
    background-color: #1e1e1e;
    color: #d4d4d4;
}

/* Remove border from the editor itself since the wrapper now has it */
:deep(.tox-tinymce) {
    border: none !important;
    /* Ensure the editor fills its container */
    height: v-bind(height + 'px') !important;
}

:deep(.cm-editor) {
    outline: none !important;
    border: none !important;
    height: 100%; /* Make it fill the container div's height */
}

/* Compact mode styles */
.rich-editor-wrapper.compact {
    border-radius: 6px;
}

.rich-editor-wrapper.compact :deep(.tox-tinymce) {
    border-radius: 6px;
}

.rich-editor-wrapper.compact :deep(.tox-toolbar) {
    padding: 4px 8px;
    min-height: 36px;
}

.rich-editor-wrapper.compact :deep(.tox-tbtn) {
    margin: 2px;
}

.rich-editor-wrapper.compact :deep(.tox-tbtn--enabled) {
    background-color: rgba(0, 0, 0, 0.1);
}
</style>
