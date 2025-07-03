<template>
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Repair History</h3>

        <div class="flex justify-end">
            <el-button type="primary" @click="openCreateModal">Add Repair</el-button>
        </div>

        <!-- Repair Modal -->
        <el-dialog
            v-model="showRepairModal"
            :title="form.id ? 'Edit Repair' : 'Add Repair'"
            width="600px"
            @close="resetForm"
        >
            <form @submit.prevent="submitForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <el-date-picker
                    v-model="form.repair_date"
                    type="date"
                    placeholder="Repair Date"
                    style="width: 100%"
                    :disabled-date="disabledFutureDates"
                />
                <el-input v-model="form.issue" placeholder="Issue" />
                <el-input v-model.number="form.repair_cost" type="number" placeholder="Cost" />
                <el-input v-model="form.status" placeholder="Status" />
                <el-input v-model="form.vendor" placeholder="Vendor" />
                <el-input
                    v-model="form.remarks"
                    type="textarea"
                    placeholder="Remarks"
                    :rows="2"
                    class="md:col-span-2"
                />

                <div class="md:col-span-2 flex justify-end mt-2 space-x-2">
                    <el-button @click="showRepairModal = false">Cancel</el-button>
                    <el-button type="primary" native-type="submit">{{ form.id ? 'Update' : 'Add' }}</el-button>
                </div>
            </form>
        </el-dialog>

        <!-- Delete Confirmation Modal -->
        <el-dialog v-model="showDeleteModal" title="Confirm Delete" width="400px">
            <p>Are you sure you want to delete this repair?</p>
            <template #footer>
                <el-button @click="showDeleteModal = false">Cancel</el-button>
                <el-button type="danger" @click="executeDelete">Delete</el-button>
            </template>
        </el-dialog>

        <!-- Repair History Table -->
        <table class="w-full border text-sm text-left">
            <thead class="bg-gray-200">
            <tr>
                <th class="p-2">Date</th>
                <th class="p-2">Issue</th>
                <th class="p-2">Cost</th>
                <th class="p-2">Status</th>
                <th class="p-2">Vendor</th>
                <th class="p-2">Remarks</th>
                <th class="p-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="r in repairs" :key="r.id" class="border-t">
                <td class="p-2">{{ formatDate(r.repair_date) }}</td>
                <td class="p-2">{{ r.issue }}</td>
                <td class="p-2">{{ formatCurrency(r.repair_cost) }}</td>
                <td class="p-2">{{ r.status }}</td>
                <td class="p-2">{{ r.vendor }}</td>
                <td class="p-2">{{ r.remarks }}</td>
                <td class="p-2 space-x-2">
                    <el-button type="text" size="small" @click="editRepair(r)">Edit</el-button>
                    <el-button
                        type="text"
                        size="small"
                        class="text-red-600"
                        @click="confirmDelete(r.id)"
                    >Delete</el-button>
                </td>
            </tr>
            <tr v-if="repairs.length === 0">
                <td colspan="7" class="p-2 text-center text-gray-500">No repair history found.</td>
            </tr>
            </tbody>
        </table>

        <!-- Success message -->
        <el-alert
            v-if="successMessage"
            title="Success"
            type="success"
            :closable="true"
            @close="successMessage = ''"
            class="mt-4"
            show-icon
        >
            {{ successMessage }}
        </el-alert>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'
import dayjs from 'dayjs'

const props = defineProps({
    asset: {
        type: Object,
        required: true
    }
})

const repairs = ref([])
const showRepairModal = ref(false)
const showDeleteModal = ref(false)
const deleteId = ref(null)
const successMessage = ref('')

const form = useForm({
    id: null,
    asset_id: props.asset.id,
    repair_date: '',
    issue: '',
    repair_cost: '',
    status: '',
    vendor: '',
    remarks: ''
})

// Load repair history
const loadRepairs = async () => {
    if (!props.asset?.id) return
    try {
        const res = await axios.get(`/assets/${props.asset.id}/repairs`)
        repairs.value = res.data
    } catch (err) {
        console.error('Failed to load repairs:', err)
    }
}

// Open create modal
const openCreateModal = () => {
    resetForm()
    form.asset_id = props.asset.id
    showRepairModal.value = true
}

// Open edit modal
const editRepair = (repair) => {
    form.reset()
    form.id = repair.id
    form.asset_id = repair.asset_id
    form.repair_date = repair.repair_date
    form.issue = repair.issue
    form.repair_cost = repair.repair_cost
    form.status = repair.status
    form.vendor = repair.vendor
    form.remarks = repair.remarks
    showRepairModal.value = true
}

// Submit form (create or update)
const submitForm = () => {
    const payload = {
        ...form.data(),
        repair_date: dayjs(form.repair_date).format('YYYY-MM-DD')
    }

    const method = form.id ? 'put' : 'post'
    const url = form.id ? `/repairs/${form.id}` : '/repairs'

    form.clearErrors()
    form[method](url, {
        data: payload,
        preserveScroll: true,
        onSuccess: () => {
            successMessage.value = form.id ? 'Repair updated successfully!' : 'Repair added successfully!'
            loadRepairs()
            // Keep the modal open after save (per your request)
        },
        onError: (err) => {
            console.error('Validation failed:', err)
        }
    })
}

// Confirm delete
const confirmDelete = (id) => {
    deleteId.value = id
    showDeleteModal.value = true
}

// Execute delete
const executeDelete = () => {
    form.delete(`/repairs/${deleteId.value}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false
            deleteId.value = null
            successMessage.value = 'Repair deleted successfully!'
            loadRepairs()
        }
    })
}

// Reset form fields
const resetForm = () => {
    form.id = null
    form.asset_id = props.asset.id
    form.repair_date = ''
    form.issue = ''
    form.repair_cost = ''
    form.status = ''
    form.vendor = ''
    form.remarks = ''
    successMessage.value = ''
}

// Format date (e.g. YYYY-MM-DD to localized)
const formatDate = (date) => {
    return date ? dayjs(date).format('DD/MM/YYYY') : '-'
}

// Format currency display with 2 decimals, empty if no value
const formatCurrency = (value) => {
    if (value == null || value === '') return '-'
    return parseFloat(value).toFixed(2)
}

// Prevent selecting future dates in date picker (optional)
const disabledFutureDates = (time) => {
    return time.getTime() > Date.now()
}

// Reload repairs when asset changes
watch(
    () => props.asset.id,
    (newId) => {
        if (newId) {
            form.asset_id = newId
            loadRepairs()
        }
    }
)

onMounted(() => loadRepairs())
</script>
