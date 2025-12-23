<template>
    <div>
        <Head :title="$t(title)" />

        <!-- Reading Progress Bar -->
        <div class="fixed top-0 left-0 w-full h-1 bg-blue-600 z-[200]">
            <div
                class="h-full bg-gray-600 transition-all duration-300 ease-out"
                :style="{ width: readingProgress + '%' }"
            ></div>
        </div>

        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 pt-24 pb-16 md:pt-32 md:pb-20">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <!-- Breadcrumb -->
                    <nav class="mb-8" aria-label="Breadcrumb">
                        <ol class="flex items-center justify-center space-x-2 text-white/80 text-sm">
                            <li><Link :href="route('blog')" class="hover:text-white transition-colors">Blog</Link></li>
                            <li><ChevronRight class="w-4 h-4" /></li>
                            <li class="text-white font-medium">{{ post.title }}</li>
                        </ol>
                    </nav>

                    <!-- Title -->
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white leading-tight mb-6">
                        {{ post.title }}
                    </h1>

                    <!-- Meta Information -->
                    <div class="flex flex-wrap items-center justify-center gap-6 text-white/90 text-sm md:text-base">
                        <!-- Author -->
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-white/20 flex items-center justify-center">
                                <img v-if="post.author?.photo_path"
                                     :src="post.author.photo_path"
                                     :alt="author_name"
                                     class="w-full h-full object-cover">
                                <User v-else class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <p class="font-medium">By {{ author_name }}</p>
                            </div>
                        </div>

                        <!-- Date -->
                        <div class="flex items-center gap-2">
                            <Calendar class="w-4 h-4" />
                            <span>{{ moment(post.updated_at).format('MMM DD, YYYY') }}</span>
                        </div>

                        <!-- Reading Time -->
                        <div class="flex items-center gap-2">
                            <Clock class="w-4 h-4" />
                            <span>{{ getReadingTime(post.details) }}</span>
                        </div>

                        <!-- Share Button -->
                        <div class="relative share-menu-container">
                            <button @click="toggleShareMenu"
                                    class="flex items-center gap-2 px-4 py-2 bg-white/20 hover:bg-white/30 rounded-full transition-colors">
                                <Share2 class="w-4 h-4" />
                                <span>Share</span>
                            </button>
                        </div>
                    </div>

                    <!-- Share Menu -->
                    <div v-if="showShareMenu"
                         class="absolute top-full left-1/2 transform -translate-x-1/2 mt-4 bg-white rounded-lg shadow-xl p-4 z-50 share-menu-container">
                        <div class="flex items-center gap-4">
                            <button @click="shareOnSocial('twitter')"
                                    class="flex items-center gap-2 px-3 py-2 text-blue-600 hover:bg-blue-50 rounded transition-colors">
                                <Twitter class="w-4 h-4" />
                                <span>Twitter</span>
                            </button>
                            <button @click="shareOnSocial('facebook')"
                                    class="flex items-center gap-2 px-3 py-2 text-blue-800 hover:bg-blue-50 rounded transition-colors">
                                <Facebook class="w-4 h-4" />
                                <span>Facebook</span>
                            </button>
                            <button @click="shareOnSocial('linkedin')"
                                    class="flex items-center gap-2 px-3 py-2 text-blue-700 hover:bg-blue-50 rounded transition-colors">
                                <Linkedin class="w-4 h-4" />
                                <span>LinkedIn</span>
                            </button>
                            <button @click="copyLink"
                                    class="flex items-center gap-2 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded transition-colors">
                                <LinkIcon class="w-4 h-4" />
                                <span>Copy Link</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Background Decoration -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-white/10 rounded-full blur-3xl"></div>
            </div>
        </section>


        <!-- Main Content Section -->
        <section class="py-16 bg-gray-50 -mt-8">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 pt-8">
                        <!-- Main Content -->
                        <article class="lg:col-span-3">
                            <!-- Featured Image -->
                            <div class="mb-8 rounded-2xl overflow-hidden shadow-lg">
                                <img v-if="post.image"
                                     :src="post.image"
                                     :alt="post.title"
                                     class="w-full h-64 md:h-80 lg:h-96 object-cover"
                                     loading="lazy">
                                <div v-else class="w-full h-64 md:h-80 lg:h-96 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                    <ImageIcon class="w-16 h-16 text-gray-400" />
                                </div>
                            </div>

                            <!-- Article Content -->
                            <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
                                <!-- Article Body -->
                                <div class="prose prose-lg max-w-none">
                                    <div class="post-details html" v-html="sanitizeHtml(post.details)"></div>
                                </div>

                                <!-- Tags -->
                                <div v-if="types.length" class="mt-12 pt-8 border-t border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                        <Tag class="w-5 h-5 text-blue-600" />
                                        Tags
                                    </h3>
                                    <div class="flex flex-wrap gap-2">
                                        <Link v-for="type in types"
                                              :key="type.id"
                                              :href="route('blog.by_type', type.id)"
                                              class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors">
                                            <Tag class="w-3 h-3" />
                                            {{ type.name }}
                                        </Link>
                                    </div>
                                </div>

                                <!-- Article Actions -->
                                <div class="mt-12 pt-8 border-t border-gray-200">
                                    <div class="flex flex-wrap items-center justify-between gap-4">
                                        <div class="flex items-center gap-3">
                                            <!-- Print Button -->
                                            <button @click="printArticle"
                                                    class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                                <Printer class="w-4 h-4" />
                                                <span>Print</span>
                                            </button>

                                            <!-- Bookmark Button -->
                                            <button @click="toggleBookmark"
                                                    class="flex items-center gap-2 px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                                <Bookmark class="w-4 h-4" />
                                                <span>Bookmark</span>
                                            </button>
                                        </div>

                                        <!-- Back to Blog -->
                                        <Link :href="route('blog')"
                                              class="flex items-center gap-2 px-4 py-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors">
                                            <ArrowLeft class="w-4 h-4" />
                                            <span>Back to Blog</span>
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <!-- Sidebar -->
                        <aside class="lg:col-span-1">
                            <div class="lg:sticky lg:top-32 space-y-8">
                                <!-- Recent Posts -->
                                <div class="bg-white rounded-2xl shadow-lg p-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                                        <BookOpen class="w-5 h-5 text-blue-600" />
                                        Recent Articles
                                    </h3>
                                    <div class="space-y-4">
                                        <div v-for="recent_post in recent_posts" :key="recent_post.id"
                                             class="group border-b border-gray-100 pb-4 last:border-b-0 last:pb-0">
                                            <Link :href="route('blog.details', recent_post.id)"
                                                  class="block hover:bg-gray-50 -m-2 p-2 rounded-lg transition-colors">
                                                <div class="flex gap-4">
                                                    <div class="flex-shrink-0">
                                                        <img v-if="recent_post.image"
                                                             :src="recent_post.image"
                                                             :alt="recent_post.title"
                                                             class="w-16 h-16 rounded-lg object-cover">
                                                        <div v-else class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                                            <ImageIcon class="w-6 h-6 text-gray-400" />
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <h4 class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                                            {{ recent_post.title }}
                                                        </h4>
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            {{ moment(recent_post.updated_at).format('MMM DD, YYYY') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </Link>
                                        </div>
                                    </div>
                                </div>

                                <!-- Table of Contents (if content has headings) -->
                                <div v-if="tableOfContents.length" class="bg-white rounded-2xl shadow-lg p-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                                        <List class="w-5 h-5 text-blue-600" />
                                        Table of Contents
                                    </h3>
                                    <nav class="space-y-2">
                                        <a v-for="(item, index) in tableOfContents"
                                           :key="index"
                                           :href="'#' + item.id"
                                           class="block text-sm text-gray-600 hover:text-blue-600 transition-colors py-1"
                                           :class="{ 'ml-4': item.level === 3 }">
                                            {{ item.text }}
                                        </a>
                                    </nav>
                                </div>

                                <!-- Newsletter Signup -->
                                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-lg p-6 text-white">
                                    <h3 class="text-xl font-bold mb-2 flex items-center gap-2">
                                        <Mail class="w-5 h-5" />
                                        Stay Updated
                                    </h3>
                                    <p class="text-white/90 text-sm mb-4">Get the latest articles delivered to your inbox.</p>
                                    <form @submit.prevent="subscribeNewsletter" class="space-y-3">
                                        <div class="relative">
                                            <Mail class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                                            <input v-model="newsletterEmail"
                                                   type="email"
                                                   placeholder="Enter your email"
                                                   class="w-full pl-10 pr-4 py-2 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white/50"
                                                   required>
                                        </div>
                                        <button type="submit"
                                                class="w-full bg-white text-blue-600 font-semibold py-2 px-4 rounded-lg hover:bg-gray-100 transition-colors flex items-center justify-center gap-2">
                                            <Mail class="w-4 h-4" />
                                            Subscribe
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </section>

        <!-- Related Articles Section -->
        <section v-if="related_posts.length" class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Related Articles</h2>
                        <p class="text-lg text-gray-600">Continue reading with these related posts</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <article v-for="related_post in related_posts" :key="related_post.id"
                                 class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                            <Link :href="route('blog.details', related_post.id)" class="block">
                                <div class="aspect-w-16 aspect-h-9 overflow-hidden">
                                    <img v-if="related_post.image"
                                         :src="related_post.image"
                                         :alt="related_post.title"
                                         class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div v-else class="w-full h-48 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                        <ImageIcon class="w-12 h-12 text-gray-400" />
                                    </div>
                                </div>

                                <div class="p-6">
                                    <div class="flex items-center gap-2 mb-3">
                                        <Calendar class="w-4 h-4 text-gray-400" />
                                        <span class="text-sm text-gray-500">{{ moment(related_post.updated_at).format('MMM DD, YYYY') }}</span>
                                    </div>

                                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors mb-3 line-clamp-2">
                                        {{ related_post.title }}
                                    </h3>

                                    <p class="text-gray-600 text-sm line-clamp-3" v-html="getExcerpt(related_post.details, 120)"></p>

                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center text-blue-600 font-semibold text-sm group-hover:gap-2 transition-all">
                                            <span>Read More</span>
                                            <ArrowRight class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" />
                                        </div>
                                        <button class="flex items-center gap-1 text-gray-400 hover:text-red-500 transition-colors">
                                            <Heart class="w-4 h-4" />
                                            <span class="text-xs">Like</span>
                                        </button>
                                    </div>
                                </div>
                            </Link>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="bg-white rounded-2xl shadow-lg p-8 md:p-12">
                        <div class="flex items-center justify-center gap-3 mb-4">
                            <Mail class="w-8 h-8 text-blue-600" />
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Stay in the Loop</h2>
                        </div>
                        <p class="text-lg text-gray-600 mb-8">Get the latest articles, tips, and insights delivered straight to your inbox.</p>

                        <form @submit.prevent="subscribeNewsletter" class="max-w-md mx-auto">
                            <div class="flex gap-3">
                                <div class="relative flex-1">
                                    <Mail class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input v-model="newsletterEmail"
                                           type="email"
                                           placeholder="Enter your email address"
                                           class="w-full pl-12 pr-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           required>
                                </div>
                                <button type="submit"
                                        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                    <Mail class="w-4 h-4" />
                                    Subscribe
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
import Layout from '@/Shared/Landing/Layout.vue'
import {Head, Link} from '@inertiajs/vue3'
import Subscribe from '@/Shared/Landing/Subscribe.vue'
import sanitizeHtml from "sanitize-html";
import moment from 'moment'
import {
    User,
    Calendar,
    Clock,
    Share2,
    Twitter,
    Facebook,
    Linkedin,
    LinkIcon,
    Printer,
    ArrowLeft,
    ArrowRight,
    ChevronRight,
    ImageIcon,
    List,
    BookOpen,
    Tag,
    Mail,
    Heart,
    Bookmark
} from 'lucide-vue-next'

export default {
    layout: Layout,
    props: {
        post: Object,
        recent_posts: Array,
        related_posts: Array,
        types: Array,
    },
    components: {
        Head,
        Link,
        Subscribe,
        User,
        Calendar,
        Clock,
        Share2,
        Twitter,
        Facebook,
        Linkedin,
        LinkIcon,
        Printer,
        ArrowLeft,
        ArrowRight,
        ChevronRight,
        ImageIcon,
        List,
        BookOpen,
        Tag,
        Mail,
        Heart,
        Bookmark
    },
    data(){
        return {
            title: this.post.title,
            author_name: '',
            showShareMenu: false,
            readingProgress: 0,
            newsletterEmail: '',
            tableOfContents: []
        }
    },
    methods: {
        getReadingTime(text){
            const wpm = 225;
            const words = text.trim().split(/\s+/).length;
            const time = Math.ceil(words / wpm);
            return time+' minute read';
        },
        sanitizeHtml: sanitizeHtml,
        toggleShareMenu() {
            this.showShareMenu = !this.showShareMenu;
        },
        shareOnSocial(platform) {
            const url = window.location.href;
            const title = this.post.title;
            const text = `Check out this article: ${title}`;

            let shareUrl = '';
            switch(platform) {
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
                    break;
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                    break;
                case 'linkedin':
                    shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`;
                    break;
            }

            if (shareUrl) {
                window.open(shareUrl, '_blank', 'width=600,height=400');
            }
            this.showShareMenu = false;
        },
        async copyLink() {
            try {
                await navigator.clipboard.writeText(window.location.href);
                // You could add a toast notification here
                alert('Link copied to clipboard!');
            } catch (err) {
                console.error('Failed to copy link: ', err);
            }
            this.showShareMenu = false;
        },
        printArticle() {
            window.print();
        },
        toggleBookmark() {
            // Implement bookmark functionality
            console.log('Bookmark toggled for article:', this.post.id);
            // You can add localStorage or API call here
        },
        subscribeNewsletter() {
            // Implement newsletter subscription logic
            console.log('Newsletter subscription:', this.newsletterEmail);
            // You can add API call here
            alert('Thank you for subscribing!');
            this.newsletterEmail = '';
        },
        getExcerpt(text, maxLength) {
            const cleanText = text.replace(/<[^>]*>/g, '');
            return cleanText.length > maxLength ? cleanText.substring(0, maxLength) + '...' : cleanText;
        },
        generateTableOfContents() {
            // This would parse the HTML content and extract headings
            // For now, we'll return an empty array
            return [];
        },
        updateReadingProgress() {
            const article = document.querySelector('.post-details');
            if (!article) return;

            const articleTop = article.offsetTop;
            const articleHeight = article.offsetHeight;
            const windowHeight = window.innerHeight;
            const scrollTop = window.pageYOffset;

            const progress = Math.min(100, Math.max(0,
                ((scrollTop - articleTop + windowHeight) / articleHeight) * 100
            ));

            this.readingProgress = progress;
        }
    },
    mounted() {
        // Add scroll listener for reading progress
        window.addEventListener('scroll', this.updateReadingProgress);

        // Generate table of contents
        this.tableOfContents = this.generateTableOfContents();

        // Close share menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.share-menu-container')) {
                this.showShareMenu = false;
            }
        });
    },
    beforeUnmount() {
        window.removeEventListener('scroll', this.updateReadingProgress);
    },
    created() {
        this.moment = moment
        if(this.post.author){
            this.author_name = this.post.author.first_name +' '+ this.post.author.last_name;
        }
    }
}
</script>

<style scoped>
/* Custom styles for the blog post */
.prose {
    @apply text-gray-700 leading-relaxed;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    @apply text-gray-900 font-bold mt-8 mb-4;
}

.prose h1 { @apply text-3xl; }
.prose h2 { @apply text-2xl; }
.prose h3 { @apply text-xl; }
.prose h4 { @apply text-lg; }

.prose p {
    @apply mb-6;
}

.prose ul, .prose ol {
    @apply mb-6 pl-6;
}

.prose li {
    @apply mb-2;
}

.prose blockquote {
    @apply border-l-4 border-blue-500 pl-6 italic text-gray-600 my-8;
}

.prose code {
    @apply bg-gray-100 px-2 py-1 rounded text-sm;
    font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
}

.prose pre {
    @apply bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto my-8;
}

.prose img {
    @apply rounded-lg shadow-md my-8;
}

.prose a {
    @apply text-blue-600 hover:text-blue-800 underline;
}

/* Line clamp utilities */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Share menu positioning */
.share-menu-container {
    position: relative;
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }

    .prose {
        @apply text-black;
    }

    .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
        @apply text-black;
    }
}
</style>
