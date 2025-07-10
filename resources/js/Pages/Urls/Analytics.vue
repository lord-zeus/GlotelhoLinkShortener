<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    id: String, // Get ID from Inertia props instead of route
});

const analytics = ref(null);
const loading = ref(true);
const error = ref(null);


const getShortUrl = (code) => {
    return `${window.location.origin}/${code}`;
};
const fetchAnalytics = async () => {
    try {
        loading.value = true;
        error.value = null;
        const response = await axios.get(`/api/urls/${props.id}/analytics`);
        analytics.value = response.data;
    } catch (err) {
        console.error('Error loading analytics:', err);
        error.value = 'Failed to load analytics data. Please try again.';
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchAnalytics();
});

const maxClicks = (data) => {
    return Math.max(...data.map(item => item.clicks), 1);
};

// Format numbers with commas
const formatNumber = (num) => {
    return num?.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") || '0';
};
</script>

<template>
    <Head title="URL Analytics" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    URL Analytics
                </h2>
                <button
                    @click="fetchAnalytics"
                    class="px-3 py-1 text-sm text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100"
                    :disabled="loading"
                >
                    {{ loading ? 'Refreshing...' : 'Refresh Data' }}
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="container px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Loading State -->
                <div v-if="loading && !analytics" class="flex flex-col items-center justify-center p-8 space-y-4 text-center">
                    <svg class="w-10 h-10 text-indigo-400 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-600">Loading analytics data...</p>
                </div>

                <!-- Error State -->
                <div v-if="error" class="p-4 mb-6 bg-red-50 rounded-md">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ error }}</h3>
                        </div>
                        <div class="ml-auto pl-3">
                            <button @click="fetchAnalytics" class="text-sm font-medium text-red-700 hover:text-red-600">
                                Retry
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Data Display -->
                <div v-if="analytics" class="space-y-8">
                    <!-- URL Info Card -->
                    <div class="p-6 bg-white rounded-lg shadow">
                        <h3 class="text-lg font-medium text-gray-900">URL Information</h3>
                        <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <p class="text-sm text-gray-500">Short URL</p>
                                <a
                                    :href="getShortUrl(analytics.url.short_code)"
                                    target="_blank"
                                    class="text-indigo-600 hover:underline"
                                >
                                    {{ getShortUrl(analytics.url.short_code) }}
                                </a>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Original URL</p>
                                <a
                                    :href="analytics.url.original_url"
                                    target="_blank"
                                    class="block mt-1 font-medium text-indigo-600 hover:text-indigo-500 hover:underline truncate"
                                    :title="analytics.url.original_url"
                                >
                                    {{ analytics.url.original_url }}
                                </a>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Created</p>
                                <p class="mt-1 font-medium">{{ analytics.url.created_at }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Clicks</p>
                                <p class="mt-1 text-2xl font-bold text-indigo-600">{{ formatNumber(analytics.url.total_clicks) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Clicks Over Time -->
                    <div class="p-6 bg-white rounded-lg shadow">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Clicks Last 30 Days</h3>
                        <div class="space-y-3">
                            <div v-for="day in analytics.clicks_by_day" :key="day.date" class="flex items-center">
                                <span class="w-20 text-sm text-gray-600">{{ day.date }}</span>
                                <div class="flex-1 mx-4">
                                    <div class="relative h-6 bg-indigo-100 rounded-full">
                                        <div
                                            class="absolute top-0 left-0 h-full bg-indigo-600 rounded-full"
                                            :style="{ width: `${(day.clicks / maxClicks(analytics.clicks_by_day)) * 100}%` }"
                                        ></div>
                                    </div>
                                </div>
                                <span class="w-8 text-sm font-medium text-right">{{ formatNumber(day.clicks) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <!-- Top Referrers -->
                        <div class="p-6 bg-white rounded-lg shadow">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Top Referrers</h3>
                            <div class="space-y-3">
                                <div v-for="referrer in analytics.top_referrers" :key="referrer.referrer" class="flex items-center">
                                    <span class="flex-1 text-sm truncate" :title="referrer.referrer">
                                        {{ referrer.referrer || 'Direct' }}
                                    </span>
                                    <span class="ml-4 font-medium">{{ formatNumber(referrer.count) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Devices -->
                        <div class="p-6 bg-white rounded-lg shadow">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Devices</h3>
                            <div class="space-y-3">
                                <div v-for="device in analytics.devices" :key="device.device" class="flex items-center">
                                    <span class="flex-1 capitalize">{{ device.device.toLowerCase() }}</span>
                                    <span class="ml-4 font-medium">{{ formatNumber(device.count) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Browsers -->
                        <div class="p-6 bg-white rounded-lg shadow">
                            <h3 class="mb-4 text-lg font-medium text-gray-900">Browsers</h3>
                            <div class="space-y-3">
                                <div v-for="browser in analytics.browsers" :key="browser.browser" class="flex items-center">
                                    <span class="flex-1 capitalize">{{ browser.browser.toLowerCase() }}</span>
                                    <span class="ml-4 font-medium">{{ formatNumber(browser.count) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Stats -->
                    <div v-if="analytics.top_countries" class="p-6 bg-white rounded-lg shadow">
                        <h3 class="mb-4 text-lg font-medium text-gray-900">Top Countries</h3>
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-5">
                            <div v-for="country in analytics.top_countries" :key="country.country" class="text-center">
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-sm font-medium text-gray-900">{{ country.country }}</p>
                                    <p class="mt-1 text-indigo-600">{{ formatNumber(country.count) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
