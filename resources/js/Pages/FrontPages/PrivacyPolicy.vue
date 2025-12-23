<template>
    <div>
        <Head :title="$t('Privacy Policy')" />
        <div class="bg-white rounded-md shadow overflow-hidden mr-2">
            <form @submit.prevent="update">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <text-input v-model="form.html.title" class="pr-6 pb-8 w-full lg:w-1/2" :label="$t('Title')" />
                    <div class="pr-6 pb-8 w-full">
                        <label class="form-label" >Page Content:</label>
                        <RichEditor v-model="form.html.content" class="mt-1" />
                <div v-if="form.errors.details" class="text-red-600 text-sm mt-1">{{ form.errors.details }}</div>
                    </div>
                </div>
                <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
                    <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">{{ $t('Save') }}</loading-button>
                </div>
            </form>

        </div>
    </div>
</template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'

import RichEditor from '@/Shared/RichEditor.vue';

export default {
    metaInfo: { title: 'Contact' },
    components: {
        Link,
        Head,
        TextInput,
        LoadingButton,
        RichEditor,
        },
    layout: Layout,
    props: {
        page: Object,
    },
    remember: 'form',
    data() {
        return {
            tabs:[
                {'name': 'Overview', 'active': true},
                {'name': 'List Information', 'active': false},
                {'name': 'Bottom', 'active': false},
            ],
            form: this.$inertia.form({
                title: 'Privacy Policy',
                slug: 'privacy',
                is_active: this.page.is_active,
                html: JSON.parse(this.page.html),
            }),
        }
    },
    methods: {
        update() {
            this.form.put(this.route('front_pages.update', 'privacy'))
        },
        activeTab(index){
            for (const tab_item of this.tabs) {
                tab_item.active = false
            }
            this.tabs[index].active = true;
        },
    },
    mounted() {

    }
}
</script>
