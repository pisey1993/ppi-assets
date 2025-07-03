<template>
    <div class="p-6 bg-gray-50 min-h-screen max-w-4xl mx-auto">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Repair History for Asset #{{ assetId }}</h2>

        <div class="flex justify-end mb-6">
            <button
                @click="openCreateModal"
                class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition"
            >
                Add New Repair
            </button>
        </div>

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
                        <button
                            @click="openEditModal(repair)"
                            class="text-blue-600 hover:text-blue-800 transition duration-150 ease-in-out focus:outline-none"
                        >
                            Edit
                        </button>
                        <button
                            @click="confirmDelete(repair.id)"
                            class="text-red-600 hover:text-red-800 transition duration-150 ease-in-out focus:outline-none"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
                <tr v-if="repairs.length === 0">
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No repair history found for this asset.</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div
                class="bg-white p-6 rounded-lg max-w-md w-full shadow-lg relative"
                @click.stop
            >
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">{{ form.id ? 'Edit Repair' : 'Add Repair' }}</h3>
                    <button @click="closeModal" class="text-gray-600 hover:text-gray-900">&times;</button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block mb-1 font-medium">Repair Date</label>
                        <input
                            type="date"
                            v-model="form.repair_date"
                            class="w-full border px-3 py-2 rounded"
                            required
                        />
                        <p v-if="errors.repair_date" class="text-red-600 text-sm mt-1">{{ errors.repair_date[0] }}</p>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Issue</label>
                        <input
                            type="text"
                            v-model="form.issue"
                            class="w-full border px-3 py-2 rounded"
                            maxlength="255"
                            placeholder="Issue description"
                        />
                        <p v-if="errors.issue" class="text-red-600 text-sm mt-1">{{ errors.issue[0] }}</p>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Cost</label>
                        <input
                            type="number"
                            step="0.01"
                            v-model.number="form.repair_cost"
                            class="w-full border px-3 py-2 rounded"
                            placeholder="e.g., 150.00"
                        />
                        <p v-if="errors.repair_cost" class="text-red-600 text-sm mt-1">{{ errors.repair_cost[0] }}</p>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Status</label>
                        <input
                            type="text"
                            v-model="form.status"
                            class="w-full border px-3 py-2 rounded"
                            maxlength="50"
                            placeholder="e.g., Completed, Pending"
                        />
                        <p v-if="errors.status" class="text-red-600 text-sm mt-1">{{ errors.status[0] }}</p>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Vendor</label>
                        <input
                            type="text"
                            v-model="form.vendor"
                            class="w-full border px-3 py-2 rounded"
                            maxlength="255"
                            placeholder="Vendor name"
                        />
                        <p v-if="errors.vendor" class="text-red-600 text-sm mt-1">{{ errors.vendor[0] }}</p>
                    </div>

                    <div>
                        <label class="block mb-1 font-medium">Remarks</label>
                        <textarea
                            v-model="form.remarks"
                            class="w-full border px-3 py-2 rounded"
                            rows="3"
                            placeholder="Additional remarks"
                        ></textarea>
                        <p v-if="errors.remarks" class="text-red-600 text-sm mt-1">{{ errors.remarks[0] }}</p>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-4 py-2 border rounded hover:bg-gray-100"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                        >
                            {{ form.id ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteConfirm"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
        >
            <div
                class="bg-white p-6 rounded-lg max-w-sm w-full text-center shadow-lg"
                @click.stop
            >
                <h3 class="text-xl font-semibold mb-4">Confirm Delete</h3>
                <p class="mb-6">Are you sure you want to delete this repair record?</p>
                <div class="flex justify-center space-x-4">
                    <button
                        @click="cancelDelete"
                        class="px-4 py-2 border rounded hover:bg-gray-100"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteRepair"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

// ** IMPORTANT **
// This assumes you receive the assetId from your Laravel blade or parent component.
// Example: <repairs :asset-id="{{ $asset->id }}" />
const props = defineProps({
    assetId: {
        type: Number,
        required: true,
    },
})

const repairs = ref([])
const showModal = ref(false)
const showDeleteConfirm = ref(false)
const deleteId = ref(null)

const form = ref({
    id: null,
    asset_id: props.assetId,
    repair_date: '',
    issue: '',
    repair_cost: null,
    status: '',
    vendor: '',
    remarks: '',
})

const errors = ref({})

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')

function formatDate(dateStr) {
    if (!dateStr) return ''
    const d = new Date(dateStr)
    return d.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

async function loadRepairs() {
    try {
        const response = await fetch(`/assets/${props.assetId}/repairs`, {
            headers: {
                'Accept': 'application/json',
            },
        })
        if (!response.ok) throw new Error('Failed to load repairs')
        repairs.value = await response.json()
    } catch (err) {
        console.error(err)
        alert('Failed to load repairs')
    }
}

function openCreateModal() {
    form.value = {
        id: null,
        asset_id: props.assetId,
        repair_date: '',
        issue: '',
        repair_cost: null,
        status: '',
        vendor: '',
        remarks: '',
    }
    errors.value = {}
    showModal.value = true
}

function openEditModal(repair) {
    form.value = { ...repair }
    errors.value = {}
    showModal.value = true
}

function closeModal() {
    showModal.value = false
    errors.value = {}
}

async function submitForm() {
    errors.value = {}
    try {
        let response

        if (form.value.id) {
            // Update
            response = await fetch(`/repairs/${form.value.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(form.value),
            })
        } else {
            // Create
            response = await fetch(`/assets/${props.assetId}/repairs`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(form.value),
            })
        }

        if (!response.ok) {
            if (response.status === 422) {
                const data = await response.json()
                errors.value = data.errors || {}
            } else {
                throw new Error('Failed to save repair')
            }
        } else {
            await loadRepairs()
            closeModal()
        }
    } catch (err) {
        console.error(err)
        alert(err.message || 'An error occurred')
    }
}

function confirmDelete(id) {
    deleteId.value = id
    showDeleteConfirm.value = true
}

function cancelDelete() {
    deleteId.value = null
    showDeleteConfirm.value = false
}

async function deleteRepair() {
    try {
        const response = await fetch(`/repairs/${deleteId.value}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
        if (!response.ok) throw new Error('Failed to delete repair')
        await loadRepairs()
        cancelDelete()
    } catch (err) {
        console.error(err)
        alert(err.message || 'Failed to delete repair')
    }
}

onMounted(() => {
    loadRepairs()
})
</script>

<style scoped>
/* Simple styling, adjust as needed */

.modal-overlay {
    background: rgba(0, 0, 0, 0.5);
}

</style>
