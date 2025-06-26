<script setup>
import { router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({ employees: Array });

const deleteEmployee = (id) => {
    if (confirm("Are you sure you want to delete this employee?")) {
        router.delete(`/employees/${id}`);
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="p-4">
            <h1 class="text-xl font-bold mb-4">Employees</h1>

            <a
                href="/employees/create"
                class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
                Create New
            </a>

            <table class="w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2 text-left">Name</th>
                    <th class="border p-2 text-left">Email</th>
                    <th class="border p-2 text-left">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="emp in employees" :key="emp.id" class="hover:bg-gray-50">
                    <td class="border p-2">{{ emp.name }}</td>
                    <td class="border p-2">{{ emp.email }}</td>
                    <td class="border p-2 space-x-2">
                        <a
                            :href="`/employees/${emp.id}/edit`"
                            class="text-indigo-600 hover:underline"
                        >
                            Edit
                        </a>
                        <button
                            @click="deleteEmployee(emp.id)"
                            class="text-red-600 hover:underline"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

    </AuthenticatedLayout>
</template>
