<template>
  <nav class="px-3 py-4 space-y-1">
    <!-- Menu Groups -->
    <div v-for="(group, groupIndex) in menuGroups" :key="groupIndex" class="mb-6">
      <!-- Group Header -->
      <div v-if="group.title" class="px-3 mb-3">
        <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
          {{ group.title }}
        </h3>
      </div>

      <!-- Group Items -->
      <div class="space-y-1">
        <div v-for="(menu_item, m_index) in group.items" :key="m_index" class="relative">
          <!-- Main Menu Item -->
          <div class="group">
            <Link v-if="!menu_item.submenu"
                  :href="menu_item.route ? route(menu_item.route) : '#'"
                  class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative overflow-hidden"
                  :class="isUrl(menu_item.url)
                    ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-500/25'
                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white'">

              <!-- Active Background Glow -->
              <div v-if="isUrl(menu_item.url)"
                   class="absolute inset-0 bg-gradient-to-r from-primary-400 to-primary-500 opacity-20 animate-pulse"></div>

              <!-- Icon Container -->
              <div class="relative flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200"
                   :class="isUrl(menu_item.url)
                     ? 'bg-white/20 backdrop-blur-sm'
                     : 'bg-slate-100 dark:bg-slate-700 group-hover:bg-slate-200 dark:group-hover:bg-slate-600'">
                <icon :name="menu_item.icon"
                      class="w-4 h-4 transition-all duration-200"
                      :class="isUrl(menu_item.url) ? 'text-white' : 'text-slate-600 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-300'" />
              </div>

              <!-- Label -->
              <span class="flex-1 relative z-10">{{ $t(menu_item.name) }}</span>

              <!-- Active Indicator -->
              <div v-if="isUrl(menu_item.url)"
                   class="relative z-10 w-2 h-2 bg-white rounded-full shadow-sm"></div>

              <!-- Hover Effect -->
              <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
            </Link>

            <!-- Expandable Menu Item -->
            <div v-else>
              <button @click="toggleSubmenu(m_index)"
                      class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 group relative overflow-hidden"
                      :class="isUrl(menu_item.url)
                        ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-500/25'
                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 hover:text-slate-900 dark:hover:text-white'">

                <!-- Active Background Glow -->
                <div v-if="isUrl(menu_item.url)"
                     class="absolute inset-0 bg-gradient-to-r from-primary-400 to-primary-500 opacity-20 animate-pulse"></div>

                <div class="flex items-center gap-3 relative z-10">
                  <!-- Icon Container -->
                  <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200"
                       :class="isUrl(menu_item.url)
                         ? 'bg-white/20 backdrop-blur-sm'
                         : 'bg-slate-100 dark:bg-slate-700 group-hover:bg-slate-200 dark:group-hover:bg-slate-600'">
                    <icon :name="menu_item.icon"
                          class="w-4 h-4 transition-all duration-200"
                          :class="isUrl(menu_item.url) ? 'text-white' : 'text-slate-600 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-300'" />
                  </div>

                  <!-- Label -->
                  <span class="flex-1 text-left">{{ $t(menu_item.name) }}</span>
                </div>

                <!-- Chevron -->
                <ChevronDown class="w-4 h-4 transition-all duration-200 relative z-10"
                             :class="[
                               expandedMenus.includes(m_index) ? 'rotate-180' : '',
                               isUrl(menu_item.url) ? 'text-white' : 'text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-300'
                             ]" />

                <!-- Hover Effect -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </button>

              <!-- Submenu -->
              <div v-if="expandedMenus.includes(m_index)"
                   class="mt-2 ml-4 space-y-1 border-l-2 border-slate-200 dark:border-slate-700 pl-4 animate-in slide-in-from-top-2 duration-200">
                <Link v-for="(sub_menu_item, s_m_index) in menu_item.submenu"
                      :key="s_m_index"
                      :href="sub_menu_item.param ? route(sub_menu_item.route, sub_menu_item.param) : route(sub_menu_item.route)"
                      class="flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-all duration-200 group relative overflow-hidden"
                      :class="isUrl(sub_menu_item.url)
                        ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 border border-primary-200 dark:border-primary-800'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-700/50'">

                  <!-- Active Background -->
                  <div v-if="isUrl(sub_menu_item.url)"
                       class="absolute inset-0 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20"></div>

                  <!-- Submenu Icon -->
                  <div class="relative flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-md transition-all duration-200"
                       :class="isUrl(sub_menu_item.url)
                         ? 'bg-primary-200 dark:bg-primary-800'
                         : 'bg-slate-100 dark:bg-slate-700 group-hover:bg-slate-200 dark:group-hover:bg-slate-600'">
                    <icon v-if="sub_menu_item.icon"
                          :name="sub_menu_item.icon"
                          class="w-3 h-3 transition-all duration-200"
                          :class="isUrl(sub_menu_item.url)
                            ? 'text-primary-600 dark:text-primary-400'
                            : 'text-slate-500 dark:text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300'" />
                    <icon v-else
                          name="dash"
                          class="w-3 h-3 transition-all duration-200"
                          :class="isUrl(sub_menu_item.url)
                            ? 'text-primary-600 dark:text-primary-400'
                            : 'text-slate-500 dark:text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-300'" />
                  </div>

                  <!-- Submenu Label -->
                  <span class="flex-1 relative z-10">{{ $t(sub_menu_item.name) }}</span>

                  <!-- Active Indicator -->
                  <div v-if="isUrl(sub_menu_item.url)"
                       class="relative z-10 w-1.5 h-1.5 bg-primary-600 dark:bg-primary-400 rounded-full"></div>

                  <!-- Hover Effect -->
                  <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import Icon from '@/Shared/Icon.vue'
import { Link } from '@inertiajs/vue3'
import { ChevronDown } from 'lucide-vue-next'

export default {
  components: {
    Icon,
    Link,
    ChevronDown,
  },
    data(){
      return{
          user: null,
          expandedMenus: [],
          menu_items: [
              {'name': 'Dashboard', 'route': 'dashboard', 'url': 'dashboard', 'icon': 'dashboard'},
              {'name': 'Tickets', 'route': 'tickets', 'url': 'tickets', 'icon': 'ticket'},
          ],
          enable_option : {}
      }
    },
    computed: {
      menuGroups() {
        const groups = [
          {
            title: 'Main',
            items: []
          },
          {
            title: 'Content',
            items: []
          },
          {
            title: 'Management',
            items: []
          },
          {
            title: 'Configuration',
            items: []
          },
          {
            title: 'System',
            items: []
          }
        ];

        // Categorize menu items
        this.menu_items.forEach(item => {
          if (['Dashboard', 'Tickets'].includes(item.name)) {
            groups[0].items.push(item);
          } else if (['Chat', 'FAQs', 'Blog', 'Knowledge Base', 'Front Pages'].includes(item.name)) {
            groups[1].items.push(item);
          } else if (['Customers', 'Notes', 'Contacts', 'Organizations', 'Manage Users'].includes(item.name)) {
            groups[2].items.push(item);
          } else if (['Settings'].includes(item.name)) {
            groups[3].items.push(item);
          } else if (['Latest Updates'].includes(item.name)) {
            groups[4].items.push(item);
          } else {
            // Default to Management group
            groups[2].items.push(item);
          }
        });

        // Remove empty groups
        return groups.filter(group => group.items.length > 0);
      }
    },
  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1)
        currentUrl = currentUrl.replace('dashboard/','')
      if (urls[0] === '') {
        return currentUrl === ''
      }
      return urls.filter(url => currentUrl.startsWith(url)).length
    },
    toggleSubmenu(index) {
      const expandedIndex = this.expandedMenus.indexOf(index);
      if (expandedIndex > -1) {
        this.expandedMenus.splice(expandedIndex, 1);
      } else {
        this.expandedMenus.push(index);
      }
    },
    addActiveClass(e){
        e.currentTarget.classList.toggle('hover')
    }
  },
    created() {
      try {

        this.user = this.$page.props.auth?.user;

        if (!this.user) {
          // No user authenticated - clear menu items for public pages
          this.menu_items = [];
          return;
        }

        const user_access = this.user.access || {};

        let enable_option = {}
        if(this.$page.props.enable_options && this.$page.props.enable_options.value){
            try {
                let options = JSON.parse(this.$page.props.enable_options.value)
                options.forEach(option=>{
                    enable_option[option.slug] = !!option.value
                })
            } catch (e) {
                console.warn('MainMenu: Error parsing enable_options:', e);
            }
        }


        // Helper function to safely check access
        const hasAccess = (module, action = 'read') => {
          return user_access[module] && user_access[module][action];
        };

        if(enable_option.chat && (hasAccess('chat', 'read') || hasAccess('chat', 'update') || hasAccess('chat', 'create') || hasAccess('chat', 'delete'))){
            this.menu_items.push({'name': 'Chat', 'route': 'chat.index', 'url': 'chat', 'icon': 'chat'})
        }

        if(enable_option.faq && (hasAccess('faq', 'read') || hasAccess('faq', 'update') || hasAccess('faq', 'create') || hasAccess('faq', 'delete'))){
            this.menu_items.push({'name': 'FAQs', 'route': 'faqs', 'url': 'faqs', 'icon': 'faq'})
        }

        if(enable_option.blog && (hasAccess('blog', 'read') || hasAccess('blog', 'update') || hasAccess('blog', 'create') || hasAccess('blog', 'delete'))){
            this.menu_items.push({'name': 'Blog', 'route': 'posts', 'url': 'posts', 'icon': 'post'})
        }

        if(enable_option.kb && (hasAccess('knowledge_base', 'read') || hasAccess('knowledge_base', 'update') || hasAccess('knowledge_base', 'create') || hasAccess('knowledge_base', 'delete'))){
            this.menu_items.push({'name': 'Knowledge Base', 'route': 'knowledge_base', 'url': 'knowledge_base', 'icon': 'knowledge'})
        }

        if(hasAccess('customer', 'read') || hasAccess('customer', 'update') || hasAccess('customer', 'create') || hasAccess('customer', 'delete')){
            this.menu_items.push({'name': 'Customers', 'route': 'customers', 'url': 'customers', 'icon': 'all_users'})
        }

        if(enable_option.note){
            this.menu_items.push( {'name': 'Notes', 'route': 'notes', 'url': 'notes', 'icon': 'notes'} )
        }

        if(enable_option.contact && (hasAccess('contact', 'read') || hasAccess('contact', 'update') || hasAccess('contact', 'create') || hasAccess('contact', 'delete'))){
            this.menu_items.push({'name': 'Contacts', 'route': 'contacts', 'url': 'contacts', 'icon': 'contact'})
        }

        if(enable_option.organization && (hasAccess('organization', 'read') || hasAccess('organization', 'update') || hasAccess('organization', 'create') || hasAccess('organization', 'delete'))){
            this.menu_items.push({'name': 'Organizations', 'route': 'organizations', 'url': 'organizations', 'icon': 'office'})
        }

        if(hasAccess('user', 'read') || hasAccess('user', 'update') || hasAccess('user', 'create') || hasAccess('user', 'delete')){
            this.menu_items.push({'name': 'Manage Users', 'route': 'users', 'url': 'users', 'icon': 'users'})
        }

        const settingSubmenus = [];
        if(this.user.role && this.user.role.slug === 'admin'){
           settingSubmenus.push({'name': 'User Roles', 'route': 'roles', 'url': 'settings/roles', 'icon': 'user_role'})
        }

        if(hasAccess('global', 'read') || hasAccess('global', 'update') || hasAccess('global', 'create') || hasAccess('global', 'delete')){
            settingSubmenus.push({'name': 'Global', 'route': 'global', 'url': 'settings/global', 'icon': 'global_setting'})
        }

        if(hasAccess('global', 'read') || hasAccess('global', 'update') || hasAccess('global', 'create') || hasAccess('global', 'delete')){
            settingSubmenus.push({'name': 'Custom fields', 'route': 'tickets.builder', 'url': 'settings/custom-form', 'icon': 'form-builder'})
        }

        if(hasAccess('department', 'read') || hasAccess('department', 'update') || hasAccess('department', 'create') || hasAccess('department', 'delete')){
            settingSubmenus.push({'name': 'Departments', 'route': 'departments', 'url': 'settings/departments', 'icon': 'departments'})
        }

        if(hasAccess('category', 'read') || hasAccess('category', 'update') || hasAccess('category', 'create') || hasAccess('category', 'delete')){
            settingSubmenus.push({'name': 'Categories', 'route': 'categories', 'url': 'settings/categories', 'icon': 'category'})
        }

        if(hasAccess('status', 'read') || hasAccess('status', 'update') || hasAccess('status', 'create') || hasAccess('status', 'delete')){
            settingSubmenus.push({'name': 'Status', 'route': 'statuses', 'url': 'settings/statuses', 'icon': 'status'})
        }

        if(hasAccess('priority', 'read') || hasAccess('priority', 'update') || hasAccess('priority', 'create') || hasAccess('priority', 'delete')){
            settingSubmenus.push({'name': 'Priorities', 'route': 'priorities', 'url': 'settings/priorities', 'icon': 'priorities'})
        }

        if(hasAccess('type', 'read') || hasAccess('type', 'update') || hasAccess('type', 'create') || hasAccess('type', 'delete')){
            settingSubmenus.push({'name': 'Types', 'route': 'types', 'url': 'settings/types', 'icon': 'types'})
        }

        if(hasAccess('language', 'read') || hasAccess('language', 'update') || hasAccess('language', 'create') || hasAccess('language', 'delete')){
            settingSubmenus.push({'name': 'Languages', 'route': 'languages', 'url': 'settings/languages', 'icon': 'edit'})
        }

        if(hasAccess('email_template', 'read') || hasAccess('email_template', 'update') || hasAccess('email_template', 'create') || hasAccess('email_template', 'delete')){
            settingSubmenus.push({'name': 'Email Templates', 'route': 'templates', 'url': 'settings/templates', 'icon': 'email'})
        }

        if(hasAccess('smtp', 'read') || hasAccess('smtp', 'update') || hasAccess('smtp', 'create') || hasAccess('smtp', 'delete')){
            settingSubmenus.push({'name': 'SMTP Mail', 'route': 'settings.smtp', 'url': 'settings/smtp', 'icon': 'email_template'})
        }

        if(hasAccess('pusher', 'read') || hasAccess('pusher', 'update') || hasAccess('pusher', 'create') || hasAccess('pusher', 'delete')){
            settingSubmenus.push({'name': 'Pusher Chat', 'route': 'settings.pusher', 'url': 'settings/pusher', 'icon': 'chat'})
        }

        if(this.user.role && this.user.role.slug === 'admin'){
            settingSubmenus.push({'name': 'Email to ticket', 'route': 'settings.piping', 'url': 'settings/piping', 'icon': 'ticket'})
            settingSubmenus.push({'name': 'Ai Settings', 'route': 'settings.ai', 'url': 'settings/ai', 'icon': 'settings'})
            settingSubmenus.push({'name': 'License', 'route': 'license.settings', 'url': 'settings/license', 'icon': 'user_role'})
            settingSubmenus.push({'name': 'Latest Updates', 'route': 'settings.update', 'url': 'settings/update', 'icon': 'archive'})
        }

        if(settingSubmenus.length){
            this.menu_items.push({'name': 'Settings', 'route': '', 'url': 'settings', 'icon': 'settings', 'submenu': settingSubmenus })
        }

        if(hasAccess('front_page', 'read') || hasAccess('front_page', 'update') || hasAccess('front_page', 'create') || hasAccess('front_page', 'delete')){
            this.menu_items.push(
                {'name': 'Front Pages', 'route': '', 'url': 'front_pages', 'icon': 'gear',
                    'submenu': [
                        {'name': 'Home', 'route': 'front_pages.page', 'url': 'front_pages/home', 'icon': 'page', 'param': 'home'},
                        {'name': 'Contact', 'route': 'front_pages.page', 'url': 'front_pages/contact', 'icon': 'page', 'param': 'contact'},
                        {'name': 'Services', 'route': 'front_pages.page', 'url': 'front_pages/services', 'icon': 'page', 'param': 'services'},
                        {'name': 'Privacy Policy', 'route': 'front_pages.page', 'url': 'front_pages/privacy', 'icon': 'page', 'param': 'privacy'},
                        {'name': 'Terms of services', 'route': 'front_pages.page', 'url': 'front_pages/terms', 'icon': 'page', 'param': 'terms'},
                        {'name': 'Footer', 'route': 'front_pages.page', 'url': 'front_pages/footer', 'icon': 'page', 'param': 'footer'},
                    ]
                },
            )
        }



      } catch (error) {
        console.error('MainMenu: Error in created hook:', error);
        // Fallback: show basic menu items
        this.menu_items = [
          {'name': 'Dashboard', 'route': 'dashboard', 'url': 'dashboard', 'icon': 'dashboard'},
          {'name': 'Tickets', 'route': 'tickets', 'url': 'tickets', 'icon': 'ticket'},
        ];
      }
    }
}
</script>
