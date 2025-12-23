<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Background overlay -->
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
              <MessageCircle class="w-5 h-5" />
              {{ $t('Start a Conversation') }}
            </h3>
            <button @click="closeModal" class="text-white hover:text-gray-200 transition-colors">
              <X class="w-6 h-6" />
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-6">
          <!-- Ticket Context -->
          <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-700 rounded-lg">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                <Ticket class="w-5 h-5" />
              </div>
              <div>
                <div class="text-sm font-medium text-slate-900 dark:text-white">Ticket #{{ ticket.uid }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">{{ ticket.subject }}</div>
              </div>
            </div>
          </div>

          <!-- Conversation Form -->
          <form @submit.prevent="startConversation" class="space-y-6">
            <!-- Conversation Type (Customer Only) -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('Conversation Type') }}
              </label>
              <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <div class="flex items-center gap-3">
                  <div class="w-4 h-4 border-2 border-green-500 bg-green-500 rounded-full">
                    <div class="w-2 h-2 bg-white rounded-full mx-auto mt-0.5"></div>
                  </div>
                  <div>
                    <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $t('Customer Support') }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Direct communication with support team') }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Participants Info -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('Who will be involved') }}
              </label>
              <div class="space-y-2">
                <!-- Customer (You) -->
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                      {{ getInitials(user.name) }}
                    </div>
                    <div>
                      <div class="text-sm font-medium text-slate-900 dark:text-white">{{ user.name }}</div>
                      <div class="text-xs text-slate-500 dark:text-slate-400">{{ $t('You') }}</div>
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Required') }}</span>
                    <CheckCircle class="w-4 h-4 text-green-500" />
                  </div>
                </div>

                <!-- Support Team -->
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                      <Users class="w-4 h-4" />
                    </div>
                    <div>
                      <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $t('Support Team') }}</div>
                      <div class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Available agents and specialists') }}</div>
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Automatic') }}</span>
                    <CheckCircle class="w-4 h-4 text-green-500" />
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Start Options -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('Quick Start Options') }}
              </label>
              <div class="grid grid-cols-1 gap-3">
                <button
                  v-for="template in customerTemplates"
                  :key="template.id"
                  type="button"
                  @click="applyTemplate(template)"
                  class="p-3 text-left border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                >
                  <div class="text-sm font-medium text-slate-900 dark:text-white">{{ template.title }}</div>
                  <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ template.description }}</div>
                </button>
              </div>
            </div>

            <!-- Initial Message -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('Your Message') }} <span class="text-red-500">*</span>
              </label>
              <textarea
                v-model="form.initial_message"
                :placeholder="$t('Describe your question or concern...')"
                rows="4"
                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-slate-700 dark:text-white"
                required
              ></textarea>
              <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                {{ form.initial_message.length }}/1000 {{ $t('characters') }}
              </div>
            </div>

            <!-- Help Text -->
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
              <div class="flex items-start gap-3">
                <div class="w-5 h-5 text-blue-500 mt-0.5">
                  <Info class="w-5 h-5" />
                </div>
                <div class="text-sm text-blue-800 dark:text-blue-200">
                  <div class="font-medium mb-1">{{ $t('What happens next?') }}</div>
                  <ul class="text-xs space-y-1">
                    <li>• {{ $t('Your message will be sent to our support team') }}</li>
                    <li>• {{ $t('You will receive real-time responses') }}</li>
                    <li>• {{ $t('The conversation will be linked to this ticket') }}</li>
                    <li>• {{ $t('You can continue the conversation anytime') }}</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-600">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-600 transition-colors"
              >
                {{ $t('Cancel') }}
              </button>
              <button
                type="submit"
                :disabled="!canStartConversation"
                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <div v-if="loading" class="flex items-center gap-2">
                  <Loader2 class="w-4 h-4 animate-spin" />
                  {{ $t('Starting...') }}
                </div>
                <div v-else class="flex items-center gap-2">
                  <MessageCircle class="w-4 h-4" />
                  {{ $t('Start Conversation') }}
                </div>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  MessageCircle,
  X,
  Ticket,
  CheckCircle,
  Loader2,
  Users,
  Info
} from 'lucide-vue-next'

export default {
  name: 'CustomerConversationStarter',
  components: {
    MessageCircle,
    X,
    Ticket,
    CheckCircle,
    Loader2,
    Users,
    Info
  },
  props: {
    show: {
      type: Boolean,
      default: false
    },
    ticket: {
      type: Object,
      required: true
    },
    user: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      loading: false,
      form: {
        conversation_type: 'customer', // Always customer for customer view
        initial_message: ''
      },
      customerTemplates: [
        {
          id: 'general_question',
          title: 'General Question',
          description: 'Ask a general question about your ticket',
          message: `Hi,

I have a question about ticket #${this.ticket?.uid}.

[Your question here]

Thank you for your help!`
        },
        {
          id: 'status_inquiry',
          title: 'Status Inquiry',
          description: 'Ask about the current status of your ticket',
          message: `Hi,

Could you please provide an update on the status of ticket #${this.ticket?.uid}?

I would like to know:
- Current status
- Expected resolution time
- Any additional information needed

Thank you!`
        },
        {
          id: 'additional_info',
          title: 'Provide Additional Information',
          description: 'Share additional details about your issue',
          message: `Hi,

I would like to provide additional information for ticket #${this.ticket?.uid}:

[Additional details here]

Please let me know if you need anything else.

Best regards`
        },
        {
          id: 'urgent_request',
          title: 'Urgent Request',
          description: 'Mark your request as urgent',
          message: `Hi,

I need urgent assistance with ticket #${this.ticket?.uid}.

[Describe the urgency and issue]

This is affecting my work/business and I would appreciate immediate attention.

Thank you for your prompt response.`
        }
      ]
    }
  },
  computed: {
    canStartConversation() {
      return this.form.initial_message.trim().length > 0 && !this.loading
    }
  },
  methods: {
    closeModal() {
      this.$emit('close')
      this.resetForm()
    },
    resetForm() {
      this.form = {
        conversation_type: 'customer',
        initial_message: ''
      }
      this.loading = false
    },
    getInitials(name) {
      return name ? name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().substring(0, 2) : 'U'
    },
    applyTemplate(template) {
      this.form.initial_message = template.message
    },
    async startConversation() {
      if (!this.canStartConversation) return

      this.loading = true
      try {
        // Prepare conversation data for customer
        const conversationData = {
          ticket_id: this.ticket.id,
          conversation_type: 'customer', // Always customer type for customer view
          participants: this.getParticipants(),
          initial_message: this.form.initial_message,
          context: {
            ticket_uid: this.ticket.uid,
            ticket_subject: this.ticket.subject,
            ticket_status: this.ticket.status?.name,
            initiated_by: 'customer'
          }
        }

        // Make API call to create conversation
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        if (!csrfToken) {
          throw new Error('CSRF token not found')
        }

        const response = await fetch('/conversations', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify(conversationData)
        })

        // Check content type and response status
        const contentType = response.headers.get('content-type') || ''
        const isJson = contentType.includes('application/json')

        // Check if response is OK
        if (!response.ok) {
          // Try to parse error response as JSON if available
          if (isJson) {
            const errorData = await response.json()
            throw new Error(errorData.message || errorData.error || errorData.errors || `Server error: ${response.status}`)
          } else {
            // Response is HTML (likely error page) or other non-JSON format
            throw new Error(`Server error (${response.status}): ${response.statusText}`)
          }
        }

        // Parse JSON response for successful responses
        if (!isJson) {
          throw new Error('Invalid response format: expected JSON')
        }

        const result = await response.json()

        if (result.success) {
          console.log('Customer conversation created successfully:', result)
          // Emit success event to parent component
          this.$emit('conversation-created', result.data)
          
          // Close modal after successful creation
          this.closeModal()
        } else {
          throw new Error(result.message || 'Failed to start conversation')
        }
      } catch (error) {
        console.error('Error starting customer conversation:', error)
        // Emit error event to parent component with a user-friendly message
        const errorMessage = error.message || 'An unexpected error occurred while starting the conversation'
        this.$emit('conversation-error', errorMessage)
      } finally {
        this.loading = false
      }
    },
    getParticipants() {
      const participants = [
        {
          user_id: this.user.id,
          role: 'customer'
        }
      ]

      // Add assigned agent if available
      if (this.ticket.assigned_to) {
        participants.push({
          user_id: this.ticket.assigned_to,
          role: 'agent'
        })
      }

      return participants
    }
  }
}
</script>



