<script setup>
import { ref, computed } from 'vue'; // Import computed
import { useForm, usePage, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AssetTransferHistory from '@/Components/AssetTransferHistory.vue';
import AssetRepairHistory from '@/Components/AssetRepairHistory.vue';
import { ElSelect, ElOption } from 'element-plus'; // Assuming Element Plus is configured in your project

// Access flash messages from Inertia.js page props
const { flash } = usePage().props;

// Reactive state for showing the success modal
const showSuccessModal = ref(false);

// Reactive state for managing active tab
const activeTab = ref('edit');

// Define props received by this component
const props = defineProps({
    asset: Object, // The asset object to be edited
    users: Array, // List of users for assignment dropdown
    locations: Array, // List of locations for current location dropdown
    categories: Array, // List of categories for category dropdown
    transfers: Array, // Transfer history data
    next_id: Number, // ID of the next asset for navigation
    previous_id: Number, // ID of the previous asset for navigation
});

// Initialize form data using Inertia's useForm hook
const form = useForm({
    asset_code: props.asset.asset_code,
    name: props.asset.name,
    category: props.asset.category?.id ?? '', // Use optional chaining and nullish coalescing
    model: props.asset.model,
    serial_number: props.asset.serial_number,
    vendor: props.asset.vendor,
    purchase_date: props.asset.purchase_date,
    purchase_cost: props.asset.purchase_cost,
    warranty_expiry: props.asset.warranty_expiry,
    account_id: props.asset.account_id,
    status: props.asset.status,
    current_location_id: props.asset.current_location_id,
    assigned_to_user_id: props.asset.assigned_to_user_id,
    notes: props.asset.notes,
    remark: props.asset.remark,
});

// Computed property to check if the form is dirty (i.e., if changes have been made)
// Inertia's useForm provides the `isDirty` property.
const isFormDirty = computed(() => form.isDirty);


// Function to handle form submission
const submit = () => {
    // Basic safety check
    if (!props.asset?.id) {
        console.error('Asset ID is missing. Cannot submit the form.');
        return;
    }

    form.put(route('assets.update', props.asset.id), {
        preserveScroll: true, // optional: keeps scroll position
        onSuccess: () => {
            showSuccessModal.value = true;
            setTimeout(() => {
                showSuccessModal.value = false;
            }, 3000);
        },
        onError: (errors) => {
            console.error('Form submission failed:', errors);
            alert('There was an error updating the asset. Please check the fields and try again.');
        },
    });
};
;
</script>

<template>
    <Head title="Edit Asset" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Edit Asset</h2>
        </template>

        <div class="max-w-7xl mx-auto p-6 bg-white rounded shadow mt-4">
            <!-- Tabs Navigation -->
            <nav class="mb-6 border-b border-gray-200">
                <ul class="flex space-x-6">
                    <li>
                        <button
                            @click="activeTab = 'edit'"
                            :class="activeTab === 'edit'
                  ? 'border-b-2 border-blue-600 text-blue-600 font-semibold'
                  : 'text-gray-600 hover:text-blue-600'"
                            class="pb-2"
                        >
                            Edit Asset
                        </button>
                    </li>
                    <li>
                        <button
                            @click="activeTab = 'transfer'"
                            :class="activeTab === 'transfer'
                  ? 'border-b-2 border-blue-600 text-blue-600 font-semibold'
                  : 'text-gray-600 hover:text-blue-600'"
                            class="pb-2"
                        >
                            Transfer History
                        </button>
                    </li>
                    <li>
                        <button
                            @click="activeTab = 'repair'"
                            :class="activeTab === 'repair'
                  ? 'border-b-2 border-blue-600 text-blue-600 font-semibold'
                  : 'text-gray-600 hover:text-blue-600'"
                            class="pb-2"
                        >
                            Repair History
                        </button>
                    </li>
                </ul>
            </nav>

            <!-- Tab Content -->
            <div>
                <!-- Success Modal -->
                <transition name="fade">
                    <div
                        v-if="showSuccessModal"
                        class="fixed-center bg-green-600 text-white px-6 py-3 rounded shadow-lg z-50"
                    >
                        Asset updated successfully!
                    </div>
                </transition>
                <!-- Edit Asset Form -->
                <div v-show="activeTab === 'edit'">
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
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.category_name }}
                                </option>
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
                            <label class="block mb-1 text-sm font-medium text-gray-700">Current Location</label>
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
                            <label class="block mb-1 text-sm font-medium text-gray-700">remark</label>
                            <textarea v-model="form.remark" placeholder="Remark" class="border p-2 rounded w-full"></textarea>
                        </div>
                        <div class="md:col-span-3 flex justify-between items-center">
                            <div>
                                <button
                                    v-if="previous_id"
                                    @click="$inertia.visit(`/assets/${previous_id}/edit`)"
                                    type="button"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2"
                                >
                                    ← Previous
                                </button>

                                <button
                                    v-if="next_id"
                                    @click="$inertia.visit(`/assets/${next_id}/edit`)"
                                    type="button"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded"
                                >
                                    Next →
                                </button>
                            </div>

                            <button
                                type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
                                :disabled="!isFormDirty"
                                :class="{ 'opacity-50 cursor-not-allowed': !isFormDirty }"
                            >
                                Update
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Transfer History -->
                <div v-show="activeTab === 'transfer'">
                    <AssetTransferHistory
                        :transfers="transfers"
                        :users="users"
                        :locations="locations"
                        :asset="asset"
                    />
                </div>

                <!-- Repair History -->
                <div v-show="activeTab === 'repair'">
                    <AssetRepairHistory :asset="asset" />
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* CSS for fade transition */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* CSS for centering the modal */
.fixed-center {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
</style>
