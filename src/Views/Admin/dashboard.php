<div class="bg-black min-h-screen">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white">Admin Dashboard</h1>
                <p class="text-gray-400 mt-1">Welcome back, <?= htmlspecialchars(\App\Core\Auth::user()['name'] ?? 'Administrator') ?>!</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Membership Stats -->
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-gold-400 bg-opacity-10 rounded-full p-3">
                                <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-400 truncate">Total Membership Applications</dt>
                                <dd class="text-2xl font-semibold text-white"><?= $stats['membership_total'] ?></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-400">
                        <span class="text-gold-400"><?= $stats['membership_drafts'] ?></span> drafts, 
                        <span class="text-green-400"><?= $stats['membership_submitted'] ?></span> submitted
                    </div>
                </div>

                <!-- Trainee Stats -->
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-gold-400 bg-opacity-10 rounded-full p-3">
                                <svg class="w-6 h-6 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-400 truncate">Total Trainee Applications</dt>
                                <dd class="text-2xl font-semibold text-white"><?= $stats['trainee_total'] ?></dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-400">
                        <span class="text-gold-400"><?= $stats['trainee_drafts'] ?></span> drafts, 
                        <span class="text-green-400"><?= $stats['trainee_submitted'] ?></span> submitted
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-green-500 bg-opacity-10 rounded-full p-3">
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-400 truncate">System Status</dt>
                                <dd class="text-2xl font-semibold text-green-400">Online</dd>
                            </dl>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-400">
                        Phase 3
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                <h2 class="text-xl font-semibold text-white mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="/admin/applications" class="flex items-center p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                        <svg class="w-5 h-5 text-gold-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-white">View Applications</span>
                    </a>
                    <a href="/admin/payments" class="flex items-center p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                        <svg class="w-5 h-5 text-gold-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        <span class="text-white">Payment Records</span>
                    </a>
                    <a href="/admin/reports" class="flex items-center p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                        <svg class="w-5 h-5 text-gold-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <span class="text-white">Reports</span>
                    </a>
                    <a href="/admin/settings" class="flex items-center p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors">
                        <svg class="w-5 h-5 text-gold-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="text-white">Settings</span>
                    </a>
                </div>
                <p class="text-sm text-gray-400 mt-4">
                    <em>Note: Additional features will be available in upcoming phases.</em>
                </p>
            </div>
        </div>
    </div>
</div>