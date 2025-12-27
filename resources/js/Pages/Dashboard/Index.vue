<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900">
    <Head :title="$t('Dashboard')" />

    <!-- Enhanced Hero Section -->
    <div class="relative mb-8 overflow-hidden">
      <!-- Main Hero Container -->
      <div class="relative bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 rounded-3xl p-8 lg:p-12 text-white overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-primary-500/20 via-transparent to-primary-900/20"></div>
        <div class="absolute inset-0 bg-black/5"></div>

        <!-- Floating Elements -->
        <div class="absolute top-4 right-4 w-32 h-32 bg-white/5 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute bottom-8 left-8 w-24 h-24 bg-primary-300/10 rounded-full blur-lg animate-bounce" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 right-1/4 w-16 h-16 bg-white/10 rounded-full blur-md animate-pulse" style="animation-delay: 2s;"></div>

        <!-- Content -->
        <div class="relative z-10">
          <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
            <!-- Left Content -->
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                  <BarChart3 class="w-6 h-6 text-white" />
                </div>
                <div class="px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full">
                  <span class="text-sm font-medium">{{ $t('Dashboard Overview') }}</span>
                </div>
              </div>

              <h1 class="text-4xl lg:text-5xl font-bold mb-4 bg-gradient-to-r from-white to-primary-100 bg-clip-text text-transparent">
                {{ $t('Welcome back') }}, {{ auth.user.first_name || auth.user.name || 'User' }}!
              </h1>

              <p class="text-xl text-primary-100 mb-6 max-w-2xl">
                {{ $t('Here\'s what\'s happening with your support tickets today') }}
              </p>

              <!-- Quick Stats Row -->
              <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full">
                  <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                  <span class="text-sm font-medium">{{ $t('System Online') }}</span>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full">
                  <Clock class="w-4 h-4" />
                  <span class="text-sm font-medium">{{ new Date().toLocaleDateString() }}</span>
                </div>
              </div>
            </div>

            <!-- Right Content - Stats -->
            <div class="lg:text-right">
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 border border-white/20">
                <div class="text-center lg:text-right">
                  <p class="text-primary-200 text-sm font-medium mb-2">{{ $t('Total Tickets') }}</p>
                  <p class="text-5xl lg:text-6xl font-bold mb-2 bg-gradient-to-r from-white to-primary-100 bg-clip-text text-transparent">
                    {{ total_tickets }}
                  </p>
                  <div class="flex items-center justify-center lg:justify-end gap-2">
                    <TrendingUp class="w-4 h-4 text-green-400" />
                    <span class="text-sm text-green-400 font-medium">{{ $t('Active System') }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-96 h-96 opacity-5">
          <svg viewBox="0 0 400 400" fill="currentColor" class="w-full h-full">
            <defs>
              <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                <path d="M 40 0 L 0 0 0 40" fill="none" stroke="currentColor" stroke-width="1"/>
              </pattern>
            </defs>
            <rect width="400" height="400" fill="url(#grid)"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- Enhanced Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- New Tickets Card -->
      <div class="group cursor-pointer" @click="goToLink(this.route('tickets', {'type': 'new'}))">
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-2xl hover:shadow-blue-500/10 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-500 group-hover:scale-105 group-hover:-translate-y-1 overflow-hidden">
          <!-- Gradient Background -->
          <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent dark:from-blue-900/10 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

          <!-- Animated Border -->
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="padding: 1px;">
            <div class="w-full h-full bg-white dark:bg-slate-800 rounded-2xl"></div>
          </div>

          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <div class="relative w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                  <PlusCircle class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                  <div class="absolute inset-0 bg-blue-500/20 rounded-xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <div>
                  <p class="text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">{{ $t('New Tickets') }}</p>
                  <p class="text-3xl font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">{{ new_tickets }}</p>
                </div>
              </div>
              <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <ArrowRight class="w-5 h-5 text-blue-500" />
              </div>
            </div>

            <!-- Enhanced Progress Bar -->
            <div class="space-y-2">
              <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500 dark:text-slate-400">{{ $t('Progress') }}</span>
                <span class="font-semibold text-blue-600 dark:text-blue-400">
                  {{ parseInt((new_tickets * 100)/total_tickets) || 0 }}%
                </span>
              </div>
              <div class="relative">
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                  <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2.5 rounded-full transition-all duration-1000 ease-out relative overflow-hidden"
                       :style="{ width: `${Math.min((new_tickets * 100) / total_tickets, 100)}%` }">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Open Tickets Card -->
      <div class="group cursor-pointer" @click="goToLink(this.route('tickets', {'type': 'open'}))">
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-2xl hover:shadow-orange-500/10 hover:border-orange-300 dark:hover:border-orange-600 transition-all duration-500 group-hover:scale-105 group-hover:-translate-y-1 overflow-hidden">
          <!-- Gradient Background -->
          <div class="absolute inset-0 bg-gradient-to-br from-orange-50/50 to-transparent dark:from-orange-900/10 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

          <!-- Animated Border -->
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-orange-500 to-orange-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="padding: 1px;">
            <div class="w-full h-full bg-white dark:bg-slate-800 rounded-2xl"></div>
          </div>

          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <div class="relative w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                  <Clock class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                  <div class="absolute inset-0 bg-orange-500/20 rounded-xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <div>
                  <p class="text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors duration-300">{{ $t('Open Tickets') }}</p>
                  <p class="text-3xl font-bold text-slate-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors duration-300">{{ opened_tickets }}</p>
                </div>
              </div>
              <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <ArrowRight class="w-5 h-5 text-orange-500" />
              </div>
            </div>

            <!-- Enhanced Progress Bar -->
            <div class="space-y-2">
              <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500 dark:text-slate-400">{{ $t('Progress') }}</span>
                <span class="font-semibold text-orange-600 dark:text-orange-400">
                  {{ parseInt((opened_tickets * 100)/total_tickets) || 0 }}%
                </span>
              </div>
              <div class="relative">
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                  <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2.5 rounded-full transition-all duration-1000 ease-out relative overflow-hidden"
                       :style="{ width: `${Math.min((opened_tickets * 100) / total_tickets, 100)}%` }">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Closed Tickets Card -->
      <div class="group cursor-pointer" @click="goToLink(this.route('tickets', {'search': 'close'}))">
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-2xl hover:shadow-green-500/10 hover:border-green-300 dark:hover:border-green-600 transition-all duration-500 group-hover:scale-105 group-hover:-translate-y-1 overflow-hidden">
          <!-- Gradient Background -->
          <div class="absolute inset-0 bg-gradient-to-br from-green-50/50 to-transparent dark:from-green-900/10 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

          <!-- Animated Border -->
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-green-500 to-green-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="padding: 1px;">
            <div class="w-full h-full bg-white dark:bg-slate-800 rounded-2xl"></div>
          </div>

          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <div class="relative w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                  <CheckCircle class="w-6 h-6 text-green-600 dark:text-green-400" />
                  <div class="absolute inset-0 bg-green-500/20 rounded-xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <div>
                  <p class="text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors duration-300">{{ $t('Closed Tickets') }}</p>
                  <p class="text-3xl font-bold text-slate-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors duration-300">{{ closed_tickets }}</p>
                </div>
              </div>
              <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <ArrowRight class="w-5 h-5 text-green-500" />
              </div>
            </div>

            <!-- Enhanced Progress Bar -->
            <div class="space-y-2">
              <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500 dark:text-slate-400">{{ $t('Progress') }}</span>
                <span class="font-semibold text-green-600 dark:text-green-400">
                  {{ parseInt((closed_tickets * 100)/total_tickets) || 0 }}%
                </span>
              </div>
              <div class="relative">
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                  <div class="bg-gradient-to-r from-green-500 to-green-600 h-2.5 rounded-full transition-all duration-1000 ease-out relative overflow-hidden"
                       :style="{ width: `${Math.min((closed_tickets * 100) / total_tickets, 100)}%` }">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Unassigned Tickets Card (Admin/Manager only) -->
      <div v-if="auth.user.role.slug !== 'customer'" class="group cursor-pointer" @click="goToLink(this.route('tickets', {'type': 'un_assigned'}))">
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-2xl hover:shadow-red-500/10 hover:border-red-300 dark:hover:border-red-600 transition-all duration-500 group-hover:scale-105 group-hover:-translate-y-1 overflow-hidden">
          <!-- Gradient Background -->
          <div class="absolute inset-0 bg-gradient-to-br from-red-50/50 to-transparent dark:from-red-900/10 dark:to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

          <!-- Animated Border -->
          <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-red-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500" style="padding: 1px;">
            <div class="w-full h-full bg-white dark:bg-slate-800 rounded-2xl"></div>
          </div>

          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="flex items-center gap-3">
                <div class="relative w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900/30 dark:to-red-800/30 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                  <AlertTriangle class="w-6 h-6 text-red-600 dark:text-red-400" />
                  <div class="absolute inset-0 bg-red-500/20 rounded-xl blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <div>
                  <p class="text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors duration-300">{{ $t('Unassigned Tickets') }}</p>
                  <p class="text-3xl font-bold text-slate-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors duration-300">{{ un_assigned_tickets }}</p>
                </div>
              </div>
              <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <ArrowRight class="w-5 h-5 text-red-500" />
              </div>
            </div>

            <!-- Enhanced Progress Bar -->
            <div class="space-y-2">
              <div class="flex items-center justify-between text-xs">
                <span class="text-slate-500 dark:text-slate-400">{{ $t('Progress') }}</span>
                <span class="font-semibold text-red-600 dark:text-red-400">
                  {{ parseInt((un_assigned_tickets * 100)/total_tickets) || 0 }}%
                </span>
              </div>
              <div class="relative">
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                  <div class="bg-gradient-to-r from-red-500 to-red-600 h-2.5 rounded-full transition-all duration-1000 ease-out relative overflow-hidden"
                       :style="{ width: `${Math.min((un_assigned_tickets * 100) / total_tickets, 100)}%` }">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

     <div class="mb-8">
      <div class="flex items-center gap-4 mb-6">
        <div class="relative">
          <div class="w-2 h-10 bg-gradient-to-b from-primary-500 to-primary-600 rounded-full"></div>
          <div class="absolute inset-0 w-2 h-10 bg-gradient-to-b from-primary-400 to-primary-500 rounded-full blur-sm opacity-50"></div>
        </div>
        <div>
          <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Quick Actions') }}</h2>
          <p class="text-slate-600 dark:text-slate-400">{{ $t('Common tasks and shortcuts') }}</p>
        </div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <!-- Create Ticket -->
        <Link :href="route('tickets.create')" class="group">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 text-center hover:shadow-lg hover:shadow-blue-500/10 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 border border-slate-200 dark:border-slate-700 group-hover:scale-105">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <Plus class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">{{ $t('Create Ticket') }}</p>
          </div>
        </Link>

        <!-- View Tickets -->
        <Link :href="route('tickets')" class="group">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 text-center hover:shadow-lg hover:shadow-green-500/10 hover:border-green-300 dark:hover:border-green-600 transition-all duration-300 border border-slate-200 dark:border-slate-700 group-hover:scale-105">
            <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <CheckCircle class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors duration-300">{{ $t('View Tickets') }}</p>
          </div>
        </Link>

        <!-- Start Chat -->
        <Link :href="route('chat.index')" class="group">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 text-center hover:shadow-lg hover:shadow-purple-500/10 hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300 border border-slate-200 dark:border-slate-700 group-hover:scale-105">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <MessageCircle class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">{{ $t('Start Chat') }}</p>
          </div>
        </Link>

        <!-- View Reports -->
        <div class="group cursor-pointer" @click="goToLink(route('tickets', {'filter': 'reports'}))">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 text-center hover:shadow-lg hover:shadow-orange-500/10 hover:border-orange-300 dark:hover:border-orange-600 transition-all duration-300 border border-slate-200 dark:border-slate-700 group-hover:scale-105">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <BarChart3 class="w-6 h-6 text-orange-600 dark:text-orange-400" />
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors duration-300">{{ $t('View Reports') }}</p>
          </div>
        </div>

        <!-- Manage Users (Admin only) -->
        <Link v-if="auth?.user?.role?.slug === 'admin'" :href="route('users')" class="group">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 text-center hover:shadow-lg hover:shadow-indigo-500/10 hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300 border border-slate-200 dark:border-slate-700 group-hover:scale-105">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/30 dark:to-indigo-800/30 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <Users class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">{{ $t('Manage Users') }}</p>
          </div>
        </Link>

        <!-- Settings (Admin only) -->
        <Link v-if="auth?.user?.role?.slug === 'admin'" :href="route('settings.smtp')" class="group">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 text-center hover:shadow-lg hover:shadow-gray-500/10 hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-300 border border-slate-200 dark:border-slate-700 group-hover:scale-105">
            <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900/30 dark:to-gray-800/30 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <Building2 class="w-6 h-6 text-gray-600 dark:text-gray-400" />
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-gray-600 dark:group-hover:text-gray-400 transition-colors duration-300">{{ $t('Settings') }}</p>
          </div>
        </Link>
      </div>
    </div>

    <!-- Enhanced Dashboard Widgets -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-8">
      <!-- SLA Monitoring Widget -->

      <div v-if="false" class="sm:col-span-1 lg:col-span-1">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 sm:p-6">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900/30 dark:to-red-800/30 rounded-xl flex items-center justify-center">
                <AlertTriangle class="w-5 h-5 text-red-600 dark:text-red-400" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('SLA Monitoring') }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Service Level Agreement') }}</p>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Compliance Rate -->
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Compliance Rate') }}</span>
              <span class="text-2xl font-bold" :class="sla_metrics.compliance_rate >= 90 ? 'text-green-600' : sla_metrics.compliance_rate >= 80 ? 'text-yellow-600' : 'text-red-600'">
                {{ sla_metrics.compliance_rate }}%
              </span>
            </div>

            <!-- Breached Tickets -->
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Breached Tickets') }}</span>
              <span class="text-lg font-semibold text-red-600">{{ sla_metrics.breached_tickets }}</span>
            </div>

            <!-- At Risk Tickets -->
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('At Risk Tickets') }}</span>
              <span class="text-lg font-semibold text-yellow-600">{{ sla_metrics.at_risk_tickets }}</span>
            </div>

            <!-- Average Resolution Time -->
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Avg Resolution') }}</span>
              <span class="text-lg font-semibold text-blue-600">{{ sla_metrics.avg_resolution_time }}h</span>
            </div>
          </div>
        </div>
      </div>
      <!-- Recent Activities Widget -->
      <div v-if="false" class="sm:col-span-1 lg:col-span-1">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 sm:p-6">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl flex items-center justify-center">
                <Activity class="w-5 h-5 text-purple-600 dark:text-purple-400" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Recent Activities') }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Latest ticket activities') }}</p>
              </div>
            </div>
          </div>

          <div class="space-y-3 max-h-64 overflow-y-auto">
            <div v-for="activity in recent_activities" :key="activity.id" class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200">
              <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" :class="getActivityBgClass(activity.color)">
                <component :is="getActivityIcon(activity.icon)" class="w-4 h-4" :class="getActivityIconClass(activity.color)" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ activity.description }}</p>
                <div class="flex items-center gap-2 mt-1">
                  <span class="text-xs text-slate-500 dark:text-slate-400">{{ activity.user }}</span>
                  <span class="text-xs text-slate-400 dark:text-slate-500">•</span>
                  <span class="text-xs text-slate-500 dark:text-slate-400">{{ formatTime(activity.created_at) }}</span>
                  <span v-if="activity.ticket_uid" class="text-xs text-blue-600 dark:text-blue-400 font-medium">#{{ activity.ticket_uid }}</span>
                </div>
              </div>
            </div>
            <div v-if="recent_activities.length === 0" class="text-center py-8">
              <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('No recent activities') }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Conversation Metrics Widget -->
      <div v-if="false" class="sm:col-span-2 lg:col-span-1">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 sm:p-6">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-xl flex items-center justify-center">
                <MessageCircle class="w-5 h-5 text-green-600 dark:text-green-400" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Conversations') }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Chat & messaging') }}</p>
              </div>
            </div>
          </div>

          <div class="space-y-4">
            <!-- Total Conversations -->
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Total Conversations') }}</span>
              <span class="text-2xl font-bold text-green-600">{{ conversation_metrics.total_conversations }}</span>
            </div>

            <!-- Active Conversations -->
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Active') }}</span>
              <span class="text-lg font-semibold text-blue-600">{{ conversation_metrics.active_conversations }}</span>
            </div>

            <!-- Today's Conversations -->
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Today') }}</span>
              <span class="text-lg font-semibold text-purple-600">{{ conversation_metrics.today_conversations }}</span>
            </div>

            <!-- Average Messages -->
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Avg Messages') }}</span>
              <span class="text-lg font-semibold text-orange-600">{{ conversation_metrics.avg_messages_per_conversation }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

        <div class="flex flex-col lg:flex-row gap-4 items-stretch">
      <!-- Recent Activities Widget -->
      <div class="w-full lg:w-1/2 flex flex-col">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 sm:p-6 flex-1 flex flex-col">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl flex items-center justify-center">
                <Activity class="w-5 h-5 text-purple-600 dark:text-purple-400" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Recent Activities') }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Latest ticket activities') }}</p>
              </div>
            </div>
          </div>

          <div class="space-y-3 overflow-y-auto flex-1">
            <div v-for="activity in recent_activities" :key="activity.id" class="flex items-start gap-3 p-3 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors duration-200">
              <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0" :class="getActivityBgClass(activity.color)">
                <component :is="getActivityIcon(activity.icon)" class="w-4 h-4" :class="getActivityIconClass(activity.color)" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-900 dark:text-white truncate">{{ activity.description }}</p>
                <div class="flex items-center gap-2 mt-1">
                  <span class="text-xs text-slate-500 dark:text-slate-400">{{ activity.user }}</span>
                  <span class="text-xs text-slate-400 dark:text-slate-500">•</span>
                  <span class="text-xs text-slate-500 dark:text-slate-400">{{ formatTime(activity.created_at) }}</span>
                  <span v-if="activity.ticket_uid" class="text-xs text-blue-600 dark:text-blue-400 font-medium">#{{ activity.ticket_uid }}</span>
                </div>
              </div>
            </div>
            <div v-if="recent_activities.length === 0" class="text-center py-8">
              <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('No recent activities') }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Conversation Metrics Widget -->
      <div class="w-full lg:w-1/2 flex flex-col">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-4 sm:p-6 flex-1 flex flex-col">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-xl flex items-center justify-center">
                <MessageCircle class="w-5 h-5 text-green-600 dark:text-green-400" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Conversations') }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('Chat & messaging') }}</p>
              </div>
            </div>
          </div>

          <div class="space-y-4 flex-1 flex flex-col justify-center">
            <!-- Total Conversations -->
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Total Conversations') }}</span>
              <span class="text-2xl font-bold text-green-600">{{ conversation_metrics.total_conversations }}</span>
            </div>

            <!-- Active Conversations -->
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Active') }}</span>
              <span class="text-lg font-semibold text-blue-600">{{ conversation_metrics.active_conversations }}</span>
            </div>

            <!-- Today's Conversations -->
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Today') }}</span>
              <span class="text-lg font-semibold text-purple-600">{{ conversation_metrics.today_conversations }}</span>
            </div>

            <!-- Average Messages -->
            <div class="flex items-center justify-between">
              <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Avg Messages') }}</span>
              <span class="text-lg font-semibold text-orange-600">{{ conversation_metrics.avg_messages_per_conversation }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <br><br><br>


    <!-- AI Dashboard Components -->
    <div v-if="false" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- AI Dashboard Widget -->
      <div>
        <AIDashboardWidget />
      </div>
      
      <!-- AI Status Monitor -->
      <div>
        <AIStatusMonitor />
      </div>
    </div>

    <!-- Quick Actions Section -->
    <div v-if="false" class="mb-8">
      <div class="flex items-center gap-4 mb-6">
        <div class="relative">
          <div class="w-2 h-10 bg-gradient-to-b from-primary-500 to-primary-600 rounded-full"></div>
          <div class="absolute inset-0 w-2 h-10 bg-gradient-to-b from-primary-400 to-primary-500 rounded-full blur-sm opacity-50"></div>
        </div>
        <div>
          <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Quick Actions') }}</h2>
          <p class="text-slate-600 dark:text-slate-400">{{ $t('Common tasks and shortcuts') }}</p>
        </div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <!-- Create Ticket -->
        <Link :href="route('tickets.create')" class="group">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 text-center hover:shadow-lg hover:shadow-blue-500/10 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 border border-slate-200 dark:border-slate-700 group-hover:scale-105">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <Plus class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">{{ $t('Create Ticket') }}</p>
          </div>
        </Link>

        <!-- View Tickets -->
        <Link :href="route('tickets')" class="group">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 text-center hover:shadow-lg hover:shadow-green-500/10 hover:border-green-300 dark:hover:border-green-600 transition-all duration-300 border border-slate-200 dark:border-slate-700 group-hover:scale-105">
            <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <CheckCircle class="w-6 h-6 text-green-600 dark:text-green-400" />
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors duration-300">{{ $t('View Tickets') }}</p>
          </div>
        </Link>

        <!-- Start Chat -->
        <Link :href="route('chat.index')" class="group">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 text-center hover:shadow-lg hover:shadow-purple-500/10 hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300 border border-slate-200 dark:border-slate-700 group-hover:scale-105">
            <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <MessageCircle class="w-6 h-6 text-purple-600 dark:text-purple-400" />
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">{{ $t('Start Chat') }}</p>
          </div>
        </Link>

        <!-- View Reports -->
        <div class="group cursor-pointer" @click="goToLink(route('tickets', {'filter': 'reports'}))">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 text-center hover:shadow-lg hover:shadow-orange-500/10 hover:border-orange-300 dark:hover:border-orange-600 transition-all duration-300 border border-slate-200 dark:border-slate-700 group-hover:scale-105">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <BarChart3 class="w-6 h-6 text-orange-600 dark:text-orange-400" />
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors duration-300">{{ $t('View Reports') }}</p>
          </div>
        </div>

        <!-- Manage Users (Admin only) -->
        <Link v-if="auth?.user?.role?.slug === 'admin'" :href="route('users')" class="group">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 text-center hover:shadow-lg hover:shadow-indigo-500/10 hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300 border border-slate-200 dark:border-slate-700 group-hover:scale-105">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/30 dark:to-indigo-800/30 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <Users class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">{{ $t('Manage Users') }}</p>
          </div>
        </Link>

        <!-- Settings (Admin only) -->
        <Link v-if="auth?.user?.role?.slug === 'admin'" :href="route('settings.smtp')" class="group">
          <div class="bg-white dark:bg-slate-800 rounded-xl p-4 text-center hover:shadow-lg hover:shadow-gray-500/10 hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-300 border border-slate-200 dark:border-slate-700 group-hover:scale-105">
            <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900/30 dark:to-gray-800/30 rounded-xl flex items-center justify-center mx-auto mb-3 group-hover:scale-110 transition-transform duration-300">
              <Building2 class="w-6 h-6 text-gray-600 dark:text-gray-400" />
            </div>
            <p class="text-sm font-medium text-slate-900 dark:text-white group-hover:text-gray-600 dark:group-hover:text-gray-400 transition-colors duration-300">{{ $t('Settings') }}</p>
          </div>
        </Link>
      </div>
    </div>

    <!-- Enhanced Analytics Section (Admin/Manager only) -->
    <div v-if="['admin', 'manager'].includes(auth?.user?.role?.slug)" class="mb-8">
      <div class="flex items-center gap-4 mb-8">
        <div class="relative">
          <div class="w-2 h-10 bg-gradient-to-b from-primary-500 to-primary-600 rounded-full"></div>
          <div class="absolute inset-0 w-2 h-10 bg-gradient-to-b from-primary-400 to-primary-500 rounded-full blur-sm opacity-50"></div>
        </div>
        <div>
          <h2 class="text-3xl font-bold text-slate-900 dark:text-white">{{ $t('Analytics Overview') }}</h2>
          <p class="text-slate-600 dark:text-slate-400 mt-1">{{ $t('Comprehensive insights into your support operations') }}</p>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Tickets by Department -->
        <div class="group relative">
          <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-purple-600/20 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <div class="relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/50 p-8 hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-500 group-hover:scale-105">
            <div class="flex items-center gap-4 mb-6">
              <div class="relative w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <Building2 class="w-7 h-7 text-purple-600 dark:text-purple-400" />
                <div class="absolute inset-0 bg-purple-500/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
              <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $t('Tickets by Department') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Distribution across teams') }}</p>
              </div>
            </div>
            <div class="flex justify-center">
              <vc-donut
                  background="transparent" foreground="grey"
                  :size="220" unit="px" :thickness="35"
                  has-legend legend-placement="bottom"
                  :sections="top_departments" :total="100"
                  :start-angle="0" :auto-adjust-text-size="true">
              </vc-donut>
            </div>
          </div>
        </div>

        <!-- Tickets by Type -->
        <div class="group relative">
          <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-indigo-600/20 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <div class="relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/50 p-8 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500 group-hover:scale-105">
            <div class="flex items-center gap-4 mb-6">
              <div class="relative w-14 h-14 bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/30 dark:to-indigo-800/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <Tag class="w-7 h-7 text-indigo-600 dark:text-indigo-400" />
                <div class="absolute inset-0 bg-indigo-500/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
              <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $t('Tickets by Type') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Issue categorization') }}</p>
              </div>
            </div>
            <div class="flex justify-center">
              <vc-donut
                  background="transparent" foreground="grey"
                  :size="220" unit="px" :thickness="35"
                  has-legend legend-placement="bottom"
                  :sections="top_types" :total="100"
                  :start-angle="0" :auto-adjust-text-size="true">
              </vc-donut>
            </div>
          </div>
        </div>

        <!-- Top Ticket Creators -->
        <div class="group relative">
          <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/20 to-emerald-600/20 rounded-3xl blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
          <div class="relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 dark:border-slate-700/50 p-8 hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-500 group-hover:scale-105">
            <div class="flex items-center gap-4 mb-6">
              <div class="relative w-14 h-14 bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-900/30 dark:to-emerald-800/30 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <Users class="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
                <div class="absolute inset-0 bg-emerald-500/20 rounded-2xl blur-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
              <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $t('Top Ticket Creators') }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $t('Most active users') }}</p>
              </div>
            </div>
            <div class="flex justify-center">
              <vc-donut
                  background="transparent" foreground="grey"
                  :size="220" unit="px" :thickness="35"
                  has-legend legend-placement="bottom"
                  :sections="top_creators" :total="100"
                  :start-angle="0" :auto-adjust-text-size="true">
              </vc-donut>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Performance Metrics Section (Admin/Manager only) -->
    <div v-if="['admin', 'manager'].includes(auth?.user?.role?.slug)" class="mb-8">
      <div class="flex items-center gap-3 mb-6">
        <div class="w-1 h-8 bg-gradient-to-b from-emerald-500 to-emerald-600 rounded-full"></div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Performance Metrics') }}</h2>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Ticket History Chart -->
        <div class="lg:col-span-8">
          <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                  <BarChart3 class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Ticket History') }}</h3>
              </div>
              <div class="text-right">
                <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ chart_line.this_month }}</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $t('this month') }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500">{{ $t('last month') }}: {{ chart_line.last_month }}</p>
              </div>
            </div>

            <!-- Custom Bar Chart -->
            <div v-if="months.length" class="flex items-end justify-between h-32 gap-2">
              <div v-for="(cl, index) in months" :key="index" class="flex flex-col items-center flex-1">
                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-t-lg relative group">
                  <div class="bg-gradient-to-t from-primary-500 to-primary-400 rounded-t-lg transition-all duration-500 hover:from-primary-600 hover:to-primary-500"
                       :style="{ height: cl.value }"
                       :title="`${cl.month}: ${cl.value}`">
                  </div>
                </div>
                <span class="text-xs text-slate-500 dark:text-slate-400 mt-2 text-center">{{ cl.month }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Response Time Metrics -->
        <div class="lg:col-span-4">
          <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
            <div class="flex items-center gap-3 mb-6">
              <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                <Clock class="w-5 h-5 text-orange-600 dark:text-orange-400" />
              </div>
              <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $t('Response Times') }}</h3>
            </div>

            <!-- First Response Time -->
            <div class="mb-6">
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('First Response') }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Average') }}</span>
              </div>
              <div class="flex items-center gap-2">
                <div v-if="firstResponse.length" v-for="(fr, fri) in firstResponse" :key="fri" class="text-center">
                  <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ fr[0] }}</span>
                  <span class="text-xs text-slate-500 dark:text-slate-400 block">{{ $t(fr[1]) }}</span>
                </div>
                <div v-else class="text-center">
                  <span class="text-2xl font-bold text-slate-500 dark:text-slate-400">0</span>
                </div>
              </div>
            </div>

            <!-- Last Response Time -->
            <div>
              <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Last Response') }}</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $t('Average') }}</span>
              </div>
              <div class="flex items-center gap-2">
                <div v-if="lastResponse.length" v-for="(fr, fri) in lastResponse" :key="fri" class="text-center">
                  <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ fr[0] }}</span>
                  <span class="text-xs text-slate-500 dark:text-slate-400 block">{{ $t(fr[1]) }}</span>
                </div>
                <div v-else class="text-center">
                  <span class="text-2xl font-bold text-slate-500 dark:text-slate-400">0</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Stats Section (Admin/Manager only) -->
    <div v-if="['admin', 'manager'].includes(auth?.user?.role?.slug)" class="mb-8">
      <div class="flex items-center gap-3 mb-6">
        <div class="w-1 h-8 bg-gradient-to-b from-amber-500 to-amber-600 rounded-full"></div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $t('Quick Stats') }}</h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Customers Card -->
        <div class="group cursor-pointer" @click="goToLink(this.route('users'))">
          <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-lg hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-300 group-hover:scale-105">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                  <Users class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                  <p class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Total Customers') }}</p>
                  <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ total_customer }}</p>
                </div>
              </div>
              <ChevronRight class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors duration-200" />
            </div>
          </div>
        </div>

        <!-- Contacts Card -->
        <div class="group cursor-pointer" @click="goToLink(this.route('contacts'))">
          <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 hover:shadow-lg hover:border-cyan-300 dark:hover:border-cyan-600 transition-all duration-300 group-hover:scale-105">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center">
                  <UserPlus class="w-6 h-6 text-cyan-600 dark:text-cyan-400" />
                </div>
                <div>
                  <p class="text-sm font-medium text-slate-600 dark:text-slate-400">{{ $t('Total Contacts') }}</p>
                  <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ total_contacts }}</p>
                </div>
              </div>
              <ChevronRight class="w-5 h-5 text-slate-400 group-hover:text-cyan-500 transition-colors duration-200" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Loading Overlay -->
    <Transition
      enter-active-class="transition-opacity duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-300"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="loading" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-2xl">
          <div class="flex flex-col items-center gap-4">
            <div class="relative">
              <div class="w-16 h-16 border-4 border-primary-200 dark:border-primary-800 rounded-full"></div>
              <div class="absolute top-0 left-0 w-16 h-16 border-4 border-primary-600 border-t-transparent rounded-full animate-spin"></div>
            </div>
            <p class="text-slate-600 dark:text-slate-400 font-medium">{{ $t('Loading dashboard data...') }}</p>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script>
import {Head, Link} from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import Icon from '@/Shared/Icon.vue'
import {
    PlusCircle,
    Clock,
    CheckCircle,
    AlertTriangle,
    Building2,
    Tag,
    Users,
    BarChart3,
    UserPlus,
    ChevronRight,
    TrendingUp,
    ArrowRight,
    Activity,
    MessageCircle,
    Plus,
    UserCheck,
    RefreshCw,
    Paperclip
} from 'lucide-vue-next'
import AIDashboardWidget from '@/Components/AI/AIDashboardWidget.vue'
import AIStatusMonitor from '@/Components/AI/AIStatusMonitor.vue'

export default {
  metaInfo: { title: 'Dashboard' },
    components: {
        Head,
        Icon,
        Link,
        PlusCircle,
        Clock,
        CheckCircle,
        AlertTriangle,
        Building2,
        Tag,
        Users,
        BarChart3,
        UserPlus,
        ChevronRight,
        TrendingUp,
        ArrowRight,
        Activity,
        MessageCircle,
        Plus,
        UserCheck,
        RefreshCw,
        Paperclip,
        AIDashboardWidget,
        AIStatusMonitor
    },
  layout: Layout,
    props: {
        auth: Object,
        entries: Array,
        chart_line: Object,
        api_key: String,
        new_tickets: Number,
        total_tickets: Number,
        un_assigned_tickets: Number,
        opened_tickets: Number,
        closed_tickets: Number,
        first_response: Array,
        top_creators: Array,
        last_response: Array,
        top_departments: Array,
        top_types: Array,
        total_customer: Number,
        total_contacts: Number,
        // Enhanced dashboard props
        enhanced_metrics: Object,
        recent_activities: Array,
        sla_metrics: Object,
        conversation_metrics: Object,
    },
    data() {
        return {
            errors: [],
            loading: false,
            firstResponse: [],
            lastResponse: [],
            months: []
        }
    },
    created() {
        for (let i = 0; i < this.first_response.length; i++) {
            if(i % 2 === 0){
                this.firstResponse = [...this.firstResponse, [this.first_response[i], this.first_response[i+1]]]
            }
        }
        for (let i = 0; i < this.last_response.length; i++) {
            if(i % 2 === 0){
                this.lastResponse = [...this.lastResponse, [this.last_response[i], this.last_response[i+1]]]
            }
        }

        this.months = this.chart_line.previousMonths.map( m =>{
            return { 'month': m, 'value': this.chart_line.months[m] ? ((this.chart_line.months[m] * 100)/this.chart_line.total)+'%': '0%' }
        })
    },
    methods: {
        goToLink(link){
            window.location.href = link;
        },
        getActivityIcon(iconName) {
            const icons = {
                'plus-circle': Plus,
                'user-check': UserCheck,
                'refresh-cw': RefreshCw,
                'message-circle': MessageCircle,
                'paperclip': Paperclip,
                'alert-triangle': AlertTriangle,
                'activity': Activity
            };
            return icons[iconName] || Activity;
        },
        getActivityBgClass(color) {
            const classes = {
                'green': 'bg-green-100 dark:bg-green-900/30',
                'blue': 'bg-blue-100 dark:bg-blue-900/30',
                'yellow': 'bg-yellow-100 dark:bg-yellow-900/30',
                'purple': 'bg-purple-100 dark:bg-purple-900/30',
                'gray': 'bg-gray-100 dark:bg-gray-900/30',
                'red': 'bg-red-100 dark:bg-red-900/30'
            };
            return classes[color] || 'bg-gray-100 dark:bg-gray-900/30';
        },
        getActivityIconClass(color) {
            const classes = {
                'green': 'text-green-600 dark:text-green-400',
                'blue': 'text-blue-600 dark:text-blue-400',
                'yellow': 'text-yellow-600 dark:text-yellow-400',
                'purple': 'text-purple-600 dark:text-purple-400',
                'gray': 'text-gray-600 dark:text-gray-400',
                'red': 'text-red-600 dark:text-red-400'
            };
            return classes[color] || 'text-gray-600 dark:text-gray-400';
        },
        formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));
            
            if (diffInMinutes < 1) return 'Just now';
            if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
            
            const diffInHours = Math.floor(diffInMinutes / 60);
            if (diffInHours < 24) return `${diffInHours}h ago`;
            
            const diffInDays = Math.floor(diffInHours / 24);
            if (diffInDays < 7) return `${diffInDays}d ago`;
            
            return date.toLocaleDateString();
        }
    },
}
</script>

