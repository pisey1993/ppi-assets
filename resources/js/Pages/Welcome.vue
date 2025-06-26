<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Top Navbar -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div class="text-lg font-semibold text-gray-800">My App</div>
                <nav class="space-x-4">
                    <Link href="/" class="text-sm text-gray-600 hover:text-gray-800">Home</Link>
                    <Link href="/dashboard" class="text-sm text-gray-600 hover:text-gray-800">Dashboard</Link>
                    <Link href="/logout" method="post" as="button" class="text-sm text-red-500">Logout</Link>
                </nav>
            </div>
        </header>

        <!-- Header Slot -->
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </div>

        <!-- Main Content -->
        <main>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>

        <!-- Footer (optional) -->
        <footer class="mt-12 text-center text-sm text-gray-400">
            &copy; 2025 My App. All rights reserved.
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
