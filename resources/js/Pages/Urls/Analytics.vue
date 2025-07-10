<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, ref, onMounted } from 'vue';

const analytics = ref({
    total_urls: 0,
    total_clicks: 0,
    top_urls: [],
    clicks_last_7_days: []
});

const loading = ref(true);

const fetchAnalytics = async () => {
    try {
        const response = await axios.get('/api/analytics');
        analytics.value = response.data;
    } catch (error) {
        alert('Error loading analytics data');
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchAnalytics();
});
</script>

<template>
    <Head title="Analytics" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Analytics
            </h2>
        </template>

        <div class="py-12">
            <div class="container px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:space-x-8">
                    <!-- Sidebar -->
                    <div class="w-full mb-6 md:w-1/4 md:mb-0">
                        <div class="p-6 bg-white rounded-lg shadow">
                            <h2 class="mb-4 text-xl font-bold">Shortener</h2>
                            <ul class="space-y-2">
                                <li>
                                    <a href="/dashboard" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                        My URLs
                                    </a>
                                </li>
                                <li>
                                    <a href="/create" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                        Create New
                                    </a>
                                </li>
                                <li>
                                    <a href="/analytics" class="flex items-center px-4 py-2 text-indigo-600 bg-indigo-100 rounded-lg">
                                        Analytics
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="w-full md:w-3/4">
                        <div v-if="loading" class="p-6 text-center bg-white rounded-lg shadow">
                            Loading analytics...
                        </div>

                        <div v-else class="space-y-6">
                            <!-- Summary Cards -->
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="p-6 bg-white rounded-lg shadow">
                                    <h3 class="text-lg font-medium text-gray-900">Total URLs</h3>
                                    <p class="mt-2 text-3xl font-bold">{{ analytics.total_urls }}</p>
                                </div>
                                <div class="p-6 bg-white rounded-lg shadow">
                                    <h3 class="text-lg font-medium text-gray-900">Total Clicks</h3>
                                    <p class="mt-2 text-3xl font-bold">{{ analytics.total_clicks }}</p>
                                </div>
                            </div>

                            <!-- Top URLs -->
                            <div class="p-6 bg-white rounded-lg shadow">
                                <h3 class="mb-4 text-lg font-medium text-gray-900">Most Popular URLs</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Short URL</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clicks</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="url in analytics.top_urls" :key="url.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a :href="`/${url.short_code}`" target="_blank" class="text-indigo-600 hover:underline">
                                                    {{ `${window.location.origin}/${url.short_code}` }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ url.clicks }}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Clicks Chart -->
                            <div class="p-6 bg-white rounded-lg shadow">
                                <h3 class="mb-4 text-lg font-medium text-gray-900">Clicks Last 7 Days</h3>
                                <div class="h-64">
                                    <!-- Simple text-based chart since we're avoiding external libraries -->
                                    <div v-for="day in analytics.clicks_last_7_days" :key="day.date" class="mb-2">
                                        <div class="flex items-center">
                                            <span class="w-24 text-sm text-gray-600">{{ day.date }}</span>
                                            <div class="flex-1 ml-2">
                                                <div
                                                    class="h-6 bg-indigo-600 rounded"
                                                    :style="{ width: `${(day.clicks / Math.max(...analytics.clicks_last_7_days.map(d => d.clicks)) * 100}%` }"
                                                ></div>
                                            </div>
                                            <span class="ml-2 text-sm font-medium">{{ day.clicks }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
