<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Safety Analytics<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.metric-card { @apply bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow; }
.chart-container { @apply bg-white rounded-lg shadow-sm border p-6; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><i class="fas fa-chart-line mr-2"></i>Safety Analytics Dashboard</h1>
            <p class="text-gray-600 mt-1">View safety trends and performance metrics</p>
        </div>
    </div>

    <!-- Date Range Selector -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form method="GET" action="<?= base_url('incident-safety/analytics') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Projects</option>
                    <?php if (!empty($projects)): ?>
                        <?php foreach ($projects as $project): ?>
                            <option value="<?= $project['id'] ?>" 
                                <?= request()->getGet('project_id') == $project['id'] ? 'selected' : '' ?>>
                                <?= $project['name'] ?? 'N/A' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Time Period</label>
                <select name="period" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="30days" <?= request()->getGet('period') === '30days' ? 'selected' : '' ?>>Last 30 Days</option>
                    <option value="90days" <?= request()->getGet('period') === '90days' ? 'selected' : '' ?>>Last 90 Days</option>
                    <option value="180days" <?= request()->getGet('period') === '180days' ? 'selected' : '' ?>>Last 180 Days</option>
                    <option value="yearly" <?= request()->getGet('period') === 'yearly' ? 'selected' : '' ?>>This Year</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i> Update
                </button>
            </div>
        </form>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Incidents -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Incidents</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $analytics['total_incidents'] ?? 0 ?></h3>
                </div>
                <div class="text-4xl text-blue-600">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="mt-3 text-xs">
                <?= !empty($analytics['previous_month_incidents']) ? 
                    ($analytics['total_incidents'] > $analytics['previous_month_incidents'] ? 
                        '<span class="text-red-600">↑ ' . ($analytics['total_incidents'] - $analytics['previous_month_incidents']) . ' vs last period</span>' : 
                        '<span class="text-green-600">↓ ' . ($analytics['previous_month_incidents'] - $analytics['total_incidents']) . ' vs last period</span>') : 
                    '' ?>
            </div>
        </div>

        <!-- Critical Incidents -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Critical Incidents</p>
                    <h3 class="text-3xl font-bold text-red-600"><?= $analytics['critical_incidents'] ?? 0 ?></h3>
                </div>
                <div class="text-4xl text-red-600">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-600">Require immediate action</div>
        </div>

        <!-- Near Misses -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Near Misses</p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= $analytics['total_near_misses'] ?? 0 ?></h3>
                </div>
                <div class="text-4xl text-orange-600">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-600">Potential hazards prevented</div>
        </div>

        <!-- People Injured -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm text-gray-600 mb-1">People Injured</p>
                    <h3 class="text-3xl font-bold text-red-600"><?= $analytics['total_injured_people'] ?? 0 ?></h3>
                </div>
                <div class="text-4xl text-red-600">
                    <i class="fas fa-user-injured"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-gray-600">Across all incidents</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Incidents by Severity -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b">Incidents by Severity</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Critical</span>
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium"><?= $analytics['critical_incidents'] ?? 0 ?></span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-red-600" style="width: <?= !empty($analytics['total_incidents']) && $analytics['critical_incidents'] > 0 ? ($analytics['critical_incidents'] / $analytics['total_incidents'] * 100) : 0 ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">High</span>
                        <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-medium"><?= $analytics['high_incidents'] ?? 0 ?></span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-orange-600" style="width: <?= !empty($analytics['total_incidents']) && $analytics['high_incidents'] > 0 ? ($analytics['high_incidents'] / $analytics['total_incidents'] * 100) : 0 ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Medium</span>
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium"><?= $analytics['medium_incidents'] ?? 0 ?></span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-yellow-600" style="width: <?= !empty($analytics['total_incidents']) && $analytics['medium_incidents'] > 0 ? ($analytics['medium_incidents'] / $analytics['total_incidents'] * 100) : 0 ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Low</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium"><?= $analytics['low_incidents'] ?? 0 ?></span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-green-600" style="width: <?= !empty($analytics['total_incidents']) && $analytics['low_incidents'] > 0 ? ($analytics['low_incidents'] / $analytics['total_incidents'] * 100) : 0 ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Safety Audit Compliance -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b">Safety Audit Compliance</h3>
            <div class="flex justify-center">
                <div class="text-center">
                    <div class="relative inline-flex items-center justify-center w-40 h-40 rounded-full bg-gray-100 mb-4">
                        <div class="text-center">
                            <p class="text-4xl font-bold text-green-600"><?= $analytics['audit_compliance_percentage'] ?? 0 ?>%</p>
                            <p class="text-sm text-gray-600 mt-1">Compliant</p>
                        </div>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden mt-4">
                        <div class="h-full bg-green-500" style="width: <?= $analytics['audit_compliance_percentage'] ?? 0 ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trend Analysis -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4 pb-4 border-b">Safety Trend</h3>
        <div class="mb-6">
            <?php 
            $trendColor = 'bg-green-50 border border-green-200 text-green-800';
            $trendIcon = 'arrow-down';
            $trendText = 'Positive Trend: Safety performance is improving';
            
            if ($analytics['trend_direction'] === 'declining') {
                $trendColor = 'bg-red-50 border border-red-200 text-red-800';
                $trendIcon = 'arrow-up';
                $trendText = 'Concerning Trend: Safety incidents are increasing';
            } elseif ($analytics['trend_direction'] === 'stable') {
                $trendColor = 'bg-yellow-50 border border-yellow-200 text-yellow-800';
                $trendIcon = 'minus';
                $trendText = 'Stable Trend: Safety metrics remain consistent';
            }
            ?>
            <div class="<?= $trendColor ?> px-4 py-3 rounded-lg">
                <i class="fas fa-<?= $trendIcon ?> mr-2"></i>
                <strong><?= $trendText ?></strong>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-2">This Month</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= $analytics['incidents_this_month'] ?? 0 ?></h3>
                <p class="text-sm text-gray-600 mt-1">incidents</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-2">Previous Month</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= $analytics['incidents_previous_month'] ?? 0 ?></h3>
                <p class="text-sm text-gray-600 mt-1">incidents</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 mb-2">Avg Resolution</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= $analytics['average_resolution_days'] ?? 0 ?></h3>
                <p class="text-sm text-gray-600 mt-1">days</p>
            </div>
        </div>
    </div>

    <!-- Additional Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <p class="text-sm text-gray-600 mb-2">Safety Audits</p>
            <h3 class="text-3xl font-bold text-gray-900 mb-3"><?= $analytics['safety_audits_conducted'] ?? 0 ?></h3>
            <p class="text-sm text-gray-600">Conducted in period</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <p class="text-sm text-gray-600 mb-2">Total Injured</p>
            <h3 class="text-3xl font-bold text-red-600 mb-3"><?= $analytics['total_injured_people'] ?? 0 ?></h3>
            <p class="text-sm text-gray-600">People across incidents</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <p class="text-sm text-gray-600 mb-2">Average Resolution</p>
            <h3 class="text-3xl font-bold text-gray-900 mb-3"><?= $analytics['average_resolution_days'] ?? 0 ?></h3>
            <p class="text-sm text-gray-600">Days to resolve</p>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
