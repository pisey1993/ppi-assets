<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Repair History</h2>

        <!-- Flash Success Message (Assuming this is handled by Inertia or a similar library) -->
        <div v-if="$page.props.flash?.success" class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
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
                <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="repair in repairs" :key="repair.id" class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(repair.repair_date) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ repair.issue || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ repair.repair_cost != null ? `$${repair.repair_cost}` : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ repair.status || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ repair.vendor || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ repair.remarks || '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center space-x-3">
                        <button @click="openEditModal(repair)" class="text-blue-600 hover:text-blue-800 transition duration-150 ease-in-out focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block align-middle" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.38-2.828-2.829z" />
                            </svg>
                            <span class="sr-only">Edit</span>
                        </button>
                        <button @click="confirmDelete(repair.id)" class="text-red-600 hover:text-red-800 transition duration-150 ease-in-out focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block align-middle" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span class="sr-only">Delete</span>
                        </button>
                    </td>
                </tr>
                <tr v-if="repairs.length === 0">
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No repair history found for this asset.</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Create/Edit Repair Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" @click.self="closeModal">
            <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-2xl transform transition-all">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">{{ form.id ? 'Edit' : 'Add New' }} Repair</h3>
                <form @submit.prevent="submitForm" novalidate>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Repair Date -->
                        <div>
                            <label for="repair_date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input v-model="form.repair_date" type="date" id="repair_date" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <p v-if="errors.repair_date" class="mt-1 text-xs text-red-600">{{ errors.repair_date[0] }}</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select v-model="form.status" id="status" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="">Select Status</option>
                                <option value="Pending">Pending</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                            <p v-if="errors.status" class="mt-1 text-xs text-red-600">{{ errors.status[0] }}</p>
                        </div>

                        <!-- Issue -->
                        <div class="md:col-span-2">
                            <label for="issue" class="block text-sm font-medium text-gray-700">Issue</label>
                            <textarea v-model="form.issue" id="issue" rows="3" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                            <p v-if="errors.issue" class="mt-1 text-xs text-red-600">{{ errors.issue[0] }}</p>
                        </div>

                        <!-- Repair Cost -->
                        <div>
                            <label for="repair_cost" class="block text-sm font-medium text-gray-700">Cost ($)</label>
                            <input v-model.number="form.repair_cost" type="number" step="0.01" id="repair_cost" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <p v-if="errors.repair_cost" class="mt-1 text-xs text-red-600">{{ errors.repair_cost[0] }}</p>
                        </div>

                        <!-- Vendor -->
                        <div>
                            <label for="vendor" class="block text-sm font-medium text-gray-700">Vendor</label>
                            <input v-model="form.vendor" type="text" id="vendor" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <p v-if="errors.vendor" class="mt-1 text-xs text-red-600">{{ errors.vendor[0] }}</p>
                        </div>

                        <!-- Remarks -->
                        <div class="md:col-span-2">
                            <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                            <textarea v-model="form.remarks" id="remarks" rows="2" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                            <p v-if="errors.remarks" class="mt-1 text-xs text-red-600">{{ errors.remarks[0] }}</p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" @click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-800 font-semibold rounded-lg hover:bg-gray-300 transition duration-150">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-150">
                            Save Repair
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteConfirm" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-2xl p-6 w-full max-w-sm">
                <h3 class="text-lg font-bold text-gray-900">Confirm Deletion</h3>
                <p class="mt-2 text-sm text-gray-600">Are you sure you want to delete this repair record? This action cannot be undone.</p>
                <div class="mt-6 flex justify-end space-x-3">
                    <button @click="cancelDelete" class="px-4 py-2 bg-gray-200 text-gray-800 font-semibold rounded-md hover:bg-gray-300 transition duration-150">
                        Cancel
                    </button>
                    <button @click="deleteRepair" class="px-4 py-2 bg-red-600 text-white font-semibold rounded-md shadow-sm hover:bg-red-700 transition duration-150">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    asset: Object,
    // If using Inertia, you might receive errors as a prop
    // errors: Object
});

const repairs = ref([]);

// A clean slate for the form object
const getCleanForm = () => ({
    id: null,
    asset_id: props.asset?.id,
    repair_date: new Date().toISOString().slice(0, 10), // Default to today
    issue: '',
    repair_cost: null,
    status: '',
    vendor: '',
    remarks: ''
});

const form = ref(getCleanForm());
const errors = ref({});
const showModal = ref(false);
const showDeleteConfirm = ref(false);
const deleteId = ref(null);

const formatDate = (dateString) => {
    if (!dateString) return '-';
    // Handles 'YYYY-MM-DD' and full ISO strings
    return new Date(dateString).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const loadRepairs = async () => {
    if (!props.asset?.id) return;
    try {
        const res = await fetch(`/assets/${props.asset.id}/repairs`);
        if (!res.ok) throw new Error('Failed to fetch repairs');
        repairs.value = await res.json();
    } catch (error) {
        console.error("Error loading repairs:", error);
        // Optionally, show an error message to the user
    }
};

const openCreateModal = () => {
    form.value = getCleanForm(); // Reset form to its initial state
    errors.value = {};
    showModal.value = true;
};

const openEditModal = (repair) => {
    // Ensure numeric values are numbers, not strings
    const repairCost = parseFloat(repair.repair_cost);
    form.value = {
        ...repair,
        repair_cost: isNaN(repairCost) ? null : repairCost,
        // Ensure date is in 'YYYY-MM-DD' format for the input
        repair_date: repair.repair_date.slice(0, 10)
    };
    errors.value = {};
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    errors.value = {}; // Clear errors when closing
};

const submitForm = async () => {
    errors.value = {};
    const url = form.value.id ? `/repairs/${form.value.id}` : `/repairs`;
    const method = form.value.id ? 'PUT' : 'POST';

    // Make sure asset_id is included
    const payload = { ...form.value, asset_id: props.asset.id };

    try {
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                // If using Laravel Sanctum/CSRF
                // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        });

        if (res.status === 422) { // Validation error
            const data = await res.json();
            errors.value = data.errors;
            return;
        }

        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }

        // On success
        await loadRepairs();
        closeModal();
        // You might want to manually trigger a flash message here if not using Inertia redirects
        // For example: $page.props.flash.success = 'Repair saved successfully!';

    } catch (error) {
        console.error("Error submitting form:", error);
        // Handle server/network errors (e.g., show a toast notification)
    }
};

const confirmDelete = (id) => {
    deleteId.value = id;
    showDeleteConfirm.value = true;
};

const cancelDelete = () => {
    deleteId.value = null;
    showDeleteConfirm.value = false;
};

const deleteRepair = async () => {
    if (!deleteId.value) return;

    try {
        const res = await fetch(`/repairs/${deleteId.value}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                // 'X-CSRF-TOKEN': ...
            }
        });

        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }

        await loadRepairs();
        cancelDelete(); // Hides modal and resets deleteId

    } catch (error) {
        console.error("Error deleting repair:", error);
    }
};

// Watch for the asset ID to change and load the corresponding repairs
watch(
    () => props.asset?.id,
    (newId) => {
        if (newId) {
            loadRepairs();
        } else {
            repairs.value = []; // Clear repairs if no asset is selected
        }
    },
    { immediate: true }
);
</script>
