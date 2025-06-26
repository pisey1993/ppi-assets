<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ElSelect, ElOption } from "element-plus";

// ─── 1. Initialize page BEFORE using it to avoid TDZ error ───
const page = usePage();

// Match all `/assets/{number}` in the URL
const matches = [...page.url.matchAll(/\/assets\/(\d+)/g)];

// Get the last match (if any)
const id = matches.length > 0 ? matches[matches.length - 1][1] : '';

console.log('Asset ID:', id);



// ─── 2. Define reactive states and form ───
const showModal = ref(false); // Controls visibility of the main transfer modal (create/edit)
const editing = ref(false); // Flag to determine if the modal is in 'edit' or 'create' mode

// State for the custom delete confirmation modal
const showDeleteConfirmModal = ref(false);
const transferToDelete = ref(null); // Stores the ID of the transfer to be deleted

// Define props expected from the parent component
const props = defineProps({
    transfers: Array, // Array of asset transfer records
    users: Array, // Array of user objects {id, name} for dropdowns
    locations: Array, // Array of location objects {id, name} for dropdowns
    asset: Object, // The current asset object (used for default asset_id)
});

// Initialize form data using Inertia's useForm hook
const form = useForm({
    id: null, // For editing existing transfer
    asset_id: '', // Associated asset ID
    from_user_id: null, // ID of the user transferring from
    to_user_id: null, // ID of the user transferring to
    from_location: null, // ID of the location transferring from
    to_location: null, // ID of the location transferring to
    transfer_date: '', // Date of the transfer
    approved_by: '', // User who approved the transfer (though not in form fields, useful for backend)
    reason: '', // Reason for the transfer
    user_status: '', // Status of the user after transfer
});

// ─── 3. Modal functions ───

// Opens the modal for creating a new transfer record
const openCreateModal = () => {
    form.reset(); // Reset form fields to initial state
    editing.value = false; // Set modal to create mode
    form.asset_id = id; // Default the asset_id to the current asset's ID from URL
    showModal.value = true; // Show the modal
};

// Opens the modal for editing an existing transfer record
const openEditModal = (transfer) => {
    // Populate form fields with data from the selected transfer
    form.id = transfer.id;
    // Use asset.id as a fallback if transfer.asset_id is not available
    form.asset_id = transfer.asset_id ?? props.asset?.id ?? '';
    form.from_user_id = transfer.from_user_id;
    form.to_user_id = transfer.to_user_id;
    form.from_location = transfer.from_location;
    form.to_location = transfer.to_location;
    form.transfer_date = transfer.transfer_date;
    form.approved_by = transfer.approved_by;
    form.reason = transfer.reason;
    form.user_status = transfer.user_status;

    editing.value = true; // Set modal to edit mode
    showModal.value = true; // Show the modal
};

// Function to handle showing the delete confirmation modal
const confirmDelete = (transferId) => {
    transferToDelete.value = transferId;
    showDeleteConfirmModal.value = true;
};

// ─── 4. Submit & delete handlers ───

// Submits the form for either creating or updating a transfer
const submitForm = () => {
    if (editing.value) {
        // If in edit mode, send a PUT request to update the transfer
        form.put(`/asset-transfers/${form.id}`, {
            onSuccess: () => (showModal.value = false), // Hide modal on success
            onError: (errors) => console.error('Update error:', errors)
        });
    } else {
        // If in create mode, send a POST request to store the new transfer
        form.post('/asset-transfers', {
            onSuccess: () => (showModal.value = false), // Hide modal on success
            onError: (errors) => console.error('Create error:', errors)
        });
    }
};

// Executes the delete operation after confirmation
const executeDelete = () => {
    if (transferToDelete.value) {
        form.delete(`/asset-transfers/${transferToDelete.value}`, {
            onSuccess: () => {
                showDeleteConfirmModal.value = false; // Hide confirmation modal on success
                transferToDelete.value = null; // Clear the ID
            },
            onError: (errors) => console.error('Delete error:', errors)
        });
    }
};

// Function to print a specific transfer record
const printTransfer = (transferId) => {
    // Opens a new tab with a specific print route (assuming Laravel route exists)
    window.open(`/asset-transfers/${transferId}/print`, '_blank');
};

</script>

<template>
    <div class="mb-4 text-right">
        <button @click="openCreateModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Add Transfer
        </button>
    </div>

    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead>
        <tr class="bg-gray-50">
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned User</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="t in transfers" :key="t.id">
            <td class="px-4 py-2 whitespace-nowrap">{{ new Date(t.transfer_date).toLocaleDateString() }}</td>
            <!-- Display user name based on to_user_id (or from_user_id if 'to' is null) -->
            <td class="px-4 py-2 whitespace-nowrap">
                {{ users.find(u => u.id === t.to_user_id)?.name || users.find(u => u.id === t.from_user_id)?.name || 'N/A' }}
            </td>
            <td class="px-4 py-2 whitespace-nowrap">{{ t.user_status }}</td>
            <td class="px-4 py-2 whitespace-nowrap">{{ t.reason || '-' }}</td>
            <td class="px-4 py-2 whitespace-nowrap space-x-2">
                <button @click="openEditModal(t)" class="text-indigo-600 hover:underline">Edit</button>
                <button @click="confirmDelete(t.id)" class="text-red-600 hover:underline">Delete</button>
                <button @click="printTransfer(t.id)" class="text-green-600 hover:underline">Print</button>
            </td>
        </tr>
        <tr v-if="transfers.length === 0">
            <td colspan="5" class="px-4 py-2 text-center text-gray-500">No transfer history found.</td>
        </tr>
        </tbody>
    </table>

    <!-- Main Transfer Modal (Create/Edit) -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-2xl relative">
            <h2 class="text-lg font-semibold mb-4">{{ editing ? 'Edit Transfer' : 'Create Transfer' }}</h2>
            <form @submit.prevent="submitForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Asset ID -->
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Asset ID</label>
                    <input v-model="form.asset_id" class="border p-2 rounded w-full bg-gray-100" readonly/>
                </div>
                <!-- From User -->
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">From User</label>
                    <el-select v-model="form.from_user_id" placeholder="-- Select User --" filterable clearable
                               class="w-full">
                        <template v-if="users && users.length > 0">
                            <el-option v-for="u in users" :key="u.id" :label="u.name" :value="u.id"/>
                        </template>
                        <template v-else>
                            <el-option disabled value="">No users available</el-option>
                        </template>
                    </el-select>
                </div>
                <!-- To User -->
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">To User</label>
                    <el-select v-model="form.to_user_id" placeholder="-- Select User --" filterable clearable
                               class="w-full">
                        <template v-if="users && users.length > 0">
                            <el-option v-for="u in users" :key="u.id" :label="u.name" :value="u.id"/>
                        </template>
                        <template v-else>
                            <el-option disabled value="">No users available</el-option>
                        </template>
                    </el-select>
                </div>
                <!-- From Location -->
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">From Location</label>
                    <el-select v-model="form.from_location" placeholder="-- Select Location --" filterable clearable
                               class="w-full">
                        <template v-if="locations && locations.length > 0">
                            <el-option v-for="loc in locations" :key="loc.id" :label="loc.name" :value="loc.id"/>
                        </template>
                        <template v-else>
                            <el-option disabled value="">No locations available</el-option>
                        </template>
                    </el-select>
                </div>
                <!-- To Location -->
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">To Location</label>
                    <el-select v-model="form.to_location" placeholder="-- Select Location --" filterable clearable
                               class="w-full">
                        <template v-if="locations && locations.length > 0">
                            <el-option v-for="loc in locations" :key="loc.id" :label="loc.name" :value="loc.id"/>
                        </template>
                        <template v-else>
                            <el-option disabled value="">No locations available</el-option>
                        </template>
                    </el-select>
                </div>
                <!-- Transfer Date -->
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">Transfer Date</label>
                    <input v-model="form.transfer_date" type="date" class="border p-2 rounded w-full"/>
                </div>
                <!-- User Status -->
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700">User Status</label>
                    <select v-model="form.user_status" class="border p-2 rounded w-full">
                        <option disabled value="">-- Select Status --</option>
                        <option value="available">Available</option>
                        <option value="assigned">Assigned</option>
                        <option value="repair">Repair</option>
                        <option value="retired">Retired</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block mb-1 text-sm font-medium text-gray-700">Reason</label>
                    <textarea v-model="form.reason" class="border p-2 rounded w-full"></textarea>
                </div>
                <div class="col-span-2 flex justify-end space-x-2 mt-4">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        {{ editing ? 'Update' : 'Submit' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Delete Confirmation Modal -->
    <div v-if="showDeleteConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white p-6 rounded shadow-lg max-w-sm w-full relative">
            <h3 class="text-lg font-semibold mb-4">Confirm Delete</h3>
            <p class="mb-6">Are you sure you want to delete this transfer record?</p>
            <div class="flex justify-end space-x-2">
                <button @click="showDeleteConfirmModal = false" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel</button>
                <button @click="executeDelete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>
</template>

<style>
/* Base styling for modals */
.fixed.inset-0 {
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Fade transition for modals */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
