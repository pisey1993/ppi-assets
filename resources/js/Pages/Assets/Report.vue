<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
// import Pagination from '@/Shared/Pagination.vue'; // Make sure you have a Pagination component

const props = defineProps({
    assets: Object,
    filters: Object,
    statuses: Array,
    departments: Array,
});

const filters = ref({
    name: props.filters.name || '',
    status: props.filters.status || '',
    location: props.filters.department || '',
    purchase_date_from: props.filters.purchase_date_from || '',
    purchase_date_to: props.filters.purchase_date_to || '',
    purchase_age: props.filters.purchase_age || '',
    show_all: props.filters.show_all === '1' || false,
});

const applyFilter = debounce(() => {
    router.get(route('assets.report'), {
        name: filters.value.name,
        status: filters.value.status,
        department: filters.value.location,
        purchase_date_from: filters.value.purchase_date_from,
        purchase_date_to: filters.value.purchase_date_to,
        purchase_age: filters.value.purchase_age,
        show_all: filters.value.show_all ? 1 : 0,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300);
</script>

<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <h1 class="text-2xl font-bold mb-4">Asset Report</h1>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
            <input
                type="text"
                class="p-2 border rounded"
                placeholder="Search by Name"
                v-model="filters.name"
                @input="applyFilter"
            />

            <select class="p-2 border rounded" v-model="filters.status" @change="applyFilter">
                <option value="">All Statuses</option>
                <option v-for="status in props.statuses" :key="status" :value="status">
                    {{ status }}
                </option>
            </select>

            <select class="p-2 border rounded" v-model="filters.location" @change="applyFilter">
                <option value="">All Locations</option>
                <option v-for="loc in props.departments" :key="loc" :value="loc">
                    {{ loc }}
                </option>
            </select>

            <input
                type="date"
                class="p-2 border rounded"
                v-model="filters.purchase_date_from"
                @change="applyFilter"
                placeholder="Purchase From"
            />

            <input
                type="date"
                class="p-2 border rounded"
                v-model="filters.purchase_date_to"
                @change="applyFilter"
                placeholder="Purchase To"
            />

            <select class="p-2 border rounded" v-model="filters.purchase_age" @change="applyFilter">
                <option value="">All Ages</option>
                <option value="3">Over 3 Years</option>
                <option value="5">Over 5 Years</option>
                <option value="10">Over 10 Years</option>
            </select>
        </div>

        <!-- Show All Toggle -->
        <div class="mb-6 flex items-center space-x-2">
            <input
                type="checkbox"
                id="showAll"
                v-model="filters.show_all"
                @change="applyFilter"
            />
            <label for="showAll" class="text-sm">Show All Records</label>
        </div>

        <!-- Asset Table -->
        <div class="overflow-x-auto bg-white border rounded">
            <table class="min-w-full text-sm border-collapse">
                <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2 border">Asset Code</th>
                    <th class="p-2 border">Name</th>
                    <th class="p-2 border">Purchase Date</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Assigned To</th>
                    <th class="p-2 border">Location</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="asset in props.assets.data" :key="asset.id" class="hover:bg-gray-50">
                    <td class="p-2 border">{{ asset.asset_code }}</td>
                    <td class="p-2 border">{{ asset.name }}</td>
                    <td class="p-2 border">{{ asset.purchase_date }}</td>
                    <td class="p-2 border">{{ asset.status }}</td>
                    <td class="p-2 border">{{ asset.assigned_user?.name || '—' }}</td>
                    <td class="p-2 border">{{ asset.current_location?.name || '—' }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
<!--        <div class="mt-4" v-if="!filters.show_all">-->
<!--            <Pagination :links="props.assets.links" />-->
<!--        </div>-->
    </div>
</template>
