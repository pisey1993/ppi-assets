<template>
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Repair History</h3>

        <div class="flex justify-end">
            <el-button type="primary" @click="openCreateModal">Add Repair</el-button>
        </div>

        <!-- Repair Modal -->
        <el-dialog v-model="showRepairModal" :title="repairForm.id ? 'Edit Repair' : 'Add Repair'" width="600px">
            <form @submit.prevent="saveRepair" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <el-date-picker v-model="repairForm.repair_date" type="date" placeholder="Repair Date" style="width: 100%" />
                <el-input v-model="repairForm.issue" placeholder="Issue" />
                <el-input v-model="repairForm.repair_cost" type="number" placeholder="Cost" />
                <el-input v-model="repairForm.status" placeholder="Status" />
                <el-input v-model="repairForm.vendor" placeholder="Vendor" />
                <el-input v-model="repairForm.remarks" type="textarea" placeholder="Remarks" :rows="2" class="md:col-span-2" />

                <div class="md:col-span-2 flex justify-end mt-2">
                    <el-button @click="showRepairModal = false">Cancel</el-button>
                    <el-button type="primary" native-type="submit">{{ repairForm.id ? 'Update' : 'Add' }}</el-button>
                </div>
            </form>
        </el-dialog>

        <!-- Delete Confirmation Modal -->
        <el-dialog v-model="showDeleteModal" title="Confirm Delete" width="400px">
            <p>Are you sure you want to delete this repair?</p>
            <template #footer>
                <el-button @click="showDeleteModal = false">Cancel</el-button>
                <el-button type="danger" @click="confirmDelete">Delete</el-button>
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
                <td class="p-2">{{ r.repair_date }}</td>
                <td class="p-2">{{ r.issue }}</td>
                <td class="p-2">{{ r.repair_cost }}</td>
                <td class="p-2">{{ r.status }}</td>
                <td class="p-2">{{ r.vendor }}</td>
                <td class="p-2">{{ r.remarks }}</td>
                <td class="p-2">
                    <el-button type="text" size="small" @click="editRepair(r)">Edit</el-button>
                    <el-button type="text" size="small" class="text-red-600" @click="prepareDelete(r.id)">Delete</el-button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
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

const repairForm = reactive({
    id: null,
    asset_id: props.asset.id,
    repair_date: '',
    issue: '',
    repair_cost: '',
    status: '',
    vendor: '',
    remarks: '',
})

const loadRepairs = async () => {
    if (!props.asset?.id) return
    try {
        const res = await axios.get(`/assets/${props.asset.id}/repairs`)
        repairs.value = res.data
    } catch (err) {
        console.error('Failed to load repairs:', err)
    }
}

const openCreateModal = () => {
    resetForm()
    showRepairModal.value = true
}

const saveRepair = () => {
    const url = repairForm.id ? `/repairs/${repairForm.id}` : '/repairs'
    const method = repairForm.id ? 'put' : 'post'

    const payload = {
        ...repairForm,
        repair_date: dayjs(repairForm.repair_date).format('YYYY-MM-DD'),
    }

    router.visit(url, {
        method,
        data: payload,
        preserveScroll: true,
        onSuccess: () => {
            showRepairModal.value = false
            resetForm()
            loadRepairs()
        },
        onError: (err) => {
            console.error('Failed to save:', err)
            alert('Save failed')
        }
    })
}

const editRepair = (repair) => {
    Object.assign(repairForm, {
        ...repair,
        repair_date: repair.repair_date // keep original string for <el-date-picker>
    })
    showRepairModal.value = true
}

const prepareDelete = (id) => {
    deleteId.value = id
    showDeleteModal.value = true
}

const confirmDelete = () => {
    router.delete(`/repairs/${deleteId.value}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false
            deleteId.value = null
            loadRepairs()
        }
    })
}

const resetForm = () => {
    repairForm.id = null
    repairForm.asset_id = props.asset.id
    repairForm.repair_date = ''
    repairForm.issue = ''
    repairForm.repair_cost = ''
    repairForm.status = ''
    repairForm.vendor = ''
    repairForm.remarks = ''
}

onMounted(() => loadRepairs())

watch(() => props.asset.id, (newId) => {
    if (newId) {
        repairForm.asset_id = newId
        loadRepairs()
    }
})
</script>
