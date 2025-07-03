<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Repair History</h2>

        <!-- Flash Success Message -->
        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg shadow">
            {{ $page.props.flash.success }}
        </div>

        <div class="flex justify-end mb-6">
            <button @click="openCreateModal" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75">
                Add New Repair
            </button>
        </div>

        <!-- Repairs Table -->
        <div class="overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody v-if="repairs" class="bg-white divide-y divide-gray-200">
                <tr v-for="repair in repairs" :key="repair.id" class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(repair.repair_date) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ repair.issue || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ repair.repair_cost != null ? `$${repair.repair_cost}` : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ repair.status || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ repair.vendor || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ repair.remarks || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center space-x-3">
                        <button @click="openEditModal(repair)" class="text-blue-600 hover:text-blue-800 transition duration-150 ease-in-out focus:outline-none">
                            ✏️
                        </button>
                        <button @click="confirmDelete(repair.id)" class="text-red-600 hover:text-red-800 transition duration-150 ease-in-out focus:outline-none">
                            🗑️
                        </button>
                    </td>
                </tr>
                <tr v-if="repairs.length === 0">
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No repair history found for this asset.</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteConfirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-2xl p-6 w-full max-w-sm">
                <h3 class="text-lg font-bold text-gray-900">Confirm Deletion</h3>
                <p class="mt-2 text-sm text-gray-600">Are you sure you want to delete this repair record? This action cannot be undone.</p>
                <div class="mt-6 flex justify-end space-x-3">
                    <button @click="cancelDelete" class="px-4 py-2 bg-gray-200 text-gray-800 font-semibold rounded-md">Cancel</button>
                    <Link :href="route('repairs.destroy', { repairId: repairToDeleteId })" method="delete" as="button" class="px-4 py-2 bg-red-600 text-white font-semibold rounded-md" preserve-scroll @click="cancelDelete">
                        Delete
                    </Link>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
                <h3 class="text-xl font-semibold mb-4">{{ form.id ? 'Edit' : 'Add' }} Repair</h3>
                <form @submit.prevent="submitForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Date</label>
                            <input type="date" v-model="form.repair_date" class="mt-1 w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Status</label>
                            <input type="text" v-model="form.status" class="mt-1 w-full rounded border-gray-300">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium">Issue</label>
                            <textarea v-model="form.issue" class="mt-1 w-full rounded border-gray-300"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Cost</label>
                            <input type="number" v-model="form.repair_cost" class="mt-1 w-full rounded border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Vendor</label>
                            <input type="text" v-model="form.vendor" class="mt-1 w-full rounded border-gray-300">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium">Remarks</label>
                            <textarea v-model="form.remarks" class="mt-1 w-full rounded border-gray-300"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-4">
                        <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-200 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">{{ form.id ? 'Update' : 'Create' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    repairs: Array,
    errors: Object
});

const assetId = ref(null);
const showModal = ref(false);
const showDeleteConfirm = ref(false);
const repairToDeleteId = ref(null);

const form = useForm({
    id: null,
    repair_date: '',
    issue: '',
    repair_cost: null,
    status: '',
    vendor: '',
    remarks: ''
});

onMounted(() => {
    const match = window.location.pathname.match(/\/assets\/(\d+)/);
    assetId.value = match ? Number(match[1]) : null;
    console.log('Extracted asset ID:', assetId.value);
});


const formatDate = (dateString) => new Date(dateString).toLocaleDateString('en-GB');

const openCreateModal = () => {
    form.reset();
    showModal.value = true;
};

const openEditModal = (repair) => {
    Object.assign(form, repair);
    form.repair_date = repair.repair_date.slice(0, 10);
    form.clearErrors();
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const submitForm = () => {
    const options = {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    };
    if (form.id) {
        form.put(route('repairs.update', { repairId: form.id }), options);
    } else {
        form.post(route('repairs.store', { assetId: assetId.value }), options);
    }
};

const confirmDelete = (id) => {
    repairToDeleteId.value = id;
    showDeleteConfirm.value = true;
};

const cancelDelete = () => {
    showDeleteConfirm.value = false;
    repairToDeleteId.value = null;
};
</script>
