<template>
  <div class="fixed top-28 z-[9999] left-0 right-0 max-w-lg m-auto">
    <!-- Success Message -->
    <Transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="$page.props.flash && $page.props.flash.success && show"
           class="mb-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 shadow-sm">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <CheckCircle class="w-5 h-5 text-green-600 dark:text-green-400" />
          </div>
          <div class="ml-3 flex-1">
            <p class="text-sm font-medium text-green-800 dark:text-green-200">
              {{ $page.props.flash.success }}
            </p>
          </div>
          <div class="ml-auto pl-3">
            <button @click="show = false"
                    class="inline-flex rounded-md bg-green-50 dark:bg-green-900/20 p-1.5 text-green-500 hover:bg-green-100 dark:hover:bg-green-900/40 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50 dark:focus:ring-offset-green-900/20 transition-colors duration-200">
              <X class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Error Message -->
    <Transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="$page.props.flash && ($page.props.flash.error || Object.keys($page.props.errors).length > 0) && show"
           class="mb-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 shadow-sm">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <AlertCircle class="w-5 h-5 text-red-600 dark:text-red-400" />
          </div>
          <div class="ml-3 flex-1">
            <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">
              {{ Object.keys($page.props.errors).length > 0 ? 'Validation Error' : 'Error' }}
            </h3>
            <div class="text-sm text-red-700 dark:text-red-300">
              <p v-if="$page.props.flash.error">{{ $page.props.flash.error }}</p>
              <div v-else>
                <p v-if="Object.keys($page.props.errors).length === 1" class="mb-2">
                  There is one form error that needs to be corrected.
                </p>
                <p v-else class="mb-2">
                  There are {{ Object.keys($page.props.errors).length }} form errors that need to be corrected.
                </p>
                <ul class="list-disc list-inside space-y-1">
                  <li v-for="(error, field) in $page.props.errors" :key="field">
                    <span class="font-medium">{{ field }}:</span> {{ error }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="ml-auto pl-3">
            <button @click="show = false"
                    class="inline-flex rounded-md bg-red-50 dark:bg-red-900/20 p-1.5 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50 dark:focus:ring-offset-red-900/20 transition-colors duration-200">
              <X class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script>
import { CheckCircle, AlertCircle, X } from 'lucide-vue-next'

export default {
  components: {
    CheckCircle,
    AlertCircle,
    X,
  },
  data() {
    return {
      show: true,
    }
  },
  watch: {
    '$page.props.flash': {
      handler() {
        this.show = true
      },
      deep: true,
    },
  },
}
</script>
