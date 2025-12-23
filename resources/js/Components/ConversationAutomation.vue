<template>
  <div class="conversation-automation">
    <!-- Header -->
    <div class="px-4 py-3 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-600">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-medium text-slate-900 dark:text-white flex items-center gap-2">
          <Zap class="w-5 h-5" />
          {{ $t('Conversation Automation') }}
        </h3>
        <div class="flex items-center gap-2">
          <button @click="createRule" class="px-3 py-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
            <Plus class="w-4 h-4 mr-1" />
            {{ $t('New Rule') }}
          </button>
          <button @click="toggleView" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
            <Grid v-if="viewMode === 'list'" class="w-4 h-4" />
            <List v-else class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Automation Rules List -->
    <div class="flex-1 overflow-y-auto p-4">
      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-8">
        <Loader2 class="w-6 h-6 animate-spin text-blue-600" />
        <span class="ml-2 text-slate-600 dark:text-slate-400">{{ $t('Loading automation rules...') }}</span>
      </div>

      <!-- Empty State -->
      <div v-else-if="automationRules.length === 0" class="text-center py-8">
        <Zap class="w-12 h-12 mx-auto mb-4 text-slate-300 dark:text-slate-600" />
        <h3 class="text-lg font-medium text-slate-900 dark:text-white mb-2">{{ $t('No automation rules') }}</h3>
        <p class="text-slate-500 dark:text-slate-400 mb-4">{{ $t('Create automation rules to streamline conversations') }}</p>
        <button @click="createRule" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          {{ $t('Create First Rule') }}
        </button>
      </div>

      <!-- Rules List -->
      <div v-else :class="viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4' : 'space-y-3'">
        <div
          v-for="rule in automationRules"
          :key="rule.id"
          class="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden hover:shadow-md transition-shadow"
        >
          <!-- Rule Header -->
          <div class="p-4 border-b border-slate-200 dark:border-slate-600">
            <div class="flex items-start justify-between">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <h4 class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ rule.name }}</h4>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="getStatusClass(rule.is_active)">
                    {{ rule.is_active ? $t('Active') : $t('Inactive') }}
                  </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400">{{ rule.description }}</p>
              </div>
              <div class="flex items-center gap-1 ml-2">
                <button @click="toggleRule(rule)" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                  <Power v-if="rule.is_active" class="w-4 h-4" />
                  <PowerOff v-else class="w-4 h-4" />
                </button>
                <button @click="editRule(rule)" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors">
                  <Edit class="w-4 h-4" />
                </button>
                <button @click="deleteRule(rule)" class="p-1 text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>

          <!-- Rule Content -->
          <div class="p-4">
            <div class="space-y-3">
              <!-- Trigger -->
              <div>
                <h5 class="text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Trigger') }}</h5>
                <div class="text-sm text-slate-600 dark:text-slate-400">
                  <span class="font-medium">{{ rule.trigger_type }}</span>
                  <span v-if="rule.trigger_conditions" class="text-slate-500 dark:text-slate-500">
                    - {{ rule.trigger_conditions }}
                  </span>
                </div>
              </div>

              <!-- Action -->
              <div>
                <h5 class="text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Action') }}</h5>
                <div class="text-sm text-slate-600 dark:text-slate-400">
                  <span class="font-medium">{{ rule.action_type }}</span>
                  <span v-if="rule.action_details" class="text-slate-500 dark:text-slate-500">
                    - {{ rule.action_details }}
                  </span>
                </div>
              </div>

              <!-- Statistics -->
              <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                <span>{{ $t('Executions') }}: {{ rule.execution_count || 0 }}</span>
                <span>{{ $t('Last run') }}: {{ formatDate(rule.last_executed_at) }}</span>
              </div>
            </div>
          </div>

          <!-- Rule Actions -->
          <div class="px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-t border-slate-200 dark:border-slate-600">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <button @click="testRule(rule)" class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                  {{ $t('Test') }}
                </button>
                <button @click="viewLogs(rule)" class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                  {{ $t('Logs') }}
                </button>
              </div>
              <div class="flex items-center gap-1">
                <button @click="duplicateRule(rule)" class="text-xs text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition-colors">
                  {{ $t('Duplicate') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Rule Editor Modal -->
    <div v-if="showEditor" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-600">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-slate-900 dark:text-white">
              {{ editingRule ? $t('Edit Automation Rule') : $t('Create Automation Rule') }}
            </h3>
            <button @click="closeEditor" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
              <X class="w-5 h-5" />
            </button>
          </div>
        </div>

        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
          <form @submit.prevent="saveRule" class="space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Rule Name') }}</label>
                <input
                  v-model="ruleForm.name"
                  type="text"
                  required
                  class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                  :placeholder="$t('Enter rule name')"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Priority') }}</label>
                <select v-model="ruleForm.priority" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                  <option value="1">{{ $t('High') }}</option>
                  <option value="2">{{ $t('Medium') }}</option>
                  <option value="3">{{ $t('Low') }}</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Description') }}</label>
              <textarea
                v-model="ruleForm.description"
                rows="2"
                class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                :placeholder="$t('Enter rule description')"
              ></textarea>
            </div>

            <!-- Trigger Configuration -->
            <div class="border-t border-slate-200 dark:border-slate-600 pt-6">
              <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-4">{{ $t('Trigger Configuration') }}</h4>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Trigger Type') }}</label>
                  <select v-model="ruleForm.trigger_type" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                    <option value="message_received">{{ $t('Message Received') }}</option>
                    <option value="conversation_created">{{ $t('Conversation Created') }}</option>
                    <option value="time_based">{{ $t('Time Based') }}</option>
                    <option value="keyword_detected">{{ $t('Keyword Detected') }}</option>
                    <option value="user_joined">{{ $t('User Joined') }}</option>
                    <option value="user_left">{{ $t('User Left') }}</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Trigger Conditions') }}</label>
                  <input
                    v-model="ruleForm.trigger_conditions"
                    type="text"
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                    :placeholder="$t('Enter trigger conditions')"
                  />
                </div>
              </div>
            </div>

            <!-- Action Configuration -->
            <div class="border-t border-slate-200 dark:border-slate-600 pt-6">
              <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-4">{{ $t('Action Configuration') }}</h4>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Action Type') }}</label>
                  <select v-model="ruleForm.action_type" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white">
                    <option value="send_message">{{ $t('Send Message') }}</option>
                    <option value="assign_user">{{ $t('Assign User') }}</option>
                    <option value="change_status">{{ $t('Change Status') }}</option>
                    <option value="add_tag">{{ $t('Add Tag') }}</option>
                    <option value="notify_user">{{ $t('Notify User') }}</option>
                    <option value="escalate">{{ $t('Escalate') }}</option>
                    <option value="close_conversation">{{ $t('Close Conversation') }}</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Action Details') }}</label>
                  <input
                    v-model="ruleForm.action_details"
                    type="text"
                    class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                    :placeholder="$t('Enter action details')"
                  />
                </div>
              </div>

              <div class="mt-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ $t('Action Message Template') }}</label>
                <textarea
                  v-model="ruleForm.action_message"
                  rows="4"
                  class="w-full px-3 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:text-white"
                  :placeholder="$t('Enter message template (use {{variable}} for dynamic content)')"
                ></textarea>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                  {{ $t('Available variables: {{user_name}}, {{conversation_id}}, {{message_content}}, {{timestamp}}') }}
                </p>
              </div>
            </div>

            <!-- Rule Settings -->
            <div class="border-t border-slate-200 dark:border-slate-600 pt-6">
              <h4 class="text-lg font-medium text-slate-900 dark:text-white mb-4">{{ $t('Rule Settings') }}</h4>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="flex items-center">
                    <input v-model="ruleForm.is_active" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">{{ $t('Active') }}</span>
                  </label>
                </div>
                <div>
                  <label class="flex items-center">
                    <input v-model="ruleForm.run_once" type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">{{ $t('Run Once') }}</span>
                  </label>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-600 flex justify-end gap-3">
          <button @click="closeEditor" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
            {{ $t('Cancel') }}
          </button>
          <button @click="testRule(ruleForm)" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
            {{ $t('Test Rule') }}
          </button>
          <button @click="saveRule" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
            {{ editingRule ? $t('Update Rule') : $t('Create Rule') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import {
  Zap,
  Plus,
  Grid,
  List,
  Power,
  PowerOff,
  Edit,
  Trash2,
  X,
  Loader2
} from 'lucide-vue-next'
import moment from 'moment'

export default {
  name: 'ConversationAutomation',
  components: {
    Zap,
    Plus,
    Grid,
    List,
    Power,
    PowerOff,
    Edit,
    Trash2,
    X,
    Loader2
  },
  props: {
    ticketId: {
      type: [String, Number],
      required: true
    }
  },
  data() {
    return {
      moment: moment,
      loading: false,
      viewMode: 'grid',
      showEditor: false,
      editingRule: null,
      ruleForm: {
        name: '',
        description: '',
        priority: '2',
        trigger_type: 'message_received',
        trigger_conditions: '',
        action_type: 'send_message',
        action_details: '',
        action_message: '',
        is_active: true,
        run_once: false
      },
      automationRules: [
        {
          id: 1,
          name: 'Auto Welcome Message',
          description: 'Send welcome message to new conversation participants',
          priority: '1',
          trigger_type: 'user_joined',
          trigger_conditions: 'new_participant',
          action_type: 'send_message',
          action_details: 'welcome_message',
          action_message: 'Welcome to the conversation! How can we help you today?',
          is_active: true,
          run_once: false,
          execution_count: 15,
          last_executed_at: new Date().toISOString()
        },
        {
          id: 2,
          name: 'Escalate Long Conversations',
          description: 'Escalate conversations that have been active for more than 24 hours',
          priority: '2',
          trigger_type: 'time_based',
          trigger_conditions: '24_hours',
          action_type: 'escalate',
          action_details: 'manager_notification',
          action_message: 'This conversation has been active for 24 hours and requires escalation.',
          is_active: true,
          run_once: false,
          execution_count: 3,
          last_executed_at: new Date(Date.now() - 86400000).toISOString()
        },
        {
          id: 3,
          name: 'Auto Close Resolved',
          description: 'Automatically close conversations marked as resolved',
          priority: '3',
          trigger_type: 'keyword_detected',
          trigger_conditions: 'resolved,closed,completed',
          action_type: 'close_conversation',
          action_details: 'auto_close',
          action_message: 'This conversation has been automatically closed as it appears to be resolved.',
          is_active: false,
          run_once: false,
          execution_count: 0,
          last_executed_at: null
        }
      ]
    }
  },
  methods: {
    toggleView() {
      this.viewMode = this.viewMode === 'grid' ? 'list' : 'grid'
    },
    createRule() {
      this.editingRule = null
      this.ruleForm = {
        name: '',
        description: '',
        priority: '2',
        trigger_type: 'message_received',
        trigger_conditions: '',
        action_type: 'send_message',
        action_details: '',
        action_message: '',
        is_active: true,
        run_once: false
      }
      this.showEditor = true
    },
    editRule(rule) {
      this.editingRule = rule
      this.ruleForm = { ...rule }
      this.showEditor = true
    },
    deleteRule(rule) {
      this.showDeleteConfirmation({
        title: this.$t('Delete Automation Rule'),
        message: this.$t('This action cannot be undone.'),
        itemName: rule.name,
        itemType: 'rule',
        deleteUrl: '', // This will be handled by the confirm callback
        deleteMethod: 'custom'
      }, () => {
        const index = this.automationRules.findIndex(r => r.id === rule.id)
        if (index > -1) {
          this.automationRules.splice(index, 1)
        }
      });
    },
    toggleRule(rule) {
      rule.is_active = !rule.is_active
    },
    duplicateRule(rule) {
      const newRule = {
        ...rule,
        id: Date.now(),
        name: `${rule.name} (Copy)`,
        is_active: false,
        execution_count: 0,
        last_executed_at: null
      }
      this.automationRules.push(newRule)
    },
    testRule(rule) {
      console.log('Testing rule:', rule)
      // Implement rule testing logic
    },
    viewLogs(rule) {
      console.log('Viewing logs for rule:', rule)
      // Implement log viewing logic
    },
    saveRule() {
      if (this.editingRule) {
        // Update existing rule
        const index = this.automationRules.findIndex(r => r.id === this.editingRule.id)
        if (index > -1) {
          this.automationRules[index] = {
            ...this.ruleForm,
            id: this.editingRule.id,
            execution_count: this.editingRule.execution_count,
            last_executed_at: this.editingRule.last_executed_at
          }
        }
      } else {
        // Create new rule
        const newRule = {
          ...this.ruleForm,
          id: Date.now(),
          execution_count: 0,
          last_executed_at: null
        }
        this.automationRules.push(newRule)
      }
      
      this.closeEditor()
    },
    closeEditor() {
      this.showEditor = false
      this.editingRule = null
      this.ruleForm = {
        name: '',
        description: '',
        priority: '2',
        trigger_type: 'message_received',
        trigger_conditions: '',
        action_type: 'send_message',
        action_details: '',
        action_message: '',
        is_active: true,
        run_once: false
      }
    },
    getStatusClass(isActive) {
      return isActive
        ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300'
        : 'bg-slate-100 text-slate-800 dark:bg-slate-600 dark:text-slate-200'
    },
    formatDate(date) {
      if (!date) return this.$t('Never')
      return moment(date).format('MMM D, YYYY h:mm A')
    }
  }
}
</script>

<style scoped>
.conversation-automation {
  @apply flex flex-col h-full;
}
</style>
