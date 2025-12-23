<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/20 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900" role="main" aria-label="Ticket Details">
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
            <span class="text-slate-900 dark:text-white font-medium">#{{ ticket.uid }}</span>
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
                  <div class="w-14 h-14 bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/25">
                    <Ticket class="w-7 h-7 text-white" />
                  </div>
                  <!-- Enhanced SLA Status Indicator -->
                  <div v-if="ticket.sla_status === 'breached'" class="absolute -top-2 -right-2 w-7 h-7 bg-red-500 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                    <AlertTriangle class="w-4 h-4 text-white" />
                  </div>
                  <div v-else-if="ticket.sla_status === 'overdue'" class="absolute -top-2 -right-2 w-7 h-7 bg-orange-500 rounded-full flex items-center justify-center shadow-lg">
                    <Clock class="w-4 h-4 text-white" />
                  </div>
                  <div v-else-if="ticket.sla_status === 'warning'" class="absolute -top-2 -right-2 w-7 h-7 bg-yellow-500 rounded-full flex items-center justify-center shadow-lg">
                    <AlertCircle class="w-4 h-4 text-white" />
                  </div>
                  <div v-else-if="ticket.sla_status === 'normal'" class="absolute -top-2 -right-2 w-7 h-7 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                    <CheckCircle class="w-4 h-4 text-white" />
                  </div>
                </div>
                <div class="flex-1">
                  <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-slate-900 via-blue-900 to-indigo-900 dark:from-white dark:via-blue-100 dark:to-indigo-100 bg-clip-text text-transparent">
                      #{{ ticket.uid }}
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
                <button @click="shareTicket" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-400 dark:hover:bg-slate-600 transition-colors">
                  <Share2 class="w-4 h-4" />
                </button>
                <button @click="printTicket" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-400 dark:hover:bg-slate-600 transition-colors">
                  <Printer class="w-4 h-4" />
                </button>
              </div>

              <!-- Admin Actions -->
              <div v-if="isAdmin" class="flex gap-2">
                <button @click="startConversation" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                  <MessageCircle class="w-4 h-4" />
                  <span>{{ $t('Start Conversation') }}</span>
                </button>
              </div>

              <!-- Customer Actions -->
              <div v-if="isCustomer" class="flex gap-2">
                <button @click="startCustomerConversation" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                  <MessageCircle class="w-4 h-4" />
                  <span>{{ $t('Contact Support') }}</span>
                </button>
              </div>

              <Link
                v-if="canEditTicket"
                :href="route('tickets.edit', ticket.uid)"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
              >
                <Edit class="w-4 h-4" />
                <span>{{ $t('Edit Ticket') }}</span>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="px-6 py-6">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Ticket Details -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Enhanced Ticket Information Card -->
          <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
              <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-slate-900 dark:text-white">{{ $t('Ticket Information') }}</h2>
                <div class="flex items-center gap-2">
                  <button @click="toggleDescription" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    <Eye v-if="!showDescription" class="w-4 h-4" />
                    <EyeOff v-else class="w-4 h-4" />
                  </button>
                  <button @click="copyDescription" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                    <Copy class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
            <div class="p-6">
              <!-- Enhanced Description -->
              <div class="mb-6">
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                  <FileText class="w-5 h-5" />
                  {{ $t('Description') }}
                </h3>
                <div v-if="showDescription" class="prose dark:prose-invert max-w-none bg-slate-50 dark:bg-slate-700/50 rounded-lg p-4" v-html="ticket.details"></div>
                <div v-else class="text-slate-500 dark:text-slate-400 italic">{{ $t('Description hidden') }}</div>
              </div>

              <!-- Enhanced Resolution (if closed) -->
              <div v-if="ticket.resolution" class="mb-6">
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                  <CheckCircle class="w-5 h-5 text-green-500" />
                  {{ $t('Resolution') }}
                </h3>
                <div class="prose dark:prose-invert max-w-none bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800" v-html="ticket.resolution"></div>
              </div>

              <!-- Enhanced Attachments -->
              <div v-if="attachments.length" class="mb-6">
                <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                  <Paperclip class="w-5 h-5" />
                  {{ $t('Attachments') }} ({{ attachments.length }})
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div v-for="file in attachments" :key="file.id" class="group flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-600 transition-colors">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                      <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                          <File class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ file.name }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ getFileSize(file.size) }} • {{ moment(file.created_at).format('MMM D, YYYY') }}</p>
                        <p v-if="file.user" class="text-xs text-slate-400 dark:text-slate-500">{{ $t('by') }} {{ file.user.first_name }} {{ file.user.last_name }}</p>
                      </div>
                    </div>
                    <div class="flex items-center gap-2">
                      <button @click="downloadAttachment(file)" class="p-1 text-slate-400 hover:text-green-600 dark:hover:text-green-400 transition-colors" :title="$t('Download')">
                        <Download class="w-4 h-4" />
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Ticket Activity Log -->
          <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
            <TicketTimeline
              :comments="comments"
              :activities="activities"
              :attachments="attachments"
            />
          </div>

          <!-- Conversation Manager -->
          <div v-if="isAdmin" class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
            <ConversationManager
              :ticket-id="ticket.id"
              :conversations="displayConversations"
              :loading="conversationsLoading"
              :user="user"
              @refresh-conversations="refreshConversations"
              @start-new-conversation="startConversation"
              @join-conversation="joinConversation"
              @view-conversation-details="viewConversationDetails"
            />
          </div>

          <!-- Conversation Analytics -->
          <div v-if="isAdmin && displayConversations.length > 0" class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
            <ConversationAnalytics
              :ticket-id="ticket.id"
              :conversations="displayConversations"
            />
          </div>
        </div>

        <!-- Right Column - Ticket Properties -->
        <div class="space-y-6">
          <!-- Basic Information -->
          <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
              <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Details') }}</h2>
            </div>
            <div class="p-6 space-y-4">
              <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Customer') }}</span>
                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ ticket.user }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Assigned to') }}</span>
                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ ticket.assigned_user || 'Unassigned' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Department') }}</span>
                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ ticket.department || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Category') }}</span>
                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ ticket.category || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Type') }}</span>
                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ ticket.type || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Source') }}</span>
                <span class="text-sm font-medium text-slate-900 dark:text-white capitalize">{{ ticket.source || 'Web' }}</span>
              </div>
            </div>
          </div>

          <!-- Timeline -->
          <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
              <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Timeline') }}</h2>
            </div>
            <div class="p-6 space-y-4">
              <div class="flex items-center gap-3">
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                <div>
                  <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $t('Created') }}</p>
                  <p class="text-xs text-slate-500 dark:text-slate-400">{{ moment(ticket.created_at).format('MMM D, YYYY h:mm A') }}</p>
                </div>
              </div>
              <div v-if="ticket.due_date" class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full" :class="ticket.is_overdue ? 'bg-red-500' : 'bg-blue-500'"></div>
                <div>
                  <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $t('Due Date') }}</p>
                  <p class="text-xs text-slate-500 dark:text-slate-400">{{ moment(ticket.due_date).format('MMM D, YYYY h:mm A') }}</p>
                </div>
              </div>
              <div v-if="ticket.last_customer_response" class="flex items-center gap-3">
                <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                <div>
                  <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $t('Last Customer Response') }}</p>
                  <p class="text-xs text-slate-500 dark:text-slate-400">{{ moment(ticket.last_customer_response).format('MMM D, YYYY h:mm A') }}</p>
                </div>
              </div>
              <div v-if="ticket.last_agent_response" class="flex items-center gap-3">
                <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                <div>
                  <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $t('Last Agent Response') }}</p>
                  <p class="text-xs text-slate-500 dark:text-slate-400">{{ moment(ticket.last_agent_response).format('MMM D, YYYY h:mm A') }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Time Tracking -->
          <div v-if="ticket.estimated_hours || ticket.actual_hours" class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
              <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Time Tracking') }}</h2>
            </div>
            <div class="p-6 space-y-4">
              <div v-if="ticket.estimated_hours" class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Estimated Hours') }}</span>
                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ ticket.estimated_hours }}h</span>
              </div>
              <div v-if="ticket.actual_hours" class="flex justify-between">
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Actual Hours') }}</span>
                <span class="text-sm font-medium text-slate-900 dark:text-white">{{ ticket.actual_hours }}h</span>
              </div>
              <div v-if="ticket.estimated_hours && ticket.actual_hours" class="pt-2 border-t border-slate-200 dark:border-slate-700">
                <div class="flex justify-between">
                  <span class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Remaining') }}</span>
                  <span class="text-sm font-medium" :class="(ticket.estimated_hours - ticket.actual_hours) < 0 ? 'text-red-600 dark:text-red-400' : 'text-slate-900 dark:text-white'">
                    {{ (ticket.estimated_hours - ticket.actual_hours).toFixed(1) }}h
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Tags -->
          <div v-if="ticket.tags && ticket.tags.length" class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-lg border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-slate-100/50 dark:from-slate-700/50 dark:to-slate-800/50 border-b border-slate-200 dark:border-slate-700">
              <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Tags') }}</h2>
            </div>
            <div class="p-6">
              <div class="flex flex-wrap gap-2">
                <span v-for="tag in ticket.tags" :key="tag" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                  {{ tag }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Admin Conversation Starter Modal -->
    <ConversationStarter
      v-if="isAdmin"
      :show="showConversationStarter"
      :ticket="ticket"
      :available-users="availableUsers"
      @close="showConversationStarter = false"
      @conversation-created="handleConversationCreated"
      @conversation-error="handleConversationError"
    />

    <!-- Customer Conversation Starter Modal -->
    <CustomerConversationStarter
      v-if="isCustomer"
      :show="showCustomerConversationStarter"
      :ticket="ticket"
      :user="user"
      @close="showCustomerConversationStarter = false"
      @conversation-created="handleConversationCreated"
      @conversation-error="handleConversationError"
    />

    <!-- Notification Manager -->
    <NotificationManager ref="notificationManager" />
  </div>
</template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import TicketTimeline from '@/Components/TicketTimeline.vue'
import ConversationStarter from '@/Components/ConversationStarter.vue'
import CustomerConversationStarter from '@/Components/CustomerConversationStarter.vue'
import ConversationManager from '@/Components/ConversationManager.vue'
import ConversationAnalytics from '@/Components/ConversationAnalytics.vue'
import NotificationManager from '@/Components/NotificationManager.vue'
import moment from 'moment'
import {
  Ticket,
  Edit,
  Printer,
  Download,
  File,
  MessageCircle,
  AlertTriangle,
  Clock,
  AlertCircle,
  CheckCircle,
  Copy,
  Star,
  Share2,
  ChevronRight,
  Calendar,
  Globe,
  TrendingUp,
  Zap,
  Tag,
  Eye,
  EyeOff,
  FileText,
  Paperclip
} from 'lucide-vue-next'

export default {
  components: {
    Link,
    Head,
    TicketTimeline,
    ConversationStarter,
    CustomerConversationStarter,
    ConversationManager,
    ConversationAnalytics,
    NotificationManager,
    Ticket,
    Edit,
    Printer,
    Download,
    File,
    MessageCircle,
    AlertTriangle,
    Clock,
    AlertCircle,
    CheckCircle,
    Copy,
    Star,
    Share2,
    ChevronRight,
    Calendar,
    Globe,
    TrendingUp,
    Zap,
    Tag, Eye,
    EyeOff,
    FileText,
    Paperclip
  },
  layout: Layout,
  props: {
    title: String,
    ticket: Object,
    attachments: Array,
    comments: Array,
    entries: Array,
    activities: Array,
    availableUsers: {
      type: Array,
      default: () => []
    },
    user: {
      type: Object,
      default: () => ({})
    },
    conversations: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      moment: moment,
      showDescription: true,
      isFavorited: false,
      showConversationStarter: false,
      showCustomerConversationStarter: false,
      conversationsLoading: false,
      conversationPollingInterval: null,
      localConversations: []
    }
  },
  computed: {
    isAdmin() {
      return this.user?.role?.slug === 'admin' || this.user?.role?.slug === 'manager' || this.user?.role?.slug === 'agent'
    },
    isCustomer() {
      return this.user?.role?.slug === 'customer'
    },
    displayConversations() {
      return this.localConversations.length > 0 ? this.localConversations : this.conversations
    },
    canEditTicket() {
      // Parse the role access JSON string
      let roleAccess = null;
      try {
        roleAccess = this.user?.role?.access ? JSON.parse(this.user.role.access) : null;
      } catch (e) {
        return false;
      }

      // Check if user has ticket update permission
      if (!roleAccess?.ticket?.update) {
        return false
      }

      // Additional check: Customers can only edit their own tickets
      if (this.user?.role?.slug === 'customer' && this.ticket?.user_id !== this.user?.id) {
        return false
      }

      return true
    }
  },
  mounted() {
    // Initialize local conversations with prop data
    this.localConversations = [...this.conversations]
    this.setupConversationUpdates()
  },
  beforeUnmount() {
    this.cleanupConversationUpdates()
  },
  methods: {
    getFileSize(size) {
      const i = Math.floor(Math.log(size) / Math.log(1024))
      return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'KB', 'MB', 'GB', 'TB'][i]
    },
    downloadAttachment(file) {
      const link = document.createElement("a");
      link.href = window.location.origin + '/files/' + file.path;
      link.download = file.name;
      link.click();
    },
    printTicket() {
      window.print();
    },
    copyTicketId() {
      navigator.clipboard.writeText(`#${this.ticket.uid}`);
      // You could add a toast notification here
    },
    copyDescription() {
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = this.ticket.details;
      navigator.clipboard.writeText(tempDiv.textContent || tempDiv.innerText || '');
      // You could add a toast notification here
    },
    toggleDescription() {
      this.showDescription = !this.showDescription;
    },
    toggleFavorite() {
      this.isFavorited = !this.isFavorited;
      // You could add API call to save favorite status here
    },
    shareTicket() {
      if (navigator.share) {
        navigator.share({
          title: `Ticket #${this.ticket.uid}`,
          text: this.ticket.subject,
          url: window.location.href
        });
      } else {
        // Fallback to copying URL
        navigator.clipboard.writeText(window.location.href);
        // You could add a toast notification here
      }
    },
    startConversation() {
      this.showConversationStarter = true;
    },
    startCustomerConversation() {
      this.showCustomerConversationStarter = true;
    },
    handleConversationCreated(responseData) {
      console.log('Conversation created response:', responseData);
      console.log('Current conversations before update:', this.localConversations.length);

      // The backend returns { conversation: {...}, initial_message: {...} }
      const conversation = responseData.conversation;
      const initialMessage = responseData.initial_message;

      if (conversation && conversation.id) {
        // Create a conversation object that matches the expected format
        const newConversation = {
          id: conversation.id,
          type: conversation.type || 'internal',
          title: `Conversation #${conversation.id}`,
          created_at: conversation.created_at || new Date().toISOString(),
          updated_at: conversation.updated_at || new Date().toISOString(),
          ticket_id: conversation.ticket_id,
          participants: conversation.participants || [],
          messages: initialMessage ? [initialMessage] : [],
          ticket: {
            id: this.ticket.id,
            uid: this.ticket.uid,
            subject: this.ticket.subject
          }
        }

        // Add to the beginning of the conversations array
        this.localConversations.unshift(newConversation)
        console.log('Conversation added to list. New count:', this.localConversations.length);

        // Add a temporary highlight class for visual feedback
        this.$nextTick(() => {
          const conversationElement = document.querySelector(`[data-conversation-id="${newConversation.id}"]`)
          if (conversationElement) {
            conversationElement.classList.add('animate-pulse', 'bg-green-50', 'dark:bg-green-900/20')
            setTimeout(() => {
              conversationElement.classList.remove('animate-pulse', 'bg-green-50', 'dark:bg-green-900/20')
            }, 3000)
          }
        })

        // Show success notification
        if (this.$refs.notificationManager) {
          this.$refs.notificationManager.success(
            'Conversation Started',
            'New conversation has been created successfully!',
            { duration: 3000 }
          )
        }

        console.log('Conversation added to list:', newConversation);
      } else {
        console.warn('Invalid conversation data received:', responseData);
      }
    },
    handleConversationError(errorMessage) {
        console.log(errorMessage);
      console.error('Conversation creation error:', errorMessage);

      // Show error notification
      if (this.$refs.notificationManager) {
        this.$refs.notificationManager.error(
          'Error',
          'Failed to start conversation: ' + errorMessage,
          { duration: 5000 }
        )
      }
    },
    async refreshConversations() {
      this.conversationsLoading = true
      try {
        // Fetch updated conversations from the server
        const response = await fetch(`/tickets/${this.ticket.id}/conversations`, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })

        if (response.ok) {
          const result = await response.json()
          if (result.success && result.data) {
            this.localConversations = result.data
            console.log('Conversations refreshed from server:', result.data.length)
          }
        } else {
          console.warn('Failed to refresh conversations:', response.status)
        }
      } catch (error) {
        console.error('Error refreshing conversations:', error)
      } finally {
        this.conversationsLoading = false
      }
    },
    joinConversation(conversation) {
      // Navigate to the conversation view page
      this.$inertia.visit(route('conversations.view', conversation.id))
    },
    viewConversationDetails(conversation) {
      // Show conversation details in a modal
      this.$inertia.visit(route('conversations.view', conversation.id))
    },
    setupConversationUpdates() {
      // Set up polling to check for new conversations every 30 seconds
      this.conversationPollingInterval = setInterval(() => {
        this.checkForNewConversations()
      }, 30000) // 30 seconds

      // Also listen for conversation events if WebSocket is available
      if (window.Echo) {
        this.setupWebSocketListeners()
      }
    },
    cleanupConversationUpdates() {
      // Clear polling interval
      if (this.conversationPollingInterval) {
        clearInterval(this.conversationPollingInterval)
        this.conversationPollingInterval = null
      }

      // Clean up WebSocket listeners
      if (window.Echo) {
        window.Echo.leave(`ticket.${this.ticket.id}`)
      }
    },
    async checkForNewConversations() {
      try {
        // Only check if we're not already loading
        if (this.conversationsLoading) return

        const response = await fetch(`/tickets/${this.ticket.id}/conversations`, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })

        if (response.ok) {
          const result = await response.json()
          if (result.success && result.data) {
            // Check if we have new conversations
            const currentIds = this.localConversations.map(c => c.id).sort()
            const newIds = result.data.map(c => c.id).sort()

            if (JSON.stringify(currentIds) !== JSON.stringify(newIds)) {
              this.localConversations = result.data
              console.log('Conversations updated via polling')
            }
          }
        }
      } catch (error) {
        console.warn('Error checking for new conversations:', error)
      }
    },
    setupWebSocketListeners() {
      // Listen for conversation events on this ticket
      window.Echo.private(`ticket.${this.ticket.id}`)
        .listen('ConversationCreated', (e) => {
          console.log('New conversation created via WebSocket:', e)
          this.handleConversationCreated(e.conversation)
        })
        .listen('ConversationUpdated', (e) => {
          console.log('Conversation updated via WebSocket:', e)
          this.refreshConversations()
        })
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
  }
}
</script>

<style scoped>
@media print {
  .no-print {
    display: none !important;
  }
}
</style>
