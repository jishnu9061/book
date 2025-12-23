<template>
    <div>
        <Head :title="$t('Footer Area')" />
        <div class="bg-white rounded-md shadow overflow-hidden mr-2">
            <form @submit.prevent="update">
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <textarea-input :rows="3" v-model="form.html.text" class="pr-6 pb-8 w-full" :label="$t('Footer text')" />
                </div>
                <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
                    <text-input v-model="form.html.copyright" class="pr-6 pb-8 w-full lg:w-1/2" :label="$t('Copyright info')" />
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
import TextareaInput from "@/Shared/TextareaInput.vue";


export default {
    metaInfo: { title: 'Footer Area' },
    components: {
        TextareaInput,
        Link,
        Head,
        TextInput,
        LoadingButton,
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
                title: 'Footer Area',
                slug: 'footer',
                is_active: this.page.is_active,
                html: JSON.parse(this.page.html),
            }),
        }
    },
    methods: {
        update() {
            this.form.put(this.route('front_pages.update', 'footer'))
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
