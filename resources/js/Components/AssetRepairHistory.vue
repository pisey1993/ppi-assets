<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Add Repair Button -->
        <div class="flex justify-end mb-6">
            <button @click="openCreateModal" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75">
                Add New Repair
            </button>
        </div>

        <!-- Repair List Table -->
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
                    <td class="px-6 py-4 whitespace-nowrap">{{ repair.repair_cost ?? '-' }}</td>
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

        <!-- Repair Form Modal -->
        <Transition name="modal-fade">
            <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
                <div class="modal-content">
                    <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-6">
                        <h3 class="text-2xl font-semibold text-gray-800">{{ form.id ? 'Edit Repair' : 'Add New Repair' }}</h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-4">
                        <div>
                            <label for="repair_date" class="block text-sm font-medium text-gray-700 mb-1">Repair Date</label>
                            <input id="repair_date" type="date" v-model="form.repair_date" required
                                   class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800" />
                            <p v-if="errors.repair_date" class="text-red-500 text-xs mt-1">{{ errors.repair_date[0] }}</p>
                        </div>
                        <div>
                            <label for="issue" class="block text-sm font-medium text-gray-700 mb-1">Issue</label>
                            <input id="issue" type="text" v-model="form.issue" maxlength="255" placeholder="e.g., Engine malfunction"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800" />
                            <p v-if="errors.issue" class="text-red-500 text-xs mt-1">{{ errors.issue[0] }}</p>
                        </div>
                        <div>
                            <label for="repair_cost" class="block text-sm font-medium text-gray-700 mb-1">Cost ($)</label>
                            <input id="repair_cost" type="number" step="0.01" v-model.number="form.repair_cost" placeholder="e.g., 150.00"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800" />
                            <p v-if="errors.repair_cost" class="text-red-500 text-xs mt-1">{{ errors.repair_cost[0] }}</p>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <input id="status" type="text" v-model="form.status" maxlength="50" placeholder="e.g., Completed, Pending"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800" />
                            <p v-if="errors.status" class="text-red-500 text-xs mt-1">{{ errors.status[0] }}</p>
                        </div>
                        <div>
                            <label for="vendor" class="block text-sm font-medium text-gray-700 mb-1">Vendor</label>
                            <input id="vendor" type="text" v-model="form.vendor" maxlength="255" placeholder="e.g., ABC Auto Repair"
                                   class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800" />
                            <p v-if="errors.vendor" class="text-red-500 text-xs mt-1">{{ errors.vendor[0] }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                            <textarea id="remarks" v-model="form.remarks" rows="3" placeholder="Any additional notes..."
                                      class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500 text-gray-800"></textarea>
                            <p v-if="errors.remarks" class="text-red-500 text-xs mt-1">{{ errors.remarks[0] }}</p>
                        </div>

                        <div class="md:col-span-2 flex justify-end space-x-3 mt-6">
                            <button type="button" @click="closeModal" class="px-5 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-300">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75">
                                {{ form.id ? 'Update Repair' : 'Add Repair' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Transition>

        <!-- Delete Confirmation Modal -->
        <Transition name="modal-fade">
            <div v-if="showDeleteConfirm" class="modal-overlay" @click.self="cancelDelete">
                <div class="modal-content text-center max-w-sm">
                    <div class="flex justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Confirm Deletion</h3>
                    <p class="text-gray-600 mb-6">Are you sure you want to delete this repair record? This action cannot be undone.</p>

                    <div class="flex justify-center space-x-4">
                        <button @click="cancelDelete" class="px-5 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button @click="deleteRepair" class="px-5 py-2 bg-red-600 text-white rounded-md shadow-sm hover:bg-red-700 transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-75">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'

// Props: asset object with id
const props = defineProps({
    asset: {
        type: Object,
        required: true
    }
})

// Repair list
const repairs = ref([])

// Form state for add/edit modal
const form = ref({
    id: null,
    asset_id: props.asset.id,
    repair_date: '',
    issue: '',
    repair_cost: null,
    status: '',
    vendor: '',
    remarks: ''
})

// Validation errors object
const errors = ref({})

// Modal visibility states
const showModal = ref(false)
const showDeleteConfirm = ref(false)
const deleteId = ref(null) // Stores the ID of the repair to be deleted

// Formats a date string to DD/MM/YYYY for display
const formatDate = (dateStr) => {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    // Using toLocaleDateString with 'en-GB' for DD/MM/YYYY format
    return d.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

// Loads repair history from the backend API
const loadRepairs = async () => {
    if (!props.asset.id) {
        console.warn('Asset ID is not available to load repairs.');
        repairs.value = []; // Clear repairs if asset ID is missing
        return;
    }
    try {
        const res = await axios.get(`/assets/${props.asset.id}/repairs`)
        repairs.value = res.data
    } catch (error) {
        console.error('Failed to load repairs:', error)
        // Optionally, display a user-friendly error message
    }
}

// Opens the modal for creating a new repair record
const openCreateModal = () => {
    // Reset form fields to their initial empty state
    form.value = {
        id: null,
        asset_id: props.asset.id,
        repair_date: '',
        issue: '',
        repair_cost: null,
        status: '',
        vendor: '',
        remarks: ''
    }
    errors.value = {} // Clear any previous validation errors
    showModal.value = true // Show the repair form modal
}

// Opens the modal for editing an existing repair record
const openEditModal = (repair) => {
    // Populate form fields with the data of the selected repair
    form.value = {
        id: repair.id,
        asset_id: repair.asset_id,
        repair_date: repair.repair_date,
        issue: repair.issue,
        repair_cost: repair.repair_cost,
        status: repair.status,
        vendor: repair.vendor,
        remarks: repair.remarks
    }
    errors.value = {} // Clear any previous validation errors
    showModal.value = true // Show the repair form modal
}

// Closes the repair form modal
const closeModal = () => {
    showModal.value = false
    errors.value = {} // Clear errors when closing
}

// Submits the repair form (either for creation or update)
const submitForm = async () => {
    errors.value = {} // Clear errors before new submission

    try {
        if (form.value.id) {
            // If form.id exists, it's an update operation
            await axios.put(`/repairs/${form.value.id}`, form.value)
        } else {
            // Otherwise, it's a create operation
            await axios.post('/repairs', form.value)
        }
        await loadRepairs() // Reload the list to show changes
        closeModal() // Close the modal on success
    } catch (error) {
        // Handle validation errors (status 422 from Laravel)
        if (error.response && error.response.status === 422) {
            errors.value = error.response.data.errors
        } else {
            // Handle other unexpected errors
            console.error('An unexpected error occurred during form submission:', error)
            // Provide a user-friendly message for unexpected errors
            alert('An unexpected error occurred. Please try again.')
        }
    }
}

// Opens the delete confirmation modal
const confirmDelete = (id) => {
    deleteId.value = id // Store the ID of the repair to be deleted
    showDeleteConfirm.value = true // Show the delete confirmation modal
}

// Cancels the delete operation and closes the confirmation modal
const cancelDelete = () => {
    deleteId.value = null // Clear the stored ID
    showDeleteConfirm.value = false // Hide the delete confirmation modal
}

// Executes the delete request after confirmation
const deleteRepair = async () => {
    try {
        await axios.delete(`/repairs/${deleteId.value}`) // Send DELETE request
        await loadRepairs() // Reload the list after successful deletion
        cancelDelete() // Close the confirmation modal
    } catch (error) {
        console.error('Failed to delete repair:', error)
        // Provide a user-friendly message for deletion failure
        alert('Failed to delete repair. Please try again.')
    }
}

// Watch for changes in the asset ID prop and reload repairs accordingly
watch(
    () => props.asset.id,
    (newId) => {
        if (newId) {
            loadRepairs()
        } else {
            repairs.value = []; // Clear repairs if asset ID becomes null
        }
    },
    { immediate: true } // Run the watcher immediately on component mount
)
</script>

<style scoped>
/* Base styling for the modal overlay */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6); /* Darker overlay for better focus */
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
    overflow-y: auto; /* Allow scrolling for smaller screens */
}

/* Base styling for the modal content */
.modal-content {
    background: #fff;
    padding: 2.5rem; /* Increased padding for more breathing room */
    border-radius: 12px; /* More rounded corners */
    width: 100%;
    max-width: 550px; /* Slightly wider modal for better form layout */
    box-shadow: 0 10px 30px rgba(0,0,0,0.2); /* Softer, larger shadow */
    position: relative;
    transform: translateY(0); /* Ensure no initial transform */
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); /* Smoother transition */
}

/* Modal transition styles */
.modal-fade-enter-active, .modal-fade-leave-active {
    transition: opacity 0.3s ease;
}
.modal-fade-enter-from, .modal-fade-leave-to {
    opacity: 0;
}

/* Specific content transition for the modal pop-up effect */
.modal-fade-enter-active .modal-content,
.modal-fade-leave-active .modal-content {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
}

.modal-fade-enter-from .modal-content,
.modal-fade-leave-to .modal-content {
    transform: translateY(20px) scale(0.95); /* Slight slide up and scale down effect */
    opacity: 0;
}

/* Responsive adjustments for table on small screens */
@media (max-width: 768px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }

    thead tr {
        position: absolute;
        top: -9999px;
        left: -9999px;
    }

    tbody tr {
        border: 1px solid #e5e7eb;
        margin-bottom: 1rem;
        border-radius: 8px;
        overflow: hidden; /* Ensures rounded corners apply to content */
    }

    tbody td {
        border: none;
        position: relative;
        padding-left: 50%;
        text-align: right;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 48px; /* Ensure consistent height for rows */
    }

    tbody td:before {
        content: attr(data-label);
        position: absolute;
        left: 1rem;
        width: 45%; /* Adjust width for label */
        text-align: left;
        font-weight: 600;
        color: #4b5563; /* Gray-700 */
    }

    /* Apply data-label to each td for responsive table headers */
    td:nth-of-type(1):before { content: "Date"; }
    td:nth-of-type(2):before { content: "Issue"; }
    td:nth-of-type(3):before { content: "Cost"; }
    td:nth-of-type(4):before { content: "Status"; }
    td:nth-of-type(5):before { content: "Vendor"; }
    td:nth-of-type(6):before { content: "Remarks"; }
    td:nth-of-type(7):before { content: "Actions"; }

    /* Specific styling for actions column on mobile */
    td:nth-of-type(7) {
        justify-content: center; /* Center action buttons */
        padding-left: 1rem; /* Remove extra padding from label */
    }
}
</style>
