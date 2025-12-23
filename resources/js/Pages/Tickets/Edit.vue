<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900" role="main" aria-label="Edit Ticket">
    <Head :title="$t(title)" />

    <!-- Enhanced Header Section with Breadcrumb -->
    <div class="relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-blue-600/5 via-purple-600/5 to-indigo-600/5 dark:from-blue-400/10 dark:via-purple-400/10 dark:to-indigo-400/10"></div>

      <!-- Breadcrumb -->
      <div class="relative bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm border-b border-slate-200/30 dark:border-slate-700/30">
        <div class="px-6 py-3">
          <nav class="flex items-center space-x-2 text-sm">
            <Link :href="route('tickets')" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
              {{ $t('Tickets') }}
            </Link>
            <ChevronRight class="w-4 h-4 text-slate-400" />
            <Link :href="route('tickets.show', ticket.uid)" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
              #{{ ticket.uid }}
            </Link>
            <ChevronRight class="w-4 h-4 text-slate-400" />
            <span class="text-slate-900 dark:text-white font-medium">{{ $t('Edit') }}</span>
          </nav>
        </div>
      </div>

      <!-- Main Header -->
      <div class="relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl border-b border-slate-200/50 dark:border-slate-700/50">
        <div class="px-6 py-6">
          <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
            <!-- Left Content -->
            <div class="flex-1">
              <div class="flex items-start gap-4 mb-4">
                <div class="relative">
                  <div class="w-14 h-14 bg-gradient-to-br from-orange-500 via-orange-600 to-red-600 rounded-2xl flex items-center justify-center shadow-lg shadow-orange-500/25">
                    <Edit class="w-7 h-7 text-white" />
                  </div>
                  <!-- Auto-save indicator -->
                  <div v-if="autoSaveStatus === 'saving'" class="absolute -top-2 -right-2 w-7 h-7 bg-blue-500 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                    <Loader2 class="w-4 h-4 text-white animate-spin" />
                  </div>
                  <div v-else-if="autoSaveStatus === 'saved'" class="absolute -top-2 -right-2 w-7 h-7 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                    <Check class="w-4 h-4 text-white" />
                  </div>
                </div>
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-900 via-orange-900 to-red-900 dark:from-white dark:via-orange-100 dark:to-red-100 bg-clip-text text-transparent">
                      {{ $t('Edit Ticket') }} #{{ ticket.uid }}
                    </h1>
                    <button @click="copyTicketId" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors" :title="$t('Copy Ticket ID')">
                      <Copy class="w-4 h-4" />
                    </button>
                  </div>
                  <p class="text-slate-600 dark:text-slate-400 text-lg leading-relaxed">{{ ticket.subject }}</p>

                  <!-- Enhanced Metadata -->
                  <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-1">
                      <Calendar class="w-4 h-4" />
                      <span>{{ $t('Created') }} {{ moment(ticket.created_at).format('MMM D, YYYY') }}</span>
                    </div>
                    <div v-if="ticket.due_date" class="flex items-center gap-1" :class="ticket.is_overdue ? 'text-red-500' : 'text-slate-500'">
                      <Clock class="w-4 h-4" />
                      <span>{{ $t('Due') }} {{ moment(ticket.due_date).format('MMM D, YYYY') }}</span>
                    </div>
                    <div v-if="ticket.source" class="flex items-center gap-1">
                      <Globe class="w-4 h-4" />
                      <span class="capitalize">{{ ticket.source }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                      <User class="w-4 h-4" />
                      <span>{{ $t('Last modified') }} {{ moment(ticket.updated_at).fromNow() }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Enhanced Status and Priority Badges -->
              <div class="flex flex-wrap gap-3">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium shadow-sm" :class="getStatusClass(ticket.status)">
                  <div class="w-2 h-2 rounded-full mr-2" :class="getStatusDotClass(ticket.status)"></div>
                  {{ ticket.status?.name || 'N/A' }}
                </span>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium shadow-sm" :class="getPriorityClass(ticket.priority)">
                  <div class="w-2 h-2 rounded-full mr-2" :class="getPriorityDotClass(ticket.priority)"></div>
                  {{ ticket.priority || 'N/A' }}
                </span>
                <span v-if="ticket.impact_level" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium shadow-sm" :class="getImpactClass(ticket.impact_level)">
                  <TrendingUp class="w-4 h-4 mr-2" />
                  {{ $t('Impact') }}: {{ ticket.impact_level }}
                </span>
                <span v-if="ticket.urgency_level" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium shadow-sm" :class="getUrgencyClass(ticket.urgency_level)">
                  <Zap class="w-4 h-4 mr-2" />
                  {{ $t('Urgency') }}: {{ ticket.urgency_level }}
                </span>
                <span v-if="ticket.tags && ticket.tags.length" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                  <Tag class="w-4 h-4 mr-2" />
                  {{ ticket.tags.length }} {{ $t('tags') }}
                </span>
              </div>
            </div>

            <!-- Enhanced Right Content - Actions -->
            <div class="flex flex-col sm:flex-row gap-3">
              <div class="flex gap-2">
                <button @click="toggleFavorite" class="p-2 rounded-lg transition-colors" :class="isFavorited ? 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400' : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-400 dark:hover:bg-slate-600'">
                  <Star :class="isFavorited ? 'fill-current' : ''" class="w-4 h-4" />
                </button>
                <button @click="previewChanges" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-400 dark:hover:bg-slate-600 transition-colors">
                  <Eye class="w-4 h-4" />
                </button>
                <button @click="printTicket" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-400 dark:hover:bg-slate-600 transition-colors">
                  <Printer class="w-4 h-4" />
                </button>
              </div>
              <Link :href="route('tickets.show', ticket.uid)" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg font-medium transition-colors duration-200">
                <Eye class="w-4 h-4" />
                <span>{{ $t('View Ticket') }}</span>
              </Link>
              <button v-if="user_access.ticket.delete" @click="destroy" class="inline-flex items-center gap-2 px-4 py-2 bg-red-100 dark:bg-red-900/20 hover:bg-red-200 dark:hover:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg font-medium transition-colors duration-200">
                <Trash2 class="w-4 h-4" />
                <span>{{ $t('Delete') }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="px-6 py-6">
      <form @submit.prevent="update" class="space-y-6">
        <!-- AI Classification Suggestions -->
        <div v-if="isAdmin" class="mb-6">
          <AIClassificationSuggestions
            :ticket-id="ticket.id"
            :current-classification="{
              priority: ticket.priority_id,
              category: ticket.category_id,
              department: ticket.department_id,
              type: ticket.type_id
            }"
            @suggestion-applied="handleSuggestionApplied"
            @classification-updated="handleClassificationUpdated"
          />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Left Column - Main Form -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
              <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $t('Basic Information') }}</h2>
              </div>
              <div class="p-6 space-y-6">
                <!-- Subject -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ $t('Subject') }} <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.subject"
                    type="text"
                    :disabled="!user_access.ticket.update || ticket.closed"
                    class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:cursor-not-allowed"
                    :class="{ 'border-red-500': form.errors.subject }"
                  />
                  <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.subject }}</p>
                </div>

                <!-- Description -->
                <div>
                  <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                      {{ $t('Description') }} <span class="text-red-500">*</span>
                    </label>
                    <button
                      v-if="!enableEditor && user_access.ticket.update && !ticket.closed"
                      type="button"
                      @click="enableEditor = true"
                      class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                      <Edit class="w-4 h-4" />
                      {{ $t('Edit') }}
                    </button>
                  </div>

                  <div v-if="!enableEditor" class="prose dark:prose-invert max-w-none p-4 bg-slate-50 dark:bg-slate-700 rounded-lg" v-html="ticket.details"></div>

                  <div v-if="enableEditor">
                    <RichEditor v-model="form.details" class="min-h-[200px]" />
                    <div class="flex justify-end mt-2">
                      <button
                        type="button"
                        @click="enableEditor = false"
                        class="inline-flex items-center gap-1 text-sm text-slate-600 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300"
                      >
                        <X class="w-4 h-4" />
                        {{ $t('Cancel') }}
                      </button>
                    </div>
                  </div>

                  <p v-if="form.errors.details" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.details }}</p>
                </div>

                <!-- Resolution (if closed) -->
                <div v-if="ticket.closed">
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ $t('Resolution') }}
                  </label>
                  <RichEditor v-model="form.resolution" class="min-h-[150px]" />
                  <p v-if="form.errors.resolution" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.resolution }}</p>
                </div>
              </div>
            </div>

            <!-- Attachments Card -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
              <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $t('Attachments') }}</h2>
              </div>
              <div class="p-6">
                <!-- File Upload -->
                <div v-if="user_access.ticket.update && !ticket.closed" class="mb-6">
                  <input ref="file" type="file" accept=".xlsx,.xls,image/*,.doc,.docx,.ppt,.pptx,.txt,.pdf,.zip" class="hidden" multiple @change="fileInputChange" />
                  <button type="button" @click="fileBrowse" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 dark:bg-blue-900/20 hover:bg-blue-200 dark:hover:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-lg font-medium transition-colors duration-200">
                    <Upload class="w-4 h-4" />
                    {{ $t('Attach Files') }}
                  </button>
                </div>

                <!-- Existing Attachments -->
                <div v-if="attachments.length" class="space-y-3">
                  <div v-for="(file, index) in attachments" :key="file.id" class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                    <div class="flex items-center gap-3">
                      <File class="w-5 h-5 text-slate-400" />
                      <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ file.name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                          {{ getFileSize(file.size) }} •
                          {{ file.user ? file.user.first_name + ' ' + file.user.last_name : 'Unknown' }} •
                          {{ moment(file.created_at).format('MMM D, YYYY') }}
                        </p>
                      </div>
                    </div>
                    <div class="flex items-center gap-2">
                      <button type="button" @click="downloadAttachment(file)" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        <Download class="w-4 h-4" />
                      </button>
                      <button v-if="user_access.ticket.delete && !ticket.closed" type="button" @click="removeAttachment(file, index)" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                        <Trash2 class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                </div>

                <!-- New Files -->
                <div v-if="form.files.length" class="space-y-3 mt-4">
                  <h4 class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('New Files') }}</h4>
                  <div v-for="(file, index) in form.files" :key="index" class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <div class="flex items-center gap-3">
                      <File class="w-5 h-5 text-blue-400" />
                      <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ file.name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ getFileSize(file.size) }}</p>
                      </div>
                    </div>
                    <button type="button" @click="fileRemove(file, index)" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                      <X class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
              <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $t('Comments') }}</h2>
              </div>
              <div class="p-6">
                <!-- Existing Comments -->
                <div v-if="comments.length" class="space-y-4 mb-6">
                  <div v-for="comment in comments" :key="comment.id" class="flex gap-4">
                    <div class="flex-shrink-0">
                      <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-medium text-sm">
                          {{ comment.user ? comment.user.first_name.charAt(0) : 'U' }}
                        </span>
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <div class="flex items-center gap-2 mb-2">
                        <h4 class="text-sm font-medium text-slate-900 dark:text-white">
                          {{ comment.user ? comment.user.first_name + ' ' + comment.user.last_name : 'Unknown User' }}
                        </h4>
                        <span class="text-xs text-slate-500 dark:text-slate-400">
                          {{ moment(comment.created_at).fromNow() }}
                        </span>
                      </div>
                      <div class="prose dark:prose-invert max-w-none text-sm" v-html="comment.details"></div>
                    </div>
                  </div>
                </div>

                <!-- Add Comment -->
                <div v-if="user_access.ticket.update && !ticket.closed">
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    {{ $t('Add Comment') }}
                  </label>
                  <RichEditor v-model="form.comment" class="min-h-[100px]" />
                  <p v-if="form.errors.comment" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.comment }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Properties -->
          <div class="space-y-6">
            <!-- Ticket Properties -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
              <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Properties') }}</h2>
              </div>
              <div class="p-6 space-y-4">
                <!-- Customer -->
                <div v-if="auth.user.role.slug !== 'customer'">
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Customer') }}</label>
                  <select-edit-input
                    placeholder="Search customer"
                    :onInput="doFilter"
                    :items="customers"
                    v-model="form.user_id"
                    :error="form.errors.user_id"
                    :editable="user_access.ticket.update && !ticket.closed"
                    :value="ticket.user"
                    class="w-full"
                  />
                </div>

                <!-- Priority -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Priority') }}</label>
                  <select-edit-input
                    placeholder="Search priority"
                    :items="priorities"
                    v-model="form.priority_id"
                    :error="form.errors.priority_id"
                    :editable="user_access.ticket.update && !ticket.closed"
                    :value="ticket.priority"
                    class="w-full"
                  />
                </div>

                <!-- Status -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Status') }}</label>
                  <select-edit-input
                    placeholder="Select status"
                    :items="statuses"
                    v-model="form.status_id"
                    :error="form.errors.status_id"
                    :editable="auth.user.role.slug !== 'customer' && user_access.ticket.update && !ticket.closed"
                    :value="ticket.status?.name || 'N/A'"
                    class="w-full"
                  />
                </div>

                <!-- Assigned To -->
                <div v-if="auth.user.role.slug !== 'customer' && !(hidden_fields && hidden_fields.includes('assigned_to'))">
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Assigned to') }}</label>
                  <select-edit-input
                    placeholder="Search user"
                    :onInput="doFilterUsersExceptCustomer"
                    :items="usersExceptCustomers"
                    v-model="form.assigned_to"
                    :error="form.errors.assigned_to"
                    :editable="user_access.ticket.update && !ticket.closed"
                    :value="ticket.assigned_user || 'Not Assigned'"
                    class="w-full"
                  />
                </div>

                <!-- Type -->
                <div v-if="!(hidden_fields && hidden_fields.includes('ticket_type'))">
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Type') }}</label>
                  <select-edit-input
                    placeholder="Search type"
                    :items="types"
                    v-model="form.type_id"
                    :error="form.errors.type_id"
                    :editable="user_access.ticket.update && !ticket.closed"
                    :value="ticket.type"
                    class="w-full"
                  />
                </div>

                <!-- Department -->
                <div v-if="!(hidden_fields && hidden_fields.includes('department'))">
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Department') }}</label>
                  <select-edit-input
                    @change="getCategories"
                    placeholder="Search department"
                    :items="departments"
                    v-model="form.department_id"
                    :error="form.errors.department_id"
                    :editable="user_access.ticket.update && !ticket.closed"
                    :value="ticket.department"
                    class="w-full"
                  />
                </div>

                <!-- Category -->
                <div v-if="!(hidden_fields && hidden_fields.includes('category')) && form.department_id">
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Category') }}</label>
                  <select-edit-input
                    ref="category"
                    @change="getSubCategories"
                    placeholder="Search category"
                    :items="categories"
                    v-model="form.category_id"
                    :error="form.errors.category_id"
                    :editable="user_access.ticket.update && !ticket.closed"
                    :value="ticket.category"
                    class="w-full"
                  />
                </div>

                <!-- Sub Category -->
                <div v-if="!(hidden_fields && hidden_fields.includes('category')) && form.category_id">
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Sub Category') }}</label>
                  <select-edit-input
                    ref="sub_category"
                    placeholder="Search sub category"
                    :items="sub_categories"
                    v-model="form.sub_category_id"
                    :error="form.errors.sub_category_id"
                    :editable="user_access.ticket.update && !ticket.closed"
                    :value="ticket.sub_category"
                    class="w-full"
                  />
                </div>
              </div>
            </div>

            <!-- Enhanced Fields -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
              <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Additional Details') }}</h2>
              </div>
              <div class="p-6 space-y-4">
                <!-- Due Date -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Due Date') }}</label>
                  <input
                    v-model="form.due_date"
                    type="datetime-local"
                    :disabled="!user_access.ticket.update || ticket.closed"
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:cursor-not-allowed"
                  />
                </div>

                <!-- Impact Level -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Impact Level') }}</label>
                  <select
                    v-model="form.impact_level"
                    :disabled="!user_access.ticket.update || ticket.closed"
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:cursor-not-allowed"
                  >
                    <option value="">{{ $t('Select Impact Level') }}</option>
                    <option value="low">{{ $t('Low') }}</option>
                    <option value="medium">{{ $t('Medium') }}</option>
                    <option value="high">{{ $t('High') }}</option>
                    <option value="critical">{{ $t('Critical') }}</option>
                  </select>
                </div>

                <!-- Urgency Level -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Urgency Level') }}</label>
                  <select
                    v-model="form.urgency_level"
                    :disabled="!user_access.ticket.update || ticket.closed"
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:cursor-not-allowed"
                  >
                    <option value="">{{ $t('Select Urgency Level') }}</option>
                    <option value="low">{{ $t('Low') }}</option>
                    <option value="medium">{{ $t('Medium') }}</option>
                    <option value="high">{{ $t('High') }}</option>
                    <option value="critical">{{ $t('Critical') }}</option>
                  </select>
                </div>

                <!-- Source -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Source') }}</label>
                  <select
                    v-model="form.source"
                    :disabled="!user_access.ticket.update || ticket.closed"
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:cursor-not-allowed"
                  >
                    <option value="web">{{ $t('Web') }}</option>
                    <option value="email">{{ $t('Email') }}</option>
                    <option value="phone">{{ $t('Phone') }}</option>
                    <option value="chat">{{ $t('Chat') }}</option>
                    <option value="api">{{ $t('API') }}</option>
                  </select>
                </div>

                <!-- Tags -->
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Tags') }}</label>
                  <input
                    v-model="tagsInput"
                    type="text"
                    :placeholder="$t('Enter tags separated by commas')"
                    :disabled="!user_access.ticket.update || ticket.closed"
                    @blur="updateTags"
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:cursor-not-allowed"
                  />
                  <div v-if="form.tags && form.tags.length" class="flex flex-wrap gap-2 mt-2">
                    <span v-for="tag in form.tags" :key="tag" class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 rounded-full text-xs">
                      {{ tag }}
                      <button v-if="user_access.ticket.update && !ticket.closed" type="button" @click="removeTag(tag)" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                        <X class="w-3 h-3" />
                      </button>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Time Tracking -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
              <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Time Tracking') }}</h2>
              </div>
              <div class="p-6 space-y-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Estimated Hours') }}</label>
                  <input
                    v-model="form.estimated_hours"
                    type="number"
                    step="0.1"
                    min="0"
                    :disabled="!user_access.ticket.update || ticket.closed"
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:cursor-not-allowed"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ $t('Actual Hours') }}</label>
                  <input
                    v-model="form.actual_hours"
                    type="number"
                    step="0.1"
                    min="0"
                    :disabled="!user_access.ticket.update || ticket.closed"
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white disabled:bg-slate-100 dark:disabled:bg-slate-800 disabled:cursor-not-allowed"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end gap-4">
          <Link :href="route('tickets.show', ticket.uid)" class="px-6 py-3 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 font-medium transition-colors duration-200">
            {{ $t('Cancel') }}
          </Link>
          <loading-button :loading="form.processing" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl">
            {{ $t('Save Changes') }}
          </loading-button>
        </div>
      </form>
    </div>

    <!-- Delete Confirmation Modal -->
    <DeleteConfirmation
      :show="showDeleteModal"
      :title="deleteConfig.title"
      :message="deleteConfig.message"
      :item-name="deleteConfig.itemName"
      :item-type="deleteConfig.itemType"
      :delete-url="deleteConfig.deleteUrl"
      :delete-method="deleteConfig.deleteMethod"
      :delete-data="deleteConfig.deleteData"
      :confirm-button-text="deleteConfig.confirmButtonText"
      :cancel-button-text="deleteConfig.cancelButtonText"
      @close="hideDeleteConfirmation"
      @confirm="confirmDelete"
    />
  </div>
</template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import Layout from '@/Shared/Layout.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import SelectEditInput from '@/Shared/SelectEditInput.vue'
import RichEditor from '@/Shared/RichEditor.vue'
import AIClassificationSuggestions from '@/Components/AI/AIClassificationSuggestions.vue'
import DeleteConfirmation from '@/Shared/DeleteConfirmation.vue'
import { useDeleteConfirmation } from '@/composables/useDeleteConfirmation'
import moment from 'moment'
import axios from 'axios'
import {
  Edit,
  Eye,
  Trash2,
  Upload,
  Download,
  File,
  X,
  Copy,
  Star,
  ChevronRight,
  Loader2,
  Check,
  Printer,
  Calendar,
  Globe,
  TrendingUp,
  Zap,
  Tag,
  User,
  Clock,
  AlertTriangle,
  AlertCircle,
  Sparkles
} from 'lucide-vue-next'

export default {
  components: {
    LoadingButton,
    Link,
    Head,
    Icon,
    SelectEditInput,
    RichEditor,
    AIClassificationSuggestions,
    DeleteConfirmation,
    Edit,
    Eye,
    Trash2,
    Upload,
    Download,
    File,
    X,
    Copy,
    Star,
    ChevronRight,
    Loader2,
    Check,
    Printer,
    Calendar,
    Globe,
    TrendingUp,
    Zap,
    Tag,
    User,
    Clock,
    AlertTriangle,
    AlertCircle,
    Sparkles
  },
  layout: Layout,
  props: {
    title: String,
    ticket: Object,
    priorities: Array,
    statuses: Array,
    types: Array,
    departments: Array,
    all_categories: {required: false},
    customers: Array,
    usersExceptCustomers: Array,
    attachments: Array,
    comments: Array,
    auth: Object,
    entries: Object,
    hidden_fields: Object,
  },
  remember: false,
  data() {
    return {
      user: this.$page.props.auth.user,
      type_status: [],
      categories: this.all_categories.filter(cat => cat.department_id === this.ticket.department_id),
      sub_categories: this.all_categories.filter(cat => cat.parent_id === this.ticket.category_id),
      enableEditor: false,
      user_access: this.$page.props.auth?.user?.access || {},
      tagsInput: '',
      // Delete confirmation
      showDeleteModal: false,
      deleteConfig: {
        title: 'Are you sure?',
        message: 'This action cannot be undone.',
        itemName: 'this item',
        itemType: 'item',
        deleteUrl: '',
        deleteMethod: 'delete',
        deleteData: {},
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
      },
      form: this.$inertia.form({
        user_id: this.ticket.user_id,
        priority_id: this.ticket.priority_id,
        status_id: this.ticket.status_id,
        department_id: this.ticket.department_id,
        category_id: this.ticket.category_id ?? null,
        sub_category_id: this.ticket.sub_category_id,
        assigned_to: this.ticket.assigned_to,
        type_id: this.ticket.type_id,
        subject: this.ticket.subject,
        details: this.ticket.details,
        resolution: this.ticket.resolution || '',
        due_date: this.ticket.due_date ? moment(this.ticket.due_date).format('YYYY-MM-DDTHH:mm') : null,
        estimated_hours: this.ticket.estimated_hours || '',
        actual_hours: this.ticket.actual_hours || '',
        impact_level: this.ticket.impact_level || '',
        urgency_level: this.ticket.urgency_level || '',
        source: this.ticket.source || 'web',
        tags: this.ticket.tags || [],
        files: this.ticket.files,
        comment: '',
        removedFiles: [],
        rating: 0,
        review: '',
      }),
      autoSaveStatus: 'idle', // 'idle', 'saving', 'saved', 'error'
      isFavorited: false,
      autoSaveTimeout: null,
      hasUnsavedChanges: false,
      activeTab: 'basic'
    }
  },
  created() {

    if(this.auth && this.auth.user && this.auth.user.role && this.auth.user.role.slug === 'customer' && this.statuses.length){
      this.type_status = this.statuses.filter(status=> (status.id === this.form.status_id) || status.name.match(/Close.*/))
    }else{
      this.type_status = this.statuses
    }
    this.moment = moment;
    this.tagsInput = this.form.tags.join(', ');
  },
  computed: {
    isAdmin() {
      return this.user && this.user.role && ['admin', 'manager', 'agent'].includes(this.user.role.slug);
    }
  },
  methods: {
    // AI-related methods
    handleSuggestionApplied(data) {
      console.log('AI suggestion applied:', data);
      // Update the form with the applied suggestion
      if (data.field === 'priority') {
        this.form.priority_id = data.suggestion.id;
      } else if (data.field === 'category') {
        this.form.category_id = data.suggestion.id;
      } else if (data.field === 'department') {
        this.form.department_id = data.suggestion.id;
      } else if (data.field === 'type') {
        this.form.type_id = data.suggestion.id;
      }
    },

    handleClassificationUpdated() {
      console.log('Classification updated via AI');
      // Refresh the ticket data or show success message
      this.$inertia.reload({ only: ['ticket'] });
    },

    getCategories(){
      this.ticket.category = 'N/A';
      this.form.category_id = null;
      this.categories = this.all_categories.filter(cat=>cat.department_id === this.form.department_id)
    },
    getSubCategories(){
      this.sub_categories = this.all_categories.filter(cat=>cat.parent_id === this.form.category_id)
      this.ticket.sub_category = 'N/A';
      this.form.sub_category_id = null;
    },
    doFilter(e){
      axios.get(this.route('filter.customers', {search: e.target.value})).then((res)=>{
        this.customers.splice(0, this.customers.length, ...res.data);
      })
    },
    doFilterUsersExceptCustomer(e){
      axios.get(this.route('filter.users_except_customer', {search: e.target.value})).then((res)=>{
        this.usersExceptCustomers.splice(0, this.usersExceptCustomers.length, ...res.data);
      })
    },
    fileInputChange(e) {
      let selectedFiles = e.target.files;
      for (let i = 0; i < selectedFiles.length; i++) {
        this.form.files.push(selectedFiles[i]);
      }
    },
    fileRemove(image, index) {
      this.form.files.splice(index, 1);
    },
    fileBrowse() {
      this.$refs.file.click()
    },
    downloadAttachment(file) {
      const link = document.createElement("a");
      link.href = window.location.origin + '/files/' + file.path;
      link.download = file.name;
      link.click();
    },
    removeAttachment(file, index) {
      this.attachments.splice(index, 1);
      this.form.removedFiles.push(file.id)
    },
    getFileSize(size) {
      const i = Math.floor(Math.log(size) / Math.log(1024))
      return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'KB', 'MB', 'GB', 'TB'][i]
    },
    update() {
      // Create a copy of the form data to avoid modifying the original
      const formData = { ...this.form.data() };
      
      // Only include due_date if it has actually changed from the original
      const originalDueDate = this.ticket.due_date ? moment(this.ticket.due_date).format('YYYY-MM-DDTHH:mm') : null;
      if (formData.due_date === originalDueDate) {
        // Remove due_date from the form data if it hasn't changed
        delete formData.due_date;
      }
      
      // Create a new form instance with the filtered data
      const updateForm = this.$inertia.form(formData);
      updateForm.post(this.route('tickets.update', this.ticket.id));
      
      this.form.files = []
      this.form.comment = ''
    },
    destroy() {
      this.showDeleteConfirmation({
        title: 'Delete Ticket',
        message: 'This action cannot be undone.',
        itemName: `Ticket #${this.ticket.uid}`,
        itemType: 'ticket',
        deleteUrl: this.route('tickets.destroy', this.ticket.id),
        deleteMethod: 'delete'
      });
    },
    showDeleteConfirmation(config) {
      this.deleteConfig = {
        ...this.deleteConfig,
        ...config
      };
      this.showDeleteModal = true;
    },
    hideDeleteConfirmation() {
      this.showDeleteModal = false;
    },
    confirmDelete() {
      this.hideDeleteConfirmation();
    },
    updateTags() {
      this.form.tags = this.tagsInput.split(',').map(tag => tag.trim()).filter(tag => tag.length > 0);
    },
    removeTag(tag) {
      this.form.tags = this.form.tags.filter(t => t !== tag);
      this.tagsInput = this.form.tags.join(', ');
    },
    copyTicketId() {
      navigator.clipboard.writeText(`#${this.ticket.uid}`);
      // You could add a toast notification here
    },
    toggleFavorite() {
      this.isFavorited = !this.isFavorited;
      // You could add API call to save favorite status here
    },
    previewChanges() {
      // You could implement a preview modal here
      console.log('Preview changes:', this.form.data());
    },
    printTicket() {
      window.print();
    },
    getStatusClass(status) {
      if (!status) return 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300';

      const statusLower = status.name?.toLowerCase() || status.toLowerCase();
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
    getImpactClass(impact) {
      return {
        'critical': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300',
        'high': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300',
        'medium': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
        'low': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300'
      }[impact] || 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300';
    },
    getUrgencyClass(urgency) {
      return {
        'critical': 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300',
        'high': 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300',
        'medium': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
        'low': 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300'
      }[urgency] || 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300';
    },
    getStatusDotClass(status) {
      if (!status) return 'bg-slate-400';

      const statusLower = status.name?.toLowerCase() || status.toLowerCase();
      if (statusLower.includes('open') || statusLower.includes('new')) {
        return 'bg-blue-500';
      } else if (statusLower.includes('pending') || statusLower.includes('waiting')) {
        return 'bg-yellow-500';
      } else if (statusLower.includes('closed') || statusLower.includes('resolved')) {
        return 'bg-green-500';
      } else if (statusLower.includes('cancelled') || statusLower.includes('cancelled')) {
        return 'bg-red-500';
      }
      return 'bg-slate-400';
    },
    getPriorityDotClass(priority) {
      if (!priority) return 'bg-slate-400';

      const priorityLower = priority.toLowerCase();
      if (priorityLower.includes('high') || priorityLower.includes('urgent')) {
        return 'bg-red-500';
      } else if (priorityLower.includes('medium') || priorityLower.includes('normal')) {
        return 'bg-yellow-500';
      } else if (priorityLower.includes('low')) {
        return 'bg-green-500';
      }
      return 'bg-slate-400';
    }
  },
}
</script>
