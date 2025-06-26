<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    assets: Object,
    filters: Object,
    categories: Array,
    users: Array,
    appUrl: String, // ←✅ Receive appUrl here
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(route('assets.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
});

const deleteAsset = (id) => {
    if (confirm('Are you sure you want to delete this asset?')) {
        router.delete(route('assets.destroy', id));
    }
};
</script>

<template>
    <Head title="Assets Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between flex-wrap gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Assets Management
                </h2>
                <a
                    :href="route('assets.create')"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded"
                >
                    + Create New
                </a>
            </div>
        </template>

        <div class="py-6">
            <div class="w-full px-2 lg:px-6 xl:px-12 2xl:px-24">
                <!-- Search -->
                <div class="mb-4">
                    <input
                        type="text"
                        v-model="search"
                        placeholder="Search by name or asset code..."
                        class="w-full md:w-1/3 border border-gray-300 rounded px-4 py-2 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>

                <!-- Table -->
                <div class="overflow-x-auto bg-white shadow rounded-xl">
                    <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2">#</th>
                            <th class="px-4 py-2">Asset Code</th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Category</th>
                            <th class="px-4 py-2">Assigned To</th>
                            <th class="px-4 py-2">Notes</th>
                            <th class="px-4 py-2">Location Remark</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                        <tr
                            v-for="(asset, index) in assets.data"
                            :key="asset.id"
                            class="hover:bg-gray-50 transition duration-150"
                        >
                            <td class="px-4 py-2 text-gray-700">{{ index + 1 }}</td>
                            <td class="px-4 py-2">{{ asset.asset_code }}</td>
                            <td class="px-4 py-2">{{ asset.name }}</td>
                            <td class="px-4 py-2">{{ asset.category?.category_name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ asset.assigned_user?.name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ asset.notes }}</td>
                            <td class="px-4 py-2">{{ asset.remark }}</td>
                            <td class="px-4 py-2">{{ asset.status }}</td>
                            <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                                <a
                                    :href="`${appUrl}/assets/${asset.id}`"
                                    class="text-green-600 hover:text-green-800 text-sm"
                                >
                                    View
                                </a>
                                <a
                                    :href="`${appUrl}/assets/${asset.id}/edit`"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm"
                                >
                                    Edit
                                </a>
                                <button
                                    @click="deleteAsset(asset.id)"
                                    class="text-red-600 hover:text-red-800 text-sm"
                                >
                                    Delete
                                </button>
                            </td>

                        </tr>
                        <tr v-if="assets.data.length === 0">
                            <td colspan="8" class="text-center py-6 text-gray-500">
                                No assets found.
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 flex justify-end space-x-2">
                    <button
                        v-for="link in assets.links"
                        :key="link.label"
                        v-html="link.label"
                        @click="() => link.url && router.get(link.url, {}, { preserveState: true })"
                        class="px-3 py-1 rounded border text-sm"
                        :class="{
                            'bg-blue-600 text-white': link.active,
                            'bg-white text-gray-700 hover:bg-gray-100': !link.active,
                            'cursor-not-allowed opacity-50': !link.url
                        }"
                        :disabled="!link.url"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
