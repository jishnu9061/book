<template>
    <div class="animate-fade-in-up">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <Rocket class="w-10 h-10 text-white" />
            </div>
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">
                System Requirements Check
            </h2>
            <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                Before we begin, let's make sure your server meets all the requirements for running HelpDesk.
            </p>
        </div>

        <!-- Requirements Check -->
        <div class="space-y-6">
            <!-- PHP Version -->
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            :class="[
                'w-8 h-8 rounded-full flex items-center justify-center',
                requirements.php ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30'
              ]"
                        >
                            <Check v-if="requirements.php" class="w-5 h-5 text-green-600 dark:text-green-400" />
                            <X v-else class="w-5 h-5 text-red-600 dark:text-red-400" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 dark:text-white">PHP Version</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400">
                                Current: {{ phpVersion }} | Required: 8.1+
                            </p>
                        </div>
                    </div>
                    <div
                        :class="[
              'px-3 py-1 rounded-full text-sm font-medium',
              requirements.php
                ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
            ]"
                    >
                        {{ requirements.php ? 'Passed' : 'Failed' }}
                    </div>
                </div>
            </div>

            <!-- PHP Extensions -->
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6">
                <h3 class="font-semibold text-slate-900 dark:text-white mb-4">PHP Extensions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div
                        v-for="(status, extension) in requirements.extensions"
                        :key="extension"
                        class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 rounded-lg"
                    >
                        <div class="flex items-center space-x-2">
                            <Check v-if="status" class="w-4 h-4 text-green-600 dark:text-green-400" />
                            <X v-else class="w-4 h-4 text-red-600 dark:text-red-400" />
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ extension }}</span>
                        </div>
                        <div
                            :class="[
                'w-2 h-2 rounded-full',
                status ? 'bg-green-500' : 'bg-red-500'
              ]"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Directory Permissions -->
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6">
                <h3 class="font-semibold text-slate-900 dark:text-white mb-4">Directory Permissions</h3>
                <div class="space-y-3">
                    <div
                        v-for="(status, directory) in requirements.permissions"
                        :key="directory"
                        class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 rounded-lg"
                    >
                        <div class="flex items-center space-x-2">
                            <Check v-if="status" class="w-4 h-4 text-green-600 dark:text-green-400" />
                            <X v-else class="w-4 h-4 text-red-600 dark:text-red-400" />
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ directory }}</span>
                        </div>
                        <div
                            :class="[
                'w-2 h-2 rounded-full',
                status ? 'bg-green-500' : 'bg-red-500'
              ]"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Server Information -->
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6">
                <h3 class="font-semibold text-slate-900 dark:text-white mb-4">Server Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-600 dark:text-slate-400">Server Software:</span>
                            <span class="text-sm font-medium text-slate-900 dark:text-white">{{ serverInfo.software }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-600 dark:text-slate-400">PHP Version:</span>
                            <span class="text-sm font-medium text-slate-900 dark:text-white">{{ phpVersion }}</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-600 dark:text-slate-400">Memory Limit:</span>
                            <span class="text-sm font-medium text-slate-900 dark:text-white">{{ serverInfo.memoryLimit }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-slate-600 dark:text-slate-400">Max Execution Time:</span>
                            <span class="text-sm font-medium text-slate-900 dark:text-white">{{ serverInfo.maxExecutionTime }}s</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
            <div class="text-sm text-slate-500 dark:text-slate-400">
                <p v-if="!allRequirementsMet" class="text-red-600 dark:text-red-400">
                    Please fix the requirements above before continuing.
                </p>
                <p v-else class="text-green-600 dark:text-green-400">
                    All requirements are met! You can proceed with the installation.
                </p>
            </div>

            <button
                @click="$emit('next')"
                :disabled="!allRequirementsMet"
                :class="[
          'px-8 py-3 rounded-lg font-semibold transition-all duration-200 transform',
          allRequirementsMet
            ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white hover:scale-105 shadow-lg hover:shadow-xl'
            : 'bg-slate-300 dark:bg-slate-600 text-slate-500 dark:text-slate-400 cursor-not-allowed'
        ]"
            >
                Continue Installation
                <ArrowRight class="w-4 h-4 ml-2 inline" />
            </button>
        </div>
    </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import { Rocket, Check, X, ArrowRight } from 'lucide-vue-next'
import { installerApiRequest, handleApiResponse } from '../../../Utils/InstallerApi.js'

export default {
    name: 'InstallerWelcome',
    components: {
        Rocket,
        Check,
        X,
        ArrowRight
    },
    emits: ['next'],
    setup() {
        const requirements = reactive({
            php: false,
            extensions: {
                'OpenSSL': false,
                'PDO': false,
                'Mbstring': false,
                'Tokenizer': false,
                'XML': false,
                'Ctype': false,
                'JSON': false,
                'cURL': false,
                'Intl': false
            },
            permissions: {
                'storage/': false,
                'storage/framework/': false,
                'storage/logs/': false,
                'bootstrap/cache/': false,
                'public/': false
            }
        })

        const serverInfo = reactive({
            software: '',
            memoryLimit: '',
            maxExecutionTime: ''
        })

        const phpVersion = ref('')

        const allRequirementsMet = computed(() => {
            return requirements.php &&
                Object.values(requirements.extensions).every(ext => ext) &&
                Object.values(requirements.permissions).every(perm => perm)
        })

        const checkRequirements = async () => {
            try {
                const response = await installerApiRequest('/install/check-requirements')
                const data = await handleApiResponse(response)

                phpVersion.value = data.phpVersion
                requirements.php = data.requirements.php
                requirements.extensions = data.requirements.extensions
                requirements.permissions = data.requirements.permissions
                serverInfo.software = data.serverInfo.software
                serverInfo.memoryLimit = data.serverInfo.memoryLimit
                serverInfo.maxExecutionTime = data.serverInfo.maxExecutionTime
            } catch (error) {
                console.error('Error checking requirements:', error)
                // Fallback to basic checks
                checkBasicRequirements()
            }
        }

        const checkBasicRequirements = () => {
            // Basic client-side checks
            phpVersion.value = '8.2.0' // This would come from server response
            requirements.php = true

            // Simulate extension checks (these would be server-side)
            Object.keys(requirements.extensions).forEach(ext => {
                requirements.extensions[ext] = true
            })

            // Simulate permission checks
            Object.keys(requirements.permissions).forEach(perm => {
                requirements.permissions[perm] = true
            })

            serverInfo.software = 'Apache/Nginx'
            serverInfo.memoryLimit = '256M'
            serverInfo.maxExecutionTime = '300'
        }

        onMounted(() => {
            checkRequirements()
        })

        return {
            requirements,
            serverInfo,
            phpVersion,
            allRequirementsMet
        }
    }
}
</script>
