<script setup>
import AssetTransferHistory from '@/Components/AssetTransferHistory.vue';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { ElSelect, ElOption } from "element-plus";

defineProps({
    users: Array,
    locations: Array,
    categories: Array,
});

const activeTab = ref('create');

const form = useForm({
    asset_code: '',
    name: '',
    category: '',
    model: '',
    serial_number: '',
    vendor: '',
    purchase_date: '',
    purchase_cost: '',
    warranty_expiry: '',
    account_id: '',
    status: '',
    current_location_id: '',
    assigned_to_user_id: '',
    notes: '',
});

const submit = () => {
    if (form.processing) return; // prevent double submit

    form.post('/assets', {
        onSuccess: () => {
            // optional: reset form or give feedback
            // form.reset();
        },
        onError: () => {
            // optional: handle error
        },
    });
};
</script>

<template>
    <Head title="Assets Management" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Assets Management</h2>
        </template>

        <div class="max-w-7xl mx-auto p-6 bg-white rounded shadow" style="margin-top: 10px">
            <!-- Tabs -->
            <nav class="mb-6 border-b border-gray-200">
                <ul class="flex space-x-6">
                    <li>
                        <button
                            @click="activeTab = 'create'"
                            :class="activeTab === 'create'
                  ? 'border-b-2 border-blue-600 text-blue-600 font-semibold'
                  : 'text-gray-600 hover:text-blue-600'"
                            class="pb-2"
                        >
                            Create Asset
                        </button>
                    </li>
                </ul>
            </nav>

            <!-- Tab Contents -->
            <div>
                <!-- Create Asset Form -->
                <div v-show="activeTab === 'create'">
                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Asset Code</label>
                            <input v-model="form.asset_code" placeholder="Asset Code" class="border p-2 rounded w-full" />
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Name</label>
                            <input v-model="form.name" placeholder="Name" class="border p-2 rounded w-full" />
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Category</label>
                            <select v-model="form.category" class="border p-2 rounded w-full">
                                <option disabled value="">-- Select Category --</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.category_name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Model</label>
                            <input v-model="form.model" placeholder="Model" class="border p-2 rounded w-full" />
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Serial Number</label>
                            <input v-model="form.serial_number" placeholder="Serial Number" class="border p-2 rounded w-full" />
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Vendor</label>
                            <input v-model="form.vendor" placeholder="Vendor" class="border p-2 rounded w-full" />
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Purchase Date</label>
                            <input v-model="form.purchase_date" type="date" class="border p-2 rounded w-full" />
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Purchase Cost</label>
                            <input v-model="form.purchase_cost" type="number" placeholder="Cost" class="border p-2 rounded w-full" />
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Warranty Expiry</label>
                            <input v-model="form.warranty_expiry" type="date" class="border p-2 rounded w-full" />
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">User Status</label>
                            <select v-model="form.status" class="border p-2 rounded w-full">
                                <option disabled value="">-- Select User Status --</option>
                                <option value="instock">In Stock</option>
                                <option value="using">Using</option>
                                <option value="repair">Repair</option>
                                <option value="broken">Broken</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Location</label>
                            <select v-model="form.current_location_id" class="border p-2 rounded w-full">
                                <option disabled value="">-- Select Location --</option>
                                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Assigned User</label>
                            <el-select
                                v-model="form.assigned_to_user_id"
                                placeholder="-- Select Assigned User --"
                                class="w-full"
                                filterable
                            >
                                <el-option
                                    v-for="user in users"
                                    :key="user.id"
                                    :label="user.name"
                                    :value="user.id"
                                />
                            </el-select>
                        </div>

                        <div class="md:col-span-3">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Notes</label>
                            <textarea v-model="form.notes" placeholder="Notes" class="border p-2 rounded w-full"></textarea>
                        </div>
                        <div class="md:col-span-3">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Notes</label>
                            <textarea v-model="form.notes" placeholder="Notes" class="border p-2 rounded w-full"></textarea>
                        </div>

                        <div class="md:col-span-3 flex justify-end">
                            <button
                                type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 flex items-center justify-center"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing" class="loader mr-2"></span>
                                {{ form.processing ? 'Submitting...' : 'Submit' }}
                            </button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.loader {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3490dc;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg);}
    100% { transform: rotate(360deg);}
}
</style>
