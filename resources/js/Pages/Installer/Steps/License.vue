<template>
    <div class="animate-fade-in-up">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <Shield class="w-10 h-10 text-white" />
            </div>
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">
                License Verification
            </h2>
            <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                Please enter your CodeCanyon purchase code to verify your license and continue with the installation.
            </p>
        </div>

        <!-- License Form -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6 mb-6">
                <div class="space-y-4">
                    <!-- Purchase Code Input -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Purchase Code <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input
                                v-model="form.purchaseCode"
                                type="text"
                                placeholder="Enter your CodeCanyon purchase code"
                                class="w-full px-4 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400 dark:focus:border-blue-400 transition-all duration-200"
                                :class="{ 'border-red-500 dark:border-red-400 focus:ring-red-500 focus:border-red-500': errors.purchaseCode }"
                                @input="clearErrors"
                            />
                            <div v-if="verificationStatus === 'verifying'" class="absolute right-3 top-3">
                                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                            </div>
                            <div v-else-if="verificationStatus === 'verified'" class="absolute right-3 top-3">
                                <Check class="w-5 h-5 text-green-600 dark:text-green-400" />
                            </div>
                            <div v-else-if="verificationStatus === 'failed'" class="absolute right-3 top-3">
                                <X class="w-5 h-5 text-red-600 dark:text-red-400" />
                            </div>
                        </div>
                        <p v-if="errors.purchaseCode" class="mt-1 text-sm text-red-600 dark:text-red-400">
                            {{ errors.purchaseCode }}
                        </p>
                        <p v-else class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                            You can find your purchase code in your CodeCanyon account under "Downloads"
                        </p>
                    </div>

                    <!-- Verification Status -->
                    <div v-if="verificationStatus === 'verified'" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <div class="flex items-center">
                            <Check class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" />
                            <div>
                                <h4 class="text-sm font-medium text-green-800 dark:text-green-200">License Verified Successfully</h4>
                                <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                                    Your purchase code is valid and you can proceed with the installation.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-else-if="verificationStatus === 'failed'" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <div class="flex items-center">
                            <X class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" />
                            <div>
                                <h4 class="text-sm font-medium text-red-800 dark:text-red-200">License Verification Failed</h4>
                                <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                                    {{ verificationError || 'Please check your purchase code and try again.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Section -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-6">
                <div class="flex items-start">
                    <Info class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-3 mt-0.5" />
                    <div>
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">Need Help Finding Your Purchase Code?</h4>
                        <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                            <li>• Log in to your CodeCanyon account</li>
                            <li>• Go to "Downloads" section</li>
                            <li>• Find your HelpDesk purchase</li>
                            <li>• Copy the purchase code from the license details</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- License Information -->
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-6">
                <h4 class="text-sm font-medium text-slate-900 dark:text-white mb-3">License Information</h4>
                <div class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                    <p>• This is a single-use license for one domain</p>
                    <p>• You can use this code for development and production</p>
                    <p>• Support and updates are included for 6 months</p>
                    <p>• Please keep your purchase code secure and private</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
            <button
                @click="$emit('back')"
                class="px-6 py-3 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
            >
                <ArrowLeft class="w-4 h-4 mr-2 inline" />
                Back
            </button>

            <div class="flex space-x-3">
                <button
                    @click="verifyLicense"
                    :disabled="!form.purchaseCode || verificationStatus === 'verifying'"
                    :class="[
            'px-6 py-3 rounded-lg font-medium transition-all duration-200',
            form.purchaseCode && verificationStatus !== 'verifying'
              ? 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-600'
              : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 cursor-not-allowed'
          ]"
                >
                    <Shield class="w-4 h-4 mr-2 inline" />
                    {{ verificationStatus === 'verifying' ? 'Verifying...' : 'Verify License' }}
                </button>

                <button
                    @click="$emit('next')"
                    :disabled="verificationStatus !== 'verified'"
                    :class="[
            'px-8 py-3 rounded-lg font-semibold transition-all duration-200 transform',
            verificationStatus === 'verified'
              ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white hover:scale-105 shadow-lg hover:shadow-xl'
              : 'bg-slate-300 dark:bg-slate-600 text-slate-500 dark:text-slate-400 cursor-not-allowed'
          ]"
                >
                    Continue
                    <ArrowRight class="w-4 h-4 ml-2 inline" />
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, reactive, watch } from 'vue'
import { Shield, Check, X, Info, ArrowLeft, ArrowRight } from 'lucide-vue-next'
import { installerApiRequest, handleApiResponse } from '../../../Utils/InstallerApi.js'

export default {
    name: 'InstallerLicense',
    components: {
        Shield,
        Check,
        X,
        Info,
        ArrowLeft,
        ArrowRight
    },
    props: {
        form: {
            type: Object,
            required: true
        }
    },
    emits: ['next', 'back', 'update:form'],
    setup(props, { emit }) {
        const verificationStatus = ref('') // '', 'verifying', 'verified', 'failed'
        const verificationError = ref('')
        const errors = reactive({})

        const clearErrors = () => {
            Object.keys(errors).forEach(key => delete errors[key])
            verificationError.value = ''
        }

        const verifyLicense = async () => {
            if (!props.form.purchaseCode) {
                errors.purchaseCode = 'Purchase code is required'
                return
            }

            verificationStatus.value = 'verifying'
            clearErrors()

            try {
                const response = await installerApiRequest('/install/verify-license', {
                    body: JSON.stringify({
                        purchase_code: props.form.purchaseCode
                    })
                })

                const data = await handleApiResponse(response)

                if (data.success) {
                    verificationStatus.value = 'verified'
                    emit('update:form', { ...props.form, verified: true })
                } else {
                    verificationStatus.value = 'failed'
                    verificationError.value = data.message || 'Invalid purchase code'
                }
            } catch (error) {
                verificationStatus.value = 'failed'
                verificationError.value = 'Network error. Please try again.'
                console.error('License verification error:', error)
            }
        }

        // Watch for form changes
        watch(() => props.form.purchaseCode, (newValue) => {
            emit('update:form', { ...props.form, purchaseCode: newValue })
        })

        return {
            verificationStatus,
            verificationError,
            errors,
            clearErrors,
            verifyLicense
        }
    }
}
</script>
