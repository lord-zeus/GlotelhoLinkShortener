<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    original_url: '',
    custom_code: '',
    expires_at: ''
});

const submit = () => {
    // Use Inertia's form helper to maintain session authentication
    form.post(route('urls.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
        onError: (errors) => {
            console.error('Error creating URL:', errors);
        }
    });
};
</script>

<template>
    <Head title="Create Short URL" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create Short URL
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label for="original_url" class="block mb-2 font-medium text-gray-700">
                                    Original URL
                                </label>
                                <input
                                    id="original_url"
                                    v-model="form.original_url"
                                    type="url"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="https://example.com/very/long/url"
                                >
                                <p v-if="form.errors.original_url" class="mt-1 text-sm text-red-600">
                                    {{ form.errors.original_url }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                                <div>
                                    <label for="custom_code" class="block mb-2 font-medium text-gray-700">
                                        Custom Code (optional)
                                    </label>
                                    <input
                                        id="custom_code"
                                        v-model="form.custom_code"
                                        type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="my-custom-link"
                                    >
                                    <p v-if="form.errors.custom_code" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.custom_code }}
                                    </p>
                                </div>

                                <div>
                                    <label for="expires_at" class="block mb-2 font-medium text-gray-700">
                                        Expiration Date (optional)
                                    </label>
                                    <input
                                        id="expires_at"
                                        v-model="form.expires_at"
                                        type="date"
                                        :min="new Date().toISOString().split('T')[0]"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    >
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Create Short URL
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
