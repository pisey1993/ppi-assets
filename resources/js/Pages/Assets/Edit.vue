<script setup>
import {ref, computed} from 'vue'; // Import computed
import {useForm, usePage, Head} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AssetTransferHistory from '@/Components/AssetTransferHistory.vue';
import AssetRepairHistory from '@/Components/AssetRepairHistory.vue';

// Access flash messages from Inertia.js page props
const {flash} = usePage().props;

// Reactive state for showing the success modal
const showSuccessModal = ref(false);

// Reactive state for managing active tab
const activeTab = ref('edit');

// Define props received by this component
const props = defineProps({
    asset: Object,
    repairs: Array,
    users: Array,
    locations: Array,
    categories: Array,
    transfers: Array,
    next_id: Number, // ID of the next asset for navigation
    previous_id: Number, // ID of the next asset for navigation
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
            // Using a custom modal/message box instead of alert()
            // In a real application, you'd have a dedicated component for this.
            // For now, we'll log to console and suggest a UI implementation.
            console.warn('There was an error updating the asset. Please check the fields and try again.');
            // You might want to display these errors more gracefully in the UI.
        },
    });
};

import { router } from '@inertiajs/vue3';

function Prev() {
    if (!props.asset?.id) {
        console.error('Asset ID is missing. Cannot redirect.');
        return;
    }

    const targetId = props.asset.id - 1;

    if (targetId <= 0) {
        console.error('Invalid target asset ID.');
        return;
    }

    console.log('prev', targetId);
    router.visit(`https://www.peoplenpartners.com/public/ppi-asset/public/assets/${targetId}/edit`);
}
function Next() {
    if (!props.asset?.id) {
        console.error('Asset ID is missing. Cannot redirect.');
        return;
    }

    const targetId = props.asset.id + 1;

    if (targetId <= 0) {
        console.error('Invalid target asset ID.');
        return;
    }
    console.log('next', targetId);
    router.visit(`https://www.peoplenpartners.com/public/ppi-asset/public/assets/${targetId}/edit`);
}
function Print() {
    if (!props.asset?.id) {
        console.error('Asset ID is missing. Cannot redirect.');
        return;
    }

    const targetId = props.asset.id;

    console.log('printing asset', targetId);

    router.visit(`https://www.peoplenpartners.com/public/ppi-asset/public/assets/${targetId}/print`);
}


</script>

<template>
    <Head title="Edit Asset"/>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-2xl font-bold text-gray-800 leading-tight print-hidden">Edit Asset</h2>
        </template>

        <div class="max-w-7xl mx-auto p-6 bg-white rounded-xl shadow-lg mt-6 print-area">
            <!-- Tabs Navigation -->
            <nav class="mb-6 border-b-2 border-gray-100 print-hidden">
                <ul class="flex space-x-8">
                    <li>
                        <button
                            @click="activeTab = 'edit'"
                            :class="activeTab === 'edit'
                  ? 'border-b-4 border-blue-600 text-blue-700 font-bold'
                  : 'text-gray-600 hover:text-blue-700 hover:border-blue-300'"
                            class="pb-3 px-1 transition-all duration-200 ease-in-out"
                        >
                            Edit Asset
                        </button>
                    </li>
                    <li>
                        <button
                            @click="activeTab = 'transfer'"
                            :class="activeTab === 'transfer'
                  ? 'border-b-4 border-blue-600 text-blue-700 font-bold'
                  : 'text-gray-600 hover:text-blue-700 hover:border-blue-300'"
                            class="pb-3 px-1 transition-all duration-200 ease-in-out"
                        >
                            Transfer History
                        </button>
                    </li>
                    <li>
                        <button
                            @click="activeTab = 'repair'"
                            :class="activeTab === 'repair'
                  ? 'border-b-4 border-blue-600 text-blue-700 font-bold'
                  : 'text-gray-600 hover:text-blue-700 hover:border-blue-300'"
                            class="pb-3 px-1 transition-all duration-200 ease-in-out"
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
                        class="fixed-center bg-green-500 text-white px-8 py-4 rounded-lg shadow-xl z-50 text-lg font-semibold animate-bounce-in"
                    >
                        Asset updated successfully!
                    </div>
                </transition>
                <!-- Edit Asset Form -->
                <div v-show="activeTab === 'edit'">
                    <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-3 gap-6 print-form-grid">
                        <div class="form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Asset Code</label>
                            <input v-model="form.asset_code" placeholder="Asset Code"
                                   class="form-input"/>
                        </div>

                        <div class="form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Name</label>
                            <input v-model="form.name" placeholder="Name" class="form-input"/>
                        </div>

                        <div class="form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Category</label>
                            <select v-model="form.category" class="form-input">
                                <option disabled value="">-- Select Category --</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.category_name }}
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Model</label>
                            <input v-model="form.model" placeholder="Model" class="form-input"/>
                        </div>

                        <div class="form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Serial Number</label>
                            <input v-model="form.serial_number" placeholder="Serial Number"
                                   class="form-input"/>
                        </div>

                        <div class="form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Vendor</label>
                            <input v-model="form.vendor" placeholder="Vendor" class="form-input"/>
                        </div>

                        <div class="form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Purchase Date</label>
                            <input v-model="form.purchase_date" type="date" class="form-input"/>
                        </div>

                        <div class="form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Purchase Cost</label>
                            <input v-model="form.purchase_cost" type="number" placeholder="Cost"
                                   class="form-input"/>
                        </div>

                        <div class="form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Warranty Expiry</label>
                            <input v-model="form.warranty_expiry" type="date" class="form-input"/>
                        </div>

                        <div class="form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">User Status</label>
                            <select v-model="form.status" class="form-input">
                                <option disabled value="">-- Select User Status --</option>
                                <option value="instock">In Stock</option>
                                <option value="using">Using</option>
                                <option value="repair">Repair</option>
                                <option value="broken">Broken</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Current Location</label>
                            <select v-model="form.current_location_id" class="form-input">
                                <option disabled value="">-- Select Location --</option>
                                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Assigned User</label>
                            <!-- For printing, we need to ensure the selected value of el-select is visible -->
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
                            <!-- Fallback for print to show selected user if el-select doesn't render well -->
                            <div class="print-only-value">
                                {{ users.find(user => user.id === form.assigned_to_user_id)?.name }}
                            </div>
                        </div>

                        <div class="md:col-span-3 form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Notes</label>
                            <textarea v-model="form.notes" placeholder="Notes"
                                      class="form-input h-24 resize-y"></textarea>
                        </div>
                        <div class="md:col-span-3 form-group">
                            <label class="block mb-1 text-sm font-medium text-gray-700">Remark</label>
                            <textarea v-model="form.remark" placeholder="Remark"
                                      class="form-input h-24 resize-y"></textarea>
                        </div>
                        <div class="md:col-span-3 flex justify-between items-center print-hidden">
                            <div class="flex space-x-2">
                                <button
                                    v-if="previous_id"
                                    @click="Prev"
                                    type="button"
                                    class="btn-secondary"
                                >
                                    ← Previous
                                </button>

                                <button
                                    v-if="next_id"
                                    @click="Next"
                                    type="button"
                                    class="btn-secondary"
                                >
                                    Next →
                                </button>
                            </div>

                            <div class="flex space-x-2">
                                <button
                                    type="button"
                                    @click="Print"
                                    class="btn-primary-outline"
                                >
                                    Print Record
                                </button>


                                <button
                                    type="submit"
                                    class="btn-primary"
                                    :disabled="!isFormDirty"
                                    :class="{ 'opacity-50 cursor-not-allowed': !isFormDirty }"
                                >
                                    Update
                                </button>
                            </div>
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
                    <AssetRepairHistory :repairs="repairs"/>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
/* Base styles for form inputs */
.form-input {
    @apply border border-gray-300 p-3 rounded-lg w-full focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 ease-in-out shadow-sm;
}

/* Base styles for buttons */
.btn-primary {
    @apply bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-md;
}

.btn-primary-outline {
    @apply bg-transparent border border-blue-600 text-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-md;
}

.btn-secondary {
    @apply bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-all duration-200 ease-in-out shadow-md;
}

/* CSS for fade transition */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.4s ease;
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

/* Keyframe for bounce-in animation */
@keyframes bounce-in {
    0% {
        transform: translate(-50%, -50%) scale(0.5);
        opacity: 0;
    }
    70% {
        transform: translate(-50%, -50%) scale(1.05);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1);
    }
}

/* Apply bounce-in animation */
.animate-bounce-in {
    animation: bounce-in 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
}
</style>
