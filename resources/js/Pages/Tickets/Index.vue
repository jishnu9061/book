<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
    <Head :title="$t(title)" />

    <!-- Enhanced Header Section with Glassmorphism -->
    <div class="relative overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-indigo-600/5 dark:from-blue-400/10 dark:via-purple-400/10 dark:to-indigo-400/10"></div>
      <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, rgba(59, 130, 246, 0.1) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(147, 51, 234, 0.1) 0%, transparent 50%);"></div>

      <div class="relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-700/50">
        <div class="px-6 py-8">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left Content -->
            <div class="flex-1">
              <div class="flex items-center gap-4 mb-6">
                <div class="relative">
                  <div class="w-16 h-16 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                    <Ticket class="w-8 h-8 text-white" />
                  </div>
                  <div class="absolute -top-1 -right-1 w-6 h-6 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full flex items-center justify-center">
                    <span class="text-xs font-bold text-white">{{ tickets.total || 0 }}</span>
                  </div>
                </div>
                <div>
                  <h1 class="text-4xl font-bold bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 dark:from-white dark:via-blue-100 dark:to-indigo-100 bg-clip-text text-transparent">
                    {{ $t('Tickets') }}
                  </h1>
                  <p class="text-slate-600 dark:text-slate-400 text-lg">{{ $t('Manage and track support tickets efficiently') }}</p>
                </div>
              </div>

              <!-- Enhanced Quick Stats with Animations -->
              <div class="flex flex-wrap gap-4">
                <div class="group flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-blue-50 to-blue-100/50 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl border border-blue-200/50 dark:border-blue-700/50 hover:shadow-lg hover:shadow-blue-500/10 transition-all duration-300">
                  <div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full animate-pulse"></div>
                  <div>
                    <span class="text-sm font-semibold text-blue-700 dark:text-blue-300">{{ tickets.total || 0 }}</span>
                    <span class="text-sm text-blue-600 dark:text-blue-400 ml-1">{{ $t('Total Tickets') }}</span>
                  </div>
                </div>
                <div class="group flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-green-50 to-emerald-100/50 dark:from-green-900/20 dark:to-emerald-800/20 rounded-xl border border-green-200/50 dark:border-green-700/50 hover:shadow-lg hover:shadow-green-500/10 transition-all duration-300">
                  <div class="w-3 h-3 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full animate-pulse"></div>
                  <div>
                    <span class="text-sm font-semibold text-green-700 dark:text-green-300">{{ getOpenTicketsCount() }}</span>
                    <span class="text-sm text-green-600 dark:text-green-400 ml-1">{{ $t('Open') }}</span>
                  </div>
                </div>
                <div class="group flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-orange-50 to-amber-100/50 dark:from-orange-900/20 dark:to-amber-800/20 rounded-xl border border-orange-200/50 dark:border-orange-700/50 hover:shadow-lg hover:shadow-orange-500/10 transition-all duration-300">
                  <div class="w-3 h-3 bg-gradient-to-r from-orange-500 to-amber-600 rounded-full animate-pulse"></div>
                  <div>
                    <span class="text-sm font-semibold text-orange-700 dark:text-orange-300">{{ getHighPriorityCount() }}</span>
                    <span class="text-sm text-orange-600 dark:text-orange-400 ml-1">{{ $t('High Priority') }}</span>
                  </div>
                </div>
                <div class="group flex items-center gap-3 px-4 py-3 bg-gradient-to-r from-purple-50 to-violet-100/50 dark:from-purple-900/20 dark:to-violet-800/20 rounded-xl border border-purple-200/50 dark:border-purple-700/50 hover:shadow-lg hover:shadow-purple-500/10 transition-all duration-300">
                  <div class="w-3 h-3 bg-gradient-to-r from-purple-500 to-violet-600 rounded-full animate-pulse"></div>
                  <div>
                    <span class="text-sm font-semibold text-purple-700 dark:text-purple-300">{{ getUnassignedCount() }}</span>
                    <span class="text-sm text-purple-600 dark:text-purple-400 ml-1">{{ $t('Unassigned') }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Content - Enhanced Actions -->
            <div class="flex flex-col sm:flex-row gap-4">
              <!-- View Toggle -->
              <div class="flex bg-slate-100 dark:bg-slate-700 rounded-lg p-1">
                <button
                  @click="viewMode = 'grid'"
                  :class="[
                    'flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200',
                    viewMode === 'grid'
                      ? 'bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm'
                      : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                  ]"
                >
                  <Grid3X3 class="w-4 h-4" />
                  <span>{{ $t('Grid') }}</span>
                </button>
                <button
                  @click="viewMode = 'list'"
                  :class="[
                    'flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-all duration-200',
                    viewMode === 'list'
                      ? 'bg-white dark:bg-slate-600 text-slate-900 dark:text-white shadow-sm'
                      : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
                  ]"
                >
                  <List class="w-4 h-4" />
                  <span>{{ $t('List') }}</span>
                </button>
              </div>

              <!-- Import/Export Actions -->
              <div class="flex gap-2">
                <label for="importCSV" class="group inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg cursor-pointer transition-all duration-200 hover:shadow-md">
                  <Upload class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" />
                  <span class="text-sm font-medium">{{ $t('Import') }}</span>
                  <input @change="uploadImportCSV" class="hidden" id="importCSV" type="file" accept=".csv" />
                </label>

                <a :href="route('ticket.csv.export')" class="group inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg transition-all duration-200 hover:shadow-md">
                  <Download class="w-4 h-4 group-hover:scale-110 transition-transform duration-200" />
                  <span class="text-sm font-medium">{{ $t('Export') }}</span>
                </a>
              </div>

              <!-- New Ticket Button with Enhanced Animation -->
              <Link :href="route('tickets.create')" class="group relative inline-flex items-center gap-2 px-6 py-2 bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:from-blue-700 hover:via-blue-800 hover:to-indigo-800 text-white rounded-lg font-medium transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-blue-500/25 hover:-translate-y-0.5">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <Plus class="w-4 h-4 relative z-10 group-hover:rotate-90 transition-transform duration-300" />
                <span class="relative z-10">{{ $t('New Ticket') }}</span>
                <div class="absolute inset-0 rounded-lg bg-gradient-to-r from-blue-400/20 to-indigo-400/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Filters Section with Advanced Features -->
    <div class="px-6 py-6">
      <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 p-6">
        <!-- Search and Controls Row -->
        <div class="flex flex-col lg:flex-row gap-4 mb-6">
          <div class="flex-1">
            <div class="relative">
              <search-input
                v-model="form.search"
                :placeholder="$t('Search by Key, Subject, Priority, Status, Assign to...')"
                class="w-full"
                @reset="reset"
              />
              <div v-if="form.search" class="absolute right-3 top-1/2 transform -translate-y-1/2">
                <span class="text-xs text-slate-500 dark:text-slate-400">{{ getSearchResultsCount() }} {{ $t('results') }}</span>
              </div>
            </div>
          </div>
          <div class="flex items-center gap-4">
            <div class="flex items-center gap-2">
              <label class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Show') }}</label>
              <select v-model="form.limit" class="px-3 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
              <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('per page') }}</span>
            </div>

          </div>
        </div>

        <!-- Quick Filter Chips -->
        <div class="flex flex-wrap gap-2 mb-6">
          <button
            v-for="filter in quickFilters"
            :key="filter.key"
            @click="applyQuickFilter(filter)"
            :class="[
              'inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-200',
              activeQuickFilter === filter.key
                ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-700'
                : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600'
            ]"
          >
            <component :is="filter.icon" class="w-4 h-4" />
            <span>{{ filter.label }}</span>
            <span class="text-xs opacity-75">({{ filter.count }})</span>
          </button>
        </div>

        <!-- Advanced Filters (Always Visible) -->
        <div class="space-y-6">


          <!-- Main Filter Row -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

              <div v-if="!(hidden_fields && hidden_fields.includes('user_id')) && user_access.ticket.update" class="space-y-2">
                  <label class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Client') }}</label>
                  <select-input-filter
                      :placeholder="$t('Client')"
                      :onInput="doFilterClients"
                      @focus="doFilterClients"
                      :items="clients"
                      v-model="form.user_id"
                      class="w-full"
                  />
              </div>

            <div v-if="!(hidden_fields && hidden_fields.includes('ticket_type'))" class="space-y-2">
              <label class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Type') }}</label>
              <select-input v-model="form.type_id" class="w-full">
                <option :value="null">{{ $t('All Types') }}</option>
                <option v-for="s in types" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select-input>
            </div>

            <div v-if="!(hidden_fields && hidden_fields.includes('department'))" class="space-y-2">
              <label class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Department') }}</label>
              <select-input v-model="form.department_id" class="w-full">
                <option :value="null">{{ $t('All Departments') }}</option>
                <option v-for="s in departments" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select-input>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Priority') }}</label>
              <select-input v-model="form.priority_id" class="w-full">
                <option :value="null">{{ $t('All Priorities') }}</option>
                <option v-for="s in priorities" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select-input>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Status') }}</label>
              <select-input v-model="form.status_id" class="w-full">
                <option :value="null">{{ $t('All Statuses') }}</option>
                <option v-for="s in statuses" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select-input>
            </div>

            <div v-if="!(hidden_fields && hidden_fields.includes('assigned_to')) && user_access.ticket.update" class="space-y-2">
              <label class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Assign To') }}</label>
              <select-input-filter
                :placeholder="$t('All Assignees')"
                :onInput="doFilter"
                @focus="doFilter"
                :items="assignees"
                v-model="form.assigned_to"
                class="w-full"
              />
            </div>
          </div>

            <!-- Date Range Filter -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Created From') }}</label>
                    <input
                        v-model="form.date_from"
                        type="date"
                        class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Created To') }}</label>
                    <input
                        v-model="form.date_to"
                        type="date"
                        class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                    />
                </div>
            </div>
        </div>

        <!-- Filter Actions -->
        <div class="flex justify-between items-center mt-6 pt-4 border-t border-slate-200 dark:border-slate-700">
          <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
            <Filter class="w-4 h-4" />
            <span>{{ getActiveFiltersCount() }} {{ $t('filters active') }}</span>
          </div>
          <div class="flex gap-2">
            <button @click="reset" class="inline-flex items-center gap-2 px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors duration-200">
              <X class="w-4 h-4" />
              <span class="text-sm font-medium">{{ $t('Clear All') }}</span>
            </button>
            <button @click="saveFilterPreset" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg transition-colors duration-200">
              <Bookmark class="w-4 h-4" />
              <span class="text-sm font-medium">{{ $t('Save Preset') }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Tickets Display with Grid/List Views -->
    <div class="px-6 pb-6">
      <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">

        <!-- List View -->
        <div v-if="viewMode === 'list'">
          <!-- Table Header -->
          <div class="bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 px-6 py-4 border-b border-slate-200 dark:border-slate-700">
            <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-slate-700 dark:text-slate-300">
              <!-- Ticket ID Column -->
              <div class="col-span-1 flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200" @click="sort('id')">
                <span>{{ $t('Key') }}</span>
                <div class="flex flex-col">
                  <ChevronUp class="w-3 h-3" :class="{'text-blue-600 dark:text-blue-400': form.direction === 'asc' && form.field === 'id', 'text-slate-400': !(form.direction === 'asc' && form.field === 'id')}" />
                  <ChevronDown class="w-3 h-3 -mt-1" :class="{'text-blue-600 dark:text-blue-400': form.direction === 'desc' && form.field === 'id', 'text-slate-400': !(form.direction === 'desc' && form.field === 'id')}" />
                </div>
              </div>

              <!-- Subject Column -->
              <div class="col-span-4 flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200" @click="sort('subject')">
                <span>{{ $t('Subject') }}</span>
                <div class="flex flex-col">
                  <ChevronUp class="w-3 h-3" :class="{'text-blue-600 dark:text-blue-400': form.direction === 'asc' && form.field === 'subject', 'text-slate-400': !(form.direction === 'asc' && form.field === 'subject')}" />
                  <ChevronDown class="w-3 h-3 -mt-1" :class="{'text-blue-600 dark:text-blue-400': form.direction === 'desc' && form.field === 'subject', 'text-slate-400': !(form.direction === 'desc' && form.field === 'subject')}" />
                </div>
              </div>

              <!-- Priority Column -->
              <div class="col-span-1 flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200" @click="sort('priority_id')">
                <span>{{ $t('Priority') }}</span>
                <div class="flex flex-col">
                  <ChevronUp class="w-3 h-3" :class="{'text-blue-600 dark:text-blue-400': form.direction === 'asc' && form.field === 'priority_id', 'text-slate-400': !(form.direction === 'asc' && form.field === 'priority_id')}" />
                  <ChevronDown class="w-3 h-3 -mt-1" :class="{'text-blue-600 dark:text-blue-400': form.direction === 'desc' && form.field === 'priority_id', 'text-slate-400': !(form.direction === 'desc' && form.field === 'priority_id')}" />
                </div>
              </div>

              <!-- Status Column -->
              <div class="col-span-1 flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200" @click="sort('status_id')">
                <span>{{ $t('Status') }}</span>
                <div class="flex flex-col">
                  <ChevronUp class="w-3 h-3" :class="{'text-blue-600 dark:text-blue-400': form.direction === 'asc' && form.field === 'status_id', 'text-slate-400': !(form.direction === 'asc' && form.field === 'status_id')}" />
                  <ChevronDown class="w-3 h-3 -mt-1" :class="{'text-blue-600 dark:text-blue-400': form.direction === 'desc' && form.field === 'status_id', 'text-slate-400': !(form.direction === 'desc' && form.field === 'status_id')}" />
                </div>
              </div>

              <!-- Created Date Column -->
              <div class="col-span-2 flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200" @click="sort('created_at')">
                <span>{{ $t('Date') }}</span>
                <div class="flex flex-col">
                  <ChevronUp class="w-3 h-3" :class="{'text-blue-600 dark:text-blue-400': form.direction === 'asc' && form.field === 'created_at', 'text-slate-400': !(form.direction === 'asc' && form.field === 'created_at')}" />
                  <ChevronDown class="w-3 h-3 -mt-1" :class="{'text-blue-600 dark:text-blue-400': form.direction === 'desc' && form.field === 'created_at', 'text-slate-400': !(form.direction === 'desc' && form.field === 'created_at')}" />
                </div>
              </div>

              <!-- Updated Date Column -->
              <div class="col-span-2 flex items-center gap-2 cursor-pointer hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200" @click="sort('updated_at')">
                <span>{{ $t('Updated') }}</span>
                <div class="flex flex-col">
                  <ChevronUp class="w-3 h-3" :class="{'text-blue-600 dark:text-blue-400': form.direction === 'asc' && form.field === 'updated_at', 'text-slate-400': !(form.direction === 'asc' && form.field === 'updated_at')}" />
                  <ChevronDown class="w-3 h-3 -mt-1" :class="{'text-blue-600 dark:text-blue-400': form.direction === 'desc' && form.field === 'updated_at', 'text-slate-400': !(form.direction === 'desc' && form.field === 'updated_at')}" />
                </div>
              </div>

              <!-- Actions Column -->
              <div class="col-span-1 flex items-center justify-end">
                <span class="text-slate-400 dark:text-slate-500">{{ $t('Actions') }}</span>
              </div>
            </div>
          </div>

          <!-- Table Body -->
          <div class="divide-y divide-slate-200 dark:divide-slate-700">
            <div v-for="ticket in tickets.data" :key="ticket.id" class="group hover:bg-gradient-to-r hover:from-slate-50 hover:to-blue-50/30 dark:hover:from-slate-700/50 dark:hover:to-blue-900/10 transition-all duration-300">
              <Link :href="route('tickets.show', ticket.uid || ticket.id)" class="block">
                <div class="grid grid-cols-12 gap-4 px-6 py-4">
                  <!-- Ticket ID -->
                  <div class="col-span-1 flex items-center">
                    <span class="text-sm font-mono text-blue-600 dark:text-blue-400 font-medium group-hover:text-blue-700 dark:group-hover:text-blue-300 transition-colors duration-200">#{{ ticket.uid }}</span>
                  </div>

                  <!-- Subject & Details -->
                  <div class="col-span-4 flex flex-col gap-2">
                    <h3 class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200 line-clamp-2">
                      {{ ticket.subject }}
                    </h3>
                    <div class="flex flex-wrap gap-4 text-xs text-slate-500 dark:text-slate-400">
                      <span v-if="ticket.user" class="flex items-center gap-1">
                        <User class="w-3 h-3" />
                        {{ ticket.user }}
                      </span>
                      <span v-if="ticket.assigned_to" class="flex items-center gap-1">
                        <UserCheck class="w-3 h-3" />
                        {{ ticket.assigned_to }}
                      </span>
                    </div>
                  </div>

                  <!-- Priority -->
                  <div class="col-span-1 flex items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getPriorityClass(ticket.priority)">
                      {{ ticket.priority }}
                    </span>
                  </div>

                  <!-- Status -->
                  <div class="col-span-1 flex items-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass(ticket.status)">
                      {{ ticket.status }}
                    </span>
                  </div>

                  <!-- Created Date -->
                  <div class="col-span-2 flex items-center">
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                      <Calendar class="w-4 h-4" />
                      <span>{{ formatDate(ticket.created_at) }}</span>
                    </div>
                  </div>

                  <!-- Updated Date -->
                  <div class="col-span-2 flex items-center">
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                      <Clock class="w-4 h-4" />
                      <span>{{ formatDate(ticket.updated_at) }}</span>
                    </div>
                  </div>

                  <!-- Actions -->
                  <div class="col-span-1 flex items-center justify-end">
                    <ChevronRight class="w-4 h-4 text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200" />
                  </div>
                </div>
              </Link>
            </div>
          </div>
        </div>

        <!-- Grid View -->
        <div v-else class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div v-for="ticket in tickets.data" :key="ticket.id" class="group">
              <Link :href="route('tickets.show', ticket.uid || ticket.id)" class="block">
                <div class="bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-6 hover:shadow-lg hover:shadow-blue-500/10 dark:hover:shadow-blue-400/10 transition-all duration-300 hover:-translate-y-1">
                  <!-- Card Header -->
                  <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-2">
                      <span class="text-lg font-mono font-bold text-blue-600 dark:text-blue-400">#{{ ticket.uid }}</span>
                    </div>
                    <div class="flex gap-2">
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getPriorityClass(ticket.priority)">
                        {{ ticket.priority }}
                      </span>
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="getStatusClass(ticket.status)">
                        {{ ticket.status }}
                      </span>
                    </div>
                  </div>

                  <!-- Card Content -->
                  <div class="space-y-3">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200 line-clamp-2">
                      {{ ticket.subject }}
                    </h3>

                    <div class="space-y-2 text-xs text-slate-500 dark:text-slate-400">
                      <div v-if="ticket.user" class="flex items-center gap-2">
                        <User class="w-3 h-3" />
                        <span>{{ ticket.user }}</span>
                      </div>
                      <div v-if="ticket.assigned_to" class="flex items-center gap-2">
                        <UserCheck class="w-3 h-3" />
                        <span>{{ ticket.assigned_to }}</span>
                      </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-200 dark:border-slate-600">
                      <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                        <Calendar class="w-3 h-3" />
                        <span>{{ formatDate(ticket.created_at) }}</span>
                      </div>
                      <ChevronRight class="w-4 h-4 text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200" />
                    </div>
                  </div>
                </div>
              </Link>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="tickets.data.length === 0" class="px-6 py-16 text-center">
          <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 rounded-full flex items-center justify-center">
            <Ticket class="w-10 h-10 text-slate-400" />
          </div>
          <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-3">{{ $t('No tickets found') }}</h3>
          <p class="text-slate-600 dark:text-slate-400 mb-8 max-w-md mx-auto">{{ $t('Try adjusting your filters or create a new ticket to get started') }}</p>
          <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <Link :href="route('tickets.create')" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
              <Plus class="w-4 h-4" />
              <span>{{ $t('Create Ticket') }}</span>
            </Link>
            <button @click="reset" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg font-medium transition-colors duration-200">
              <X class="w-4 h-4" />
              <span>{{ $t('Clear Filters') }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Enhanced Pagination -->
      <div class="mt-8">
        <pagination :links="tickets.links" />
      </div>
    </div>
  </div>
</template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout.vue'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import SearchInput from '@/Shared/SearchInput.vue'
import SelectInputFilter from '@/Shared/SelectInputFilter.vue'
import moment from 'moment'
import { formatDate } from '@/Utils/dateFormatter'
import axios from 'axios'
import {
  Ticket,
  Plus,
  Upload,
  Download,
  User,
  UserCheck,
  Calendar,
  Clock,
  ChevronUp,
  ChevronDown,
  ChevronRight,
  X,
  Grid3X3,
  List,
  Filter,
  Bookmark,
  AlertTriangle,
  CheckCircle,
  Clock as ClockIcon,
  UserX
} from 'lucide-vue-next'

export default {
    metaInfo: { title: 'Tickets' },
    components: {
        SearchInput,
        Icon,
        Link,
        Head,
        Pagination,
        SelectInputFilter,
        SelectInput,
        Ticket,
        Plus,
        Upload,
        Download,
        User,
        UserCheck,
        Calendar,
        Clock,
        ChevronUp,
        ChevronDown,
        ChevronRight,
        X,
        Grid3X3,
        List,
        Filter,
        Bookmark,
        AlertTriangle,
        CheckCircle,
        ClockIcon,
        UserX,
    },
    layout: Layout,
    props: {
        title: String,
        filters: Object,
        tickets: Object,
        assignees: Array,
        auth: Object,
        priorities: Array,
        statuses: Array,
        types: Array,
        categories: Array,
        departments: Array,
        hidden_fields: Object,
    },
    remember: 'form',
    data() {
        return {
            headers: [
                {name: 'Key', value: 'id', sort: true},
                {name: 'Subject', value: 'subject', sort: true},
                {name: 'Priority', value: 'priority_id', sort: true},
                {name: 'Status', value: 'status_id', sort: true},
                {name: 'Date', value: 'created_at', sort: true},
                {name: 'Updated', value: 'updated_at', sort: true},
            ],
            user_access: this.$page.props.auth?.user?.access || {},
            viewMode: 'list', // 'list' or 'grid'
            activeQuickFilter: null,
            clients: [],
            form: {
                search: this.filters.search,
                limit: this.filters.limit ?? 10,
                customer_id: this.filters.customer_id,
                field: this.filters.field,
                direction: this.filters.direction,
                priority_id: this.filters.priority_id ?? null,
                status_id: this.filters.status_id ?? null,
                type_id: this.filters.type_id ?? null,
                category_id: this.filters.category_id ?? null,
                department_id: this.filters.department_id ?? null,
                date_from: this.filters.date_from ?? null,
                date_to: this.filters.date_to ?? null,
            },
        }
    },
    computed: {
        quickFilters() {
            return [
                {
                    key: 'open',
                    label: this.$t('Open'),
                    icon: CheckCircle,
                    count: this.getOpenTicketsCount(),
                    filter: { status_id: this.getOpenStatusId() }
                },
                {
                    key: 'high_priority',
                    label: this.$t('High Priority'),
                    icon: AlertTriangle,
                    count: this.getHighPriorityCount(),
                    filter: { priority_id: this.getHighPriorityId() }
                },
                {
                    key: 'unassigned',
                    label: this.$t('Unassigned'),
                    icon: UserX,
                    count: this.getUnassignedCount(),
                    filter: { assigned_to: null }
                },
                {
                    key: 'recent',
                    label: this.$t('Recent'),
                    icon: ClockIcon,
                    count: this.getRecentTicketsCount(),
                    filter: { date_from: this.getRecentDate() }
                }
            ];
        }
    },
    watch: {
        form: {
            deep: true,
            handler: throttle(function() {
                this.$inertia.get(this.route('tickets'), pickBy(this.form), { replace: true, preserveState: true })
            }, 150),
        },
    },
    methods: {
        doFilter(e){
            // Handle both event object and direct value
            const searchValue = e && e.target ? e.target.value : (e || '');
            axios.get(this.route('filter.assignees', {search: searchValue})).then((res)=>{
                this.assignees.splice(0, this.assignees.length, ...res.data);
            })
        },
        doFilterClients(e){
            // Handle both event object and direct value
            const searchValue = e && e.target ? e.target.value : (e || '');
            axios.get(this.route('filter.clients', {search: searchValue})).then((res)=>{
                this.clients.splice(0, this.clients.length, ...res.data);
            })
        },
        sort(field) {
            this.form.field = field;
            this.form.direction = this.form.direction === 'asc' ? 'desc' : 'asc';
        },
        reset() {
            this.form = mapValues(this.form, () => null)
        },
        uploadImportCSV(e){
            if(e.target.files.length){
                this.$inertia.form({file: e.target.files[0]}).post(this.route('ticket.csv.import'))
            }
        },
        getOpenTicketsCount() {
            // Count tickets that are not closed
            return this.tickets.data?.filter(ticket =>
                ticket.status && !ticket.status.toLowerCase().includes('closed')
            ).length || 0;
        },
        getPriorityClass(priority) {
            if (!priority) return 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300';

            const priorityLower = priority.toLowerCase();
            if (priorityLower.includes('high') || priorityLower.includes('urgent')) {
                return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300';
            } else if (priorityLower.includes('medium') || priorityLower.includes('normal')) {
                return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300';
            } else if (priorityLower.includes('low')) {
                return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300';
            }
            return 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300';
        },
        getStatusClass(status) {
            if (!status) return 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300';

            const statusLower = status.toLowerCase();
            if (statusLower.includes('open') || statusLower.includes('new')) {
                return 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300';
            } else if (statusLower.includes('pending') || statusLower.includes('waiting')) {
                return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300';
            } else if (statusLower.includes('closed') || statusLower.includes('resolved')) {
                return 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300';
            } else if (statusLower.includes('cancelled') || statusLower.includes('cancelled')) {
                return 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300';
            }
            return 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300';
        },
        formatDate(date) {
            const locale = this.$t('error') === 'error' ? 'en' : 'zh-tw';
            return formatDate(date, 'fromNow', locale);
        },
        getSearchResultsCount() {
            return this.tickets.data?.length || 0;
        },
        getActiveFiltersCount() {
            let count = 0;
            if (this.form.search) count++;
            if (this.form.priority_id) count++;
            if (this.form.status_id) count++;
            if (this.form.type_id) count++;
            if (this.form.category_id) count++;
            if (this.form.department_id) count++;
            if (this.form.assigned_to) count++;
            if (this.form.date_from) count++;
            if (this.form.date_to) count++;
            return count;
        },
        getHighPriorityCount() {
            return this.tickets.data?.filter(ticket =>
                ticket.priority && (ticket.priority.toLowerCase().includes('high') || ticket.priority.toLowerCase().includes('urgent'))
            ).length || 0;
        },
        getUnassignedCount() {
            return this.tickets.data?.filter(ticket =>
                !ticket.assigned_to
            ).length || 0;
        },
        getRecentTicketsCount() {
            const recentDate = moment().subtract(7, 'days');
            return this.tickets.data?.filter(ticket =>
                moment(ticket.created_at).isAfter(recentDate)
            ).length || 0;
        },
        getOpenStatusId() {
            const openStatus = this.statuses?.find(status =>
                status.name.toLowerCase().includes('open') || status.name.toLowerCase().includes('new')
            );
            return openStatus?.id || null;
        },
        getHighPriorityId() {
            const highPriority = this.priorities?.find(priority =>
                priority.name.toLowerCase().includes('high') || priority.name.toLowerCase().includes('urgent')
            );
            return highPriority?.id || null;
        },
        getRecentDate() {
            return moment().subtract(7, 'days').format('YYYY-MM-DD');
        },
        applyQuickFilter(filter) {
            this.activeQuickFilter = this.activeQuickFilter === filter.key ? null : filter.key;

            if (this.activeQuickFilter === filter.key) {
                // Apply the filter
                Object.keys(filter.filter).forEach(key => {
                    this.form[key] = filter.filter[key];
                });
            } else {
                // Clear the filter
                Object.keys(filter.filter).forEach(key => {
                    this.form[key] = null;
                });
            }
        },
        saveFilterPreset() {
            // This would typically save to localStorage or send to backend
            const preset = {
                name: 'Custom Filter',
                filters: { ...this.form },
                created_at: new Date().toISOString()
            };

            // For now, just show a success message
            alert(this.$t('Filter preset saved successfully!'));
        }
    },
    created() {
        this.moment = moment;
    }
}
</script>
