<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Background overlay -->
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal"></div>

      <!-- Modal panel -->
      <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white flex items-center gap-2">
              <MessageCircle class="w-5 h-5" />
              {{ $t('Start Conversation') }}
            </h3>
            <button @click="closeModal" class="text-white hover:text-gray-200 transition-colors">
              <X class="w-6 h-6" />
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="px-6 py-6">
          <!-- Ticket Context -->
          <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-700 rounded-lg border border-slate-200 dark:border-slate-600">
            <h4 class="text-sm font-medium text-slate-900 dark:text-white mb-2">{{ $t('Ticket Context') }}</h4>
            <div class="text-sm text-slate-600 dark:text-slate-400">
              <div class="flex items-center gap-2 mb-1">
                <Ticket class="w-4 h-4" />
                <span class="font-medium">#{{ ticket.uid }}</span>
                <span>{{ ticket.subject }}</span>
              </div>
              <div class="text-xs text-slate-500 dark:text-slate-500">
                {{ $t('Customer') }}: {{ ticket.user }} | {{ $t('Status') }}: {{ ticket.status?.name }}
              </div>
            </div>
          </div>

          <!-- Conversation Form -->
          <form @submit.prevent="startConversation" class="space-y-6">
            <!-- Conversation Type -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('Conversation Type') }}
              </label>
              <div class="grid grid-cols-2 gap-4">
                <label class="relative flex items-center p-4 border border-slate-200 dark:border-slate-600 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors" :class="form.conversation_type === 'internal' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : ''">
                  <input v-model="form.conversation_type" type="radio" value="internal" class="sr-only">
                  <div class="flex items-center gap-3">
                    <div class="w-4 h-4 border-2 rounded-full" :class="form.conversation_type === 'internal' ? 'border-blue-500 bg-blue-500' : 'border-slate-300'">
                      <div v-if="form.conversation_type === 'internal'" class="w-2 h-2 bg-white rounded-full mx-auto mt-0.5"></div>
                    </div>
                    <div>
                      <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $t('Internal') }}</div>
                      <div class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Team discussion only') }}</div>
                    </div>
                  </div>
                </label>
                <label class="relative flex items-center p-4 border border-slate-200 dark:border-slate-600 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors" :class="form.conversation_type === 'customer' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : ''">
                  <input v-model="form.conversation_type" type="radio" value="customer" class="sr-only">
                  <div class="flex items-center gap-3">
                    <div class="w-4 h-4 border-2 rounded-full" :class="form.conversation_type === 'customer' ? 'border-blue-500 bg-blue-500' : 'border-slate-300'">
                      <div v-if="form.conversation_type === 'customer'" class="w-2 h-2 bg-white rounded-full mx-auto mt-0.5"></div>
                    </div>
                    <div>
                      <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $t('Customer') }}</div>
                      <div class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Visible to customer') }}</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <!-- Participants -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('Participants') }}
              </label>
              <div class="space-y-2">
                <!-- Customer (always included) -->
                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                      {{ getInitials(ticket.user) }}
                    </div>
                    <div>
                      <div class="text-sm font-medium text-slate-900 dark:text-white">{{ ticket.user }}</div>
                      <div class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Customer') }}</div>
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Required') }}</span>
                    <CheckCircle class="w-4 h-4 text-green-500" />
                  </div>
                </div>

                <!-- Assigned Agent -->
                <div v-if="ticket.assigned_user" class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700 rounded-lg">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                      {{ getInitials(ticket.assigned_user) }}
                    </div>
                    <div>
                      <div class="text-sm font-medium text-slate-900 dark:text-white">{{ ticket.assigned_user }}</div>
                      <div class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Assigned Agent') }}</div>
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <input v-model="form.include_assigned_agent" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Optional') }}</span>
                  </div>
                </div>

                <!-- Enhanced Participant Selection -->
                <div class="border-t border-slate-200 dark:border-slate-600 pt-3">
                  <div class="flex items-center gap-2 mb-3">
                    <Plus class="w-4 h-4 text-slate-400" />
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $t('Add Participants') }}</span>
                  </div>
                  <ParticipantSelector
                    :users="availableUsers"
                    :selected-participants="additionalParticipants"
                    :exclude-users="excludedUserIds"
                    @add-participant="addParticipant"
                    @remove-participant="removeParticipant"
                    @clear-all="clearAdditionalParticipants"
                  />
                </div>
              </div>
            </div>

            <!-- Initial Message -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('Initial Message') }}
              </label>
              <div class="relative">
                <textarea
                  v-model="form.initial_message"
                  rows="4"
                  class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                  :placeholder="$t('Type your initial message here...')"
                ></textarea>
                <div class="absolute bottom-2 right-2 text-xs text-slate-400">
                  {{ form.initial_message.length }}/500
                </div>
              </div>
            </div>

            <!-- Quick Templates -->
            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                {{ $t('Quick Templates') }}
              </label>
              <div class="grid grid-cols-1 gap-2">
                <button
                  v-for="template in quickTemplates"
                  :key="template.id"
                  type="button"
                  @click="applyTemplate(template)"
                  class="text-left p-3 border border-slate-200 dark:border-slate-600 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors"
                >
                  <div class="text-sm font-medium text-slate-900 dark:text-white">{{ template.title }}</div>
                  <div class="text-xs text-slate-500 dark:text-slate-400">{{ template.description }}</div>
                </button>
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
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
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
  Plus,
  Loader2
} from 'lucide-vue-next'
import ParticipantSelector from './ParticipantSelector.vue'

export default {
  name: 'ConversationStarter',
  components: {
    MessageCircle,
    X,
    Ticket,
    CheckCircle,
    Plus,
    Loader2,
    ParticipantSelector
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
    availableUsers: {
      type: Array,
      default: () => []
    }
  },
  data() {
    return {
      loading: false,
      form: {
        conversation_type: 'internal',
        include_assigned_agent: true,
        additional_participants: [],
        initial_message: ''
      },
      additionalParticipants: [],
      quickTemplates: [
        {
          id: 'status_update',
          title: 'Status Update',
          description: 'Inform about ticket status changes',
          message: `Hi,

I wanted to update you on the status of ticket #${this.ticket?.uid}.

Current Status: ${this.ticket?.status?.name}

Please let me know if you have any questions.

Best regards`
        },
        {
          id: 'information_request',
          title: 'Information Request',
          description: 'Request additional information from customer',
          message: `Hi,

Thank you for contacting us regarding ticket #${this.ticket?.uid}.

To better assist you, I need some additional information:

1. [Specific question 1]
2. [Specific question 2]
3. [Specific question 3]

Please provide these details so I can resolve your issue quickly.

Best regards`
        },
        {
          id: 'resolution_update',
          title: 'Resolution Update',
          description: 'Provide resolution or next steps',
          message: `Hi,

I have an update on ticket #${this.ticket?.uid}.

[Resolution details or next steps]

Please let me know if this resolves your issue or if you need any clarification.

Best regards`
        },
        {
          id: 'escalation',
          title: 'Escalation Notice',
          description: 'Inform about ticket escalation',
          message: `Hi,

I wanted to inform you that ticket #${this.ticket?.uid} has been escalated to our senior team for review.

This is due to: [Reason for escalation]

We will provide an update within [timeframe].

Best regards`
        }
      ]
    }
  },
  computed: {
    canStartConversation() {
      return this.form.initial_message.trim().length > 0 && !this.loading
    },
    excludedUserIds() {
      const excluded = [this.ticket.user_id]
      if (this.ticket.assigned_to && this.form.include_assigned_agent) {
        excluded.push(this.ticket.assigned_to)
      }
      return excluded
    }
  },
  methods: {
    closeModal() {
      this.$emit('close')
      this.resetForm()
    },
    resetForm() {
      this.form = {
        conversation_type: 'internal',
        include_assigned_agent: true,
        additional_participants: [],
        initial_message: ''
      }
      this.additionalParticipants = []
      this.loading = false
    },
    addParticipant(participant) {
      if (!this.additionalParticipants.find(p => p.id === participant.id)) {
        this.additionalParticipants.push(participant)
      }
    },
    removeParticipant(userId) {
      this.additionalParticipants = this.additionalParticipants.filter(p => p.id !== userId)
    },
    clearAdditionalParticipants() {
      this.additionalParticipants = []
    },
    getInitials(name) {
      if (!name) return 'U'
      return name.split(' ').map(n => n.charAt(0)).join('').toUpperCase().substring(0, 2)
    },
    applyTemplate(template) {
      this.form.initial_message = template.message
    },
    async startConversation() {
      if (!this.canStartConversation) return

      this.loading = true
      try {
        // Prepare conversation data
        const conversationData = {
          ticket_id: this.ticket.id,
          conversation_type: this.form.conversation_type,
          participants: this.getParticipants(),
          initial_message: this.form.initial_message,
          context: {
            ticket_uid: this.ticket.uid,
            ticket_subject: this.ticket.subject,
            ticket_status: this.ticket.status?.name
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
        let contentType = response.headers.get('content-type') || ''
        const isJson = contentType.includes('application/json')

        // Get response text first (we'll parse it conditionally)
        const responseText = await response.text()
        let result = null

        // Log response for debugging
        console.log('Conversation creation response:', {
          status: response.status,
          statusText: response.statusText,
          contentType: contentType,
          isJson: isJson,
          responseText: responseText.substring(0, 200) // Log first 200 chars
        })

        // Try to parse as JSON if possible
        if (isJson || responseText.trim().startsWith('{')) {
          try {
            result = JSON.parse(responseText)
            console.log('Parsed JSON result:', result)
          } catch (e) {
            console.warn('Failed to parse response as JSON:', e, 'Response text:', responseText)
          }
        }

        // Check if response is OK
        if (!response.ok) {
          // Handle different error status codes
          if (result) {
            // Validation errors (422)
            if (response.status === 422 && result.errors) {
              const errorMessages = Object.values(result.errors).flat().join(', ')
              throw new Error(errorMessages || result.message || 'Validation failed')
            }
            // Other JSON errors
            const errorMsg = result.message || result.error || (result.errors ? JSON.stringify(result.errors) : '') || `Server error: ${response.status}`
            console.error('Server returned error:', result)
            throw new Error(errorMsg)
          } else {
            // Response is HTML (likely error page) or other non-JSON format
            console.error('Server returned non-JSON error:', responseText.substring(0, 500))
            throw new Error(`Server error (${response.status}): ${response.statusText || 'Unknown error'}`)
          }
        }

        // Parse JSON response for successful responses
        if (!result) {
          if (isJson) {
            try {
              result = JSON.parse(responseText)
              console.log('Parsed JSON result (retry):', result)
            } catch (e) {
              console.error('Failed to parse JSON response:', e, 'Response text:', responseText)
              throw new Error('Invalid response format: expected JSON but received: ' + responseText.substring(0, 100))
            }
          } else {
            console.error('Response is not JSON but status is OK:', {
              contentType: contentType,
              responseText: responseText.substring(0, 200)
            })
            throw new Error('Invalid response format: expected JSON, received ' + contentType)
          }
        }

        if (!result) {
          console.error('Result is null after parsing:', {
            responseText: responseText,
            isJson: isJson,
            contentType: contentType
          })
          throw new Error('Invalid response format: could not parse response')
        }

        if (result.success) {
          console.log('Conversation created successfully:', result)
          // Emit success event to parent component
          this.$emit('conversation-created', result.data)
          
          // Close modal after successful creation
          this.closeModal()
        } else {
          console.error('Server returned success=false:', result)
          const errorMsg = result.message || result.error || 'Failed to start conversation'
          throw new Error(errorMsg)
        }
      } catch (error) {
        console.error('Error starting conversation:', error)
        console.error('Error details:', {
          message: error.message,
          stack: error.stack,
          response: error.response
        })
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
          user_id: this.ticket.user_id,
          role: 'customer',
          required: true
        }
      ]

      if (this.form.include_assigned_agent && this.ticket.assigned_to) {
        participants.push({
          user_id: this.ticket.assigned_to,
          role: 'agent',
          required: false
        })
      }

      // Add additional participants
      this.additionalParticipants.forEach(participant => {
        participants.push({
          user_id: participant.id,
          role: participant.role || 'participant',
          required: false
        })
      })

      return participants
    }
  }
}
</script>

<style scoped>
/* Custom styles for the modal */
</style>
