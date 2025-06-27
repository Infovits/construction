<?= $this->extend('layouts/main') ?>



<?= $this->section('content') ?>
   <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 md:p-6">
                        <div class="flex items-center space-x-3 md:space-x-4">
                            <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                                <i data-lucide="bell" class="w-5 h-5 md:w-6 md:h-6 text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm md:text-base">NOTIFICATION</h3>
                                <p class="text-xs md:text-sm text-gray-600">50 Unread Notifications</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 md:p-6">
                        <div class="flex items-center space-x-3 md:space-x-4">
                            <div class="w-10 h-10 md:w-12 md:h-12 bg-green-500 rounded-full flex items-center justify-center">
                                <i data-lucide="folder-open" class="w-5 h-5 md:w-6 md:h-6 text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm md:text-base">PROJECT</h3>
                                <p class="text-xs md:text-sm text-gray-600">4 Project Last Update</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 md:p-6">
                        <div class="flex items-center space-x-3 md:space-x-4">
                            <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-500 rounded-full flex items-center justify-center">
                                <i data-lucide="users" class="w-5 h-5 md:w-6 md:h-6 text-white"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm md:text-base">CLIENT</h3>
                                <p class="text-xs md:text-sm text-gray-600">2 Client Waiting</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-indigo-600 rounded-xl p-4 md:p-6 text-white">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="flex items-center space-x-2 mb-2">
                                    <i data-lucide="plus" class="w-4 h-4 md:w-5 md:h-5"></i>
                                    <span class="font-semibold text-sm md:text-base">CREATE NEW</span>
                                </div>
                                <p class="text-xs md:text-sm opacity-90">PROJECT</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 md:gap-8">
                    <!-- Left Column -->
                    <div class="xl:col-span-2 space-y-6 md:space-y-8">
                        <!-- Client Statistics -->
                        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                            <div class="flex items-center justify-between mb-4 md:mb-6">
                                <h3 class="text-base md:text-lg font-semibold text-gray-800">Client Statistic</h3>
                                <div class="flex items-center space-x-1 md:space-x-2 overflow-x-auto">
                                    <button class="px-2 md:px-3 py-1 text-xs md:text-sm bg-blue-500 text-white rounded-full flex items-center space-x-1 flex-shrink-0">
                                        <span class="text-xs">🇺🇸</span>
                                        <span>US</span>
                                    </button>
                                    <button class="px-2 md:px-3 py-1 text-xs md:text-sm border border-gray-300 rounded-full flex items-center space-x-1 flex-shrink-0">
                                        <span class="text-xs">🇬🇧</span>
                                        <span>UK</span>
                                    </button>
                                    <button class="px-2 md:px-3 py-1 text-xs md:text-sm border border-gray-300 rounded-full flex items-center space-x-1 flex-shrink-0">
                                        <span class="text-xs">🇫🇷</span>
                                        <span>France</span>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4 md:space-x-6 mb-4 overflow-x-auto">
                                <button class="text-xs md:text-sm font-medium text-gray-600 hover:text-indigo-600 whitespace-nowrap">1M</button>
                                <button class="text-xs md:text-sm font-medium text-gray-600 hover:text-indigo-600 whitespace-nowrap">3M</button>
                                <button class="text-xs md:text-sm font-medium text-gray-600 hover:text-indigo-600 whitespace-nowrap">6M</button>
                                <button class="text-xs md:text-sm font-medium text-indigo-600 border-b-2 border-indigo-600 pb-1 whitespace-nowrap">All time</button>
                            </div>
                            
                            <div class="h-48 md:h-64">
                                <canvas id="clientChart"></canvas>
                            </div>
                        </div>

                        <!-- Bottom Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                            <!-- Site Health -->
                            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                                <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-2">Site Health</h3>
                                <p class="text-xs md:text-sm text-gray-600 mb-4 md:mb-6">Check your site health score</p>
                                
                                <div class="relative w-24 h-24 md:w-32 md:h-32 mx-auto mb-4">
                                    <canvas id="siteHealthChart"></canvas>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-xl md:text-2xl font-bold text-gray-800">84%</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-3 h-3 bg-indigo-500 rounded-full"></div>
                                            <span class="text-xs md:text-sm text-gray-600">Your site</span>
                                        </div>
                                        <span class="text-xs md:text-sm font-medium">84%</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                            <span class="text-xs md:text-sm text-gray-600">Top 10% websites</span>
                                        </div>
                                        <span class="text-xs md:text-sm font-medium">92%</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Online Sales -->
                            <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                                <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-2">Online Sales</h3>
                                <p class="text-xs md:text-sm text-gray-600 mb-4 md:mb-6">Percentage of product a user demands</p>
                                
                                <div class="relative w-24 h-24 md:w-32 md:h-32 mx-auto mb-4">
                                    <canvas id="onlineSalesChart"></canvas>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="text-center">
                                            <div class="text-xl md:text-2xl font-bold text-gray-800">80%</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-center">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-xs md:text-sm text-gray-600">Mobile 80%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Daily Tasks -->
                    <div class="space-y-4 md:space-y-6">
                        <!-- Manage Project Card -->
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-4 md:p-6 text-white">
                            <h3 class="text-base md:text-lg font-semibold mb-2">Manage your project in one touch</h3>
                            <p class="text-xs md:text-sm opacity-90 mb-4">Etiam facilisis ligula nec velit posuere egestas. Nunc dictum.</p>
                            <button class="bg-white text-indigo-600 px-3 md:px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors text-sm">
                                Try Free
                            </button>
                        </div>

                        <!-- Daily Task -->
                        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                            <h3 class="text-base md:text-lg font-semibold text-gray-800 mb-2">Daily Task</h3>
                            <p class="text-xs md:text-sm text-gray-600 mb-4 md:mb-6">Percentage of product a user demands</p>
                            
                            <div class="space-y-2 md:space-y-3">
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-xs md:text-sm text-gray-500">10:00</span>
                                </div>
                                
                                <div class="bg-indigo-600 rounded-lg p-2 md:p-3">
                                    <div class="text-white font-medium text-sm md:text-base">iOS Dev Team Meeting</div>
                                    <div class="text-indigo-200 text-xs md:text-sm">10:00 - 12:00</div>
                                </div>
                                
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-xs md:text-sm text-gray-500">01:00</span>
                                </div>
                                
                                <div class="bg-yellow-500 rounded-lg p-2 md:p-3">
                                    <div class="text-white font-medium text-sm md:text-base">SEO Analytics</div>
                                    <div class="text-yellow-200 text-xs md:text-sm">01:00 - 03:00</div>
                                </div>
                                
                                <div class="bg-red-500 rounded-lg p-2 md:p-3">
                                    <div class="text-white font-medium text-sm md:text-base">Logo</div>
                                    <div class="text-red-200 text-xs md:text-sm">01:00 - 03:00</div>
                                </div>
                                
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-xs md:text-sm text-gray-500">04:00</span>
                                </div>
                                
                                <div class="bg-green-500 rounded-lg p-2 md:p-3">
                                    <div class="text-white font-medium text-sm md:text-base">Digital Marketing</div>
                                    <div class="text-green-200 text-xs md:text-sm">04:00 - 05:00</div>
                                </div>
                                
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-xs md:text-sm text-gray-500">06:00</span>
                                </div>
                                
                                <div class="bg-gray-300 rounded-lg p-2 md:p-3">
                                    <div class="text-gray-700 font-medium text-sm md:text-base">Web development</div>
                                    <div class="text-gray-600 text-xs md:text-sm">06:00 - 08:00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

   
<?= $this->endSection() ?>

<?= $this->section('css') ?>
    
<?= $this->endSection() ?>

<?= $this->section('js') ?>
    
<?= $this->endSection() ?>