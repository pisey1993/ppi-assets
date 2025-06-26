<template>
    <div class="min-h-screen bg-gray-100 text-gray-800">
        <!-- Navbar -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-4 px-6 flex justify-between items-center">
                <h1 class="text-xl font-bold">My App</h1>
                <nav class="space-x-4">
                    <Link href="/" class="hover:underline">Home</Link>
                    <Link href="/dashboard" class="hover:underline">Dashboard</Link>
                    <Link href="/logout" method="post" as="button" class="hover:underline">Logout</Link>
                </nav>
            </div>
        </header>

        <!-- Page Content -->
        <main class="py-8 px-6 max-w-7xl mx-auto">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white mt-12 py-4 text-center text-sm text-gray-500 border-t">
            © 2025 My App. All rights reserved.
        </footer>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'
export default {
    name: "AppLayout"
}
</script>

<style scoped>

</style>
