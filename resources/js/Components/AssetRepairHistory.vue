<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <h2 class="text-xl font-bold mb-6">Repairs for Asset #{{ assetId }}</h2>

        <!-- Add Repair Button -->
        <div class="flex justify-end mb-4">
            <button @click="openCreateModal"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                Add Repair
            </button>
        </div>

        <!-- Repair Table -->
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Issue</th>
                    <th class="px-4 py-2 text-left">Cost</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="repair in repairs" :key="repair.id" class="border-t hover:bg-gray-50">
                    <td class="px-4 py-2">{{ formatDate(repair.repair_date) }}</td>
                    <td class="px-4 py-2">{{ repair.issue || '-' }}</td>
                    <td class="px-4 py-2">{{ repair.repair_cost ?? '-' }}</td>
                    <td class="px-4 py-2">{{ repair.status || '-' }}</td>
                    <td class="px-4 py-2 text-center space-x-2">
                        <button @click="openEditModal(repair)" class="text-blue-600 hover:underline">Edit</button>
                        <button @click="confirmDelete(repair.id)" class="text-red-600 hover:underline">Delete</button>
                    </td>
                </tr>
                <tr v-if="repairs.length === 0">
                    <td colspan="5" class="text-center text-gray-500 py-4">No repairs found.</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal for Create/Edit -->
        <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-md w-full max-w-md shadow">
                <div class="flex justify-between mb-4">
                    <h3 class="text-lg font-semibold">
                        {{ form.id ? 'Edit Repair' : 'Add Repair' }}
                    </h3>
                    <button @click="closeModal" class="text-gray-600 hover:text-black">✕</button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label class="block text-sm mb-1">Date</label>
                        <input type="date" v-model="form.repair_date"
                               class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Issue</label>
                        <input type="text" v-model="form.issue" class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Cost</label>
                        <input type="number" step="0.01" v-model="form.repair_cost" class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Status</label>
                        <input type="text" v-model="form.status" class="w-full border rounded p-2">
                    </div>

                    <div class="flex justify-end space-x-2 pt-4">
                        <button type="button" @click="closeModal"
                                class="px-4 py-2 border rounded">Cancel</button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            {{ form.id ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation -->
        <div v-if="showDeleteConfirm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-6 rounded-md w-full max-w-sm text-center shadow">
                <p class="text-lg mb-4">Are you sure you want to delete this repair?</p>
                <div class="flex justify-center space-x-4">
                    <button @click="cancelDelete" class="px-4 py-2 border rounded">Cancel</button>
                    <button @click="deleteRepair" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

// ID from URL
const assetId = ref(null)
const repairs = ref([])

const form = ref({
    id: null,
    asset_id: null,
    repair_date: '',
    issue: '',
    repair_cost: null,
    status: ''
})

const showModal = ref(false)
const showDeleteConfirm = ref(false)
const deleteId = ref(null)

const formatDate = (dateStr) => {
    const d = new Date(dateStr)
    return d.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}

// Get ID from URL
onMounted(() => {
    const match = window.location.pathname.match(/assets\/(\d+)\/edit/)
    assetId.value = match ? match[1] : null
    form.value.asset_id = assetId.value
    if (assetId.value) loadRepairs()
})

// Load repair list
const loadRepairs = async () => {
    try {
        const res = await axios.get(`/assets/${assetId.value}/repairs`)
        repairs.value = res.data
    } catch (e) {
        console.error('Failed to load:', e)
    }
}

const openCreateModal = () => {
    form.value = {
        id: null,
        asset_id: assetId.value,
        repair_date: '',
        issue: '',
        repair_cost: null,
        status: ''
    }
    showModal.value = true
}

const openEditModal = (repair) => {
    form.value = { ...repair }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
}

const submitForm = async () => {
    try {
        if (form.value.id) {
            await axios.put(`/repairs/${form.value.id}`, form.value)
        } else {
            await axios.post('/repairs', form.value)
        }
        await loadRepairs()
        closeModal()
    } catch (err) {
        console.error('Save failed:', err)
    }
}

const confirmDelete = (id) => {
    deleteId.value = id
    showDeleteConfirm.value = true
}

const cancelDelete = () => {
    deleteId.value = null
    showDeleteConfirm.value = false
}

const deleteRepair = async () => {
    try {
        await axios.delete(`/repairs/${deleteId.value}`)
        await loadRepairs()
        cancelDelete()
    } catch (err) {
        console.error('Delete failed:', err)
    }
}
</script>
