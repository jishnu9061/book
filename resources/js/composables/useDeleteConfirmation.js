import { ref } from 'vue';

export function useDeleteConfirmation() {
    const showDeleteModal = ref(false);
    const deleteConfig = ref({
        title: 'Are you sure?',
        message: 'This action cannot be undone.',
        itemName: 'this item',
        itemType: 'item',
        deleteUrl: '',
        deleteMethod: 'delete',
        deleteData: {},
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    });

    const showDeleteConfirmation = (config) => {
        deleteConfig.value = {
            ...deleteConfig.value,
            ...config
        };
        showDeleteModal.value = true;
    };

    const hideDeleteConfirmation = () => {
        showDeleteModal.value = false;
    };

    const confirmDelete = (callback) => {
        if (callback) {
            callback();
        }
        hideDeleteConfirmation();
    };

    return {
        showDeleteModal,
        deleteConfig,
        showDeleteConfirmation,
        hideDeleteConfirmation,
        confirmDelete
    };
}

