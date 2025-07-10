<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    urls: Object,
});

const search = ref('');
const selectedUrl = ref(null);

// Initialize with props data
const pagination = ref({
    current_page: props.urls.current_page,
    last_page: props.urls.last_page,
    from: props.urls.from,
    to: props.urls.to,
    total: props.urls.total,
});

// Watch for page changes
watch(() => props.urls, (newUrls) => {
    pagination.value = {
        current_page: newUrls.current_page,
        last_page: newUrls.last_page,
        from: newUrls.from,
        to: newUrls.to,
        total: newUrls.total,
    };
});

const filteredUrls = computed(() => {
    if (!search.value) return props.urls.data;
    const query = search.value.toLowerCase();
    return props.urls.data.filter(url =>
        url.original_url.toLowerCase().includes(query) ||
        url.short_code.toLowerCase().includes(query)
    );
});

const getShortUrl = (code) => {
    return `${window.location.origin}/${code}`;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
};

const showStats = (url) => {
    const stats = `
    Short URL: ${getShortUrl(url.short_code)}
    Original URL: ${url.original_url}
    Clicks: ${url.count}
    Created: ${formatDate(url.created_at)}
    ${url.expires_at ? `Expires: ${formatDate(url.expires_at)}` : ''}
  `;
    alert('URL Statistics:\n\n' + stats);
};

const copyUrl = (url) => {
    navigator.clipboard.writeText(getShortUrl(url.short_code));
    alert('Copied to clipboard: ' + getShortUrl(url.short_code));
};

const deleteUrl = async (url) => {
    if (!confirm('Are you sure you want to delete this URL?')) return;

    try {
        await router.delete(`/urls/${url.id}`);
        alert('URL deleted successfully');
    } catch (error) {
        alert('Failed to delete URL: ' + error.message);
    }
};

const nextPage = () => {
    if (pagination.value.current_page < pagination.value.last_page) {
        router.get('/dashboard', { page: pagination.value.current_page + 1 }, {
            preserveState: true,
            replace: true,
        });
    }
};

const prevPage = () => {
    if (pagination.value.current_page > 1) {
        router.get('/dashboard', { page: pagination.value.current_page - 1 }, {
            preserveState: true,
            replace: true,
        });
    }
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                URL Shortener Dashboard
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
                                    <a href="/dashboard" class="flex items-center px-4 py-2 text-indigo-600 bg-indigo-100 rounded-lg">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                        </svg>
                                        My URLs
                                    </a>
                                </li>
                                <li>
                                    <a href="/create" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Create New
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="w-full md:w-3/4">
                        <div class="p-6 bg-white rounded-lg shadow">
                            <div class="flex items-center justify-between mb-6">
                                <h1 class="text-2xl font-bold">My Short URLs</h1>
                                <a
                                    href="/create"
                                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700"
                                >
                                    + New URL
                                </a>
                            </div>

                            <div class="mb-4">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search URLs..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md"
                                >
                            </div>

                            <div v-if="loading" class="text-center">
                                Loading URLs...
                            </div>

                            <div v-else>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Short URL</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Original URL</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clicks</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="url in filteredUrls" :key="url.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a
                                                    :href="getShortUrl(url.short_code)"
                                                    target="_blank"
                                                    class="text-indigo-600 hover:underline"
                                                >
                                                    {{ getShortUrl(url.short_code) }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 max-w-xs truncate" :title="url.original_url">
                                                {{ url.original_url }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ url.count }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ formatDate(url.created_at) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a
                                                    :href="'/urls/' + url.id + '/analytics'"
                                                    class="mr-3 text-indigo-600 hover:text-indigo-900"
                                                >
                                                    Stats
                                                </a>
                                                <button
                                                    @click="copyUrl(url)"
                                                    class="mr-3 text-indigo-600 hover:text-indigo-900"
                                                >
                                                    Copy
                                                </button>
                                                <button
                                                    @click="deleteUrl(url)"
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="flex items-center justify-between mt-4">
                                    <div class="text-sm text-gray-700">
                                        Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
                                    </div>
                                    <div class="flex space-x-2">
                                        <button
                                            @click="prevPage"
                                            :disabled="pagination.current_page === 1"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50"
                                        >
                                            Previous
                                        </button>
                                        <button
                                            @click="nextPage"
                                            :disabled="pagination.current_page === pagination.last_page"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50"
                                        >
                                            Next
                                        </button>
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
