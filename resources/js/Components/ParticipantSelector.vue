<template>
  <div class="participant-selector">
    <!-- Search Input -->
    <div class="relative mb-4">
      <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" />
      <input
        v-model="searchQuery"
        type="text"
        class="w-full pl-10 pr-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
        :placeholder="$t('Search users...')"
      />
      <button
        v-if="searchQuery"
        @click="clearSearch"
        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300"
      >
        <X class="w-4 h-4" />
      </button>
    </div>

    <!-- Filter Tabs -->
    <div class="flex space-x-1 mb-4 bg-slate-100 dark:bg-slate-700 rounded-lg p-1">
      <button
        v-for="filter in filters"
        :key="filter.key"
        @click="activeFilter = filter.key"
        class="flex-1 px-3 py-1.5 text-sm font-medium rounded-md transition-colors"
        :class="activeFilter === filter.key 
          ? 'bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm' 
          : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
      >
        <component :is="filter.icon" class="w-4 h-4 inline mr-2" />
        {{ filter.label }}
        <span v-if="getFilterCount(filter.key) > 0" class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
          {{ getFilterCount(filter.key) }}
        </span>
      </button>
    </div>

    <!-- Selected Participants -->
    <div v-if="selectedParticipants.length > 0" class="mb-4">
      <h4 class="text-sm font-medium text-slate-900 dark:text-white mb-2">{{ $t('Selected Participants') }}</h4>
      <div class="flex flex-wrap gap-2">
        <div
          v-for="participant in selectedParticipants"
          :key="participant.id"
          class="flex items-center gap-2 px-3 py-1.5 bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 rounded-lg text-sm"
        >
          <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">
            {{ getInitials(participant.name) }}
          </div>
          <span>{{ participant.name }}</span>
          <span class="text-xs opacity-75 capitalize">{{ participant.role }}</span>
          <button
            @click="removeParticipant(participant.id)"
            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200"
          >
            <X class="w-3 h-3" />
          </button>
        </div>
      </div>
    </div>

    <!-- Users List -->
    <div class="max-h-64 overflow-y-auto space-y-1">
      <div
        v-for="user in filteredUsers"
        :key="user.id"
        class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors cursor-pointer"
        :class="{ 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800': isSelected(user.id) }"
        @click="toggleParticipant(user)"
      >
        <div class="flex-shrink-0">
          <input
            :id="`user-${user.id}`"
            type="checkbox"
            :checked="isSelected(user.id)"
            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"
            @change="toggleParticipant(user)"
          />
        </div>
        <div class="w-8 h-8 bg-gradient-to-br from-slate-500 to-slate-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
          {{ getInitials(user.name) }}
        </div>
        <div class="flex-1 min-w-0">
          <div class="text-sm font-medium text-slate-900 dark:text-white truncate">
            {{ user.name }}
          </div>
          <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
            {{ user.email }}
          </div>
        </div>
        <div class="flex-shrink-0">
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="getRoleClass(user.role)">
            {{ user.role }}
          </span>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredUsers.length === 0" class="text-center py-8 text-slate-500 dark:text-slate-400">
      <Users class="w-8 h-8 mx-auto mb-2 text-slate-300 dark:text-slate-600" />
      <p class="text-sm">{{ $t('No users found') }}</p>
    </div>

    <!-- Quick Actions -->
    <div v-if="selectedParticipants.length > 0" class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-600">
      <div class="flex items-center justify-between">
        <div class="text-sm text-slate-600 dark:text-slate-400">
          {{ selectedParticipants.length }} {{ $t('participants selected') }}
        </div>
        <div class="flex gap-2">
          <button
            @click="selectAll"
            class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200"
          >
            {{ $t('Select All') }}
          </button>
          <button
            @click="clearAll"
            class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200"
          >
            {{ $t('Clear All') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  Search,
  X,
  Users,
  User,
  Shield,
  Crown
} from 'lucide-vue-next'

export default {
  name: 'ParticipantSelector',
  components: {
    Search,
    X,
    Users,
    User,
    Shield,
    Crown
  },
  props: {
    users: {
      type: Array,
      default: () => []
    },
    selectedParticipants: {
      type: Array,
      default: () => []
    },
    excludeUsers: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      searchQuery: '',
      activeFilter: 'all',
      filters: [
        { key: 'all', label: 'All', icon: Users },
        { key: 'admin', label: 'Admins', icon: Crown },
        { key: 'manager', label: 'Managers', icon: Shield },
        { key: 'agent', label: 'Agents', icon: User }
      ]
    }
  },
  computed: {
    filteredUsers() {
      let filtered = this.users.filter(user => 
        !this.excludeUsers.includes(user.id)
      )

      // Apply search filter
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        filtered = filtered.filter(user => 
          user.name.toLowerCase().includes(query) ||
          user.email.toLowerCase().includes(query)
        )
      }

      // Apply role filter
      if (this.activeFilter !== 'all') {
        filtered = filtered.filter(user => user.role === this.activeFilter)
      }

      return filtered
    }
  },
  methods: {
    toggleParticipant(user) {
      const isSelected = this.isSelected(user.id)
      
      if (isSelected) {
        this.removeParticipant(user.id)
      } else {
        this.addParticipant(user)
      }
    },
    addParticipant(user) {
      if (!this.isSelected(user.id)) {
        this.$emit('add-participant', {
          ...user,
          role: this.getDefaultRole(user.role)
        })
      }
    },
    removeParticipant(userId) {
      this.$emit('remove-participant', userId)
    },
    selectAll() {
      const usersToAdd = this.filteredUsers.filter(user => !this.isSelected(user.id))
      usersToAdd.forEach(user => this.addParticipant(user))
    },
    clearAll() {
      this.$emit('clear-all')
    },
    clearSearch() {
      this.searchQuery = ''
    },
    isSelected(userId) {
      return this.selectedParticipants.some(p => p.id === userId)
    },
    getInitials(name) {
      if (!name) return 'U'
      return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().substring(0, 2)
    },
    getRoleClass(role) {
      const classes = {
        admin: 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300',
        manager: 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300',
        agent: 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300'
      }
      return classes[role] || 'bg-slate-100 text-slate-800 dark:bg-slate-600 dark:text-slate-200'
    },
    getDefaultRole(userRole) {
      // Map user roles to participant roles
      const roleMap = {
        admin: 'participant',
        manager: 'participant',
        agent: 'agent'
      }
      return roleMap[userRole] || 'participant'
    },
    getFilterCount(filterKey) {
      if (filterKey === 'all') {
        return this.users.filter(user => !this.excludeUsers.includes(user.id)).length
      }
      return this.users.filter(user => 
        user.role === filterKey && !this.excludeUsers.includes(user.id)
      ).length
    }
  }
}
</script>

<style scoped>
.participant-selector {
  @apply relative;
}
</style>
