<script setup>
import { Head } from '@inertiajs/vue3';


const props = defineProps({
    asset: Object,
    category: Object,
    assigned_user: Object,
    location: Object,
});
</script>

<template>
    <Head :title="`Print Asset: ${asset.asset_code}`" />

        <div class="max-w-3xl mx-auto bg-white p-8 rounded shadow print:shadow-none print:p-0 print:rounded-none">
            <!-- Company Header -->
            <div class="text-center mb-6 border-b pb-4">
                <h1 class="text-2xl font-bold">People & Partners Insurance Plc.</h1>
                <p class="text-sm">Asset Record Printout</p>
            </div>

            <!-- Asset Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <label class="font-semibold">Asset Code:</label>
                    <div>{{ asset.asset_code }}</div>
                </div>
                <div>
                    <label class="font-semibold">Name:</label>
                    <div>{{ asset.name }}</div>
                </div>

                <div>
                    <label class="font-semibold">Category:</label>
                    <div>{{ category?.category_name }}</div>
                </div>
                <div>
                    <label class="font-semibold">Model:</label>
                    <div>{{ asset.model }}</div>
                </div>

                <div>
                    <label class="font-semibold">Serial Number:</label>
                    <div>{{ asset.serial_number }}</div>
                </div>
                <div>
                    <label class="font-semibold">Vendor:</label>
                    <div>{{ asset.vendor }}</div>
                </div>

                <div>
                    <label class="font-semibold">Purchase Date:</label>
                    <div>{{ asset.purchase_date }}</div>
                </div>
                <div>
                    <label class="font-semibold">Purchase Cost:</label>
                    <div>{{ asset.purchase_cost }}</div>
                </div>

                <div>
                    <label class="font-semibold">Warranty Expiry:</label>
                    <div>{{ asset.warranty_expiry }}</div>
                </div>

                <div>
                    <label class="font-semibold">Current Status:</label>
                    <div class="capitalize">{{ asset.status }}</div>
                </div>

                <div>
                    <label class="font-semibold">Current Location:</label>
                    <div>{{ location?.name }}</div>
                </div>

                <div>
                    <label class="font-semibold">Assigned User:</label>
                    <div>{{ assigned_user?.name }}</div>
                </div>

                <div class="md:col-span-2">
                    <label class="font-semibold">Notes:</label>
                    <div>{{ asset.notes }}</div>
                </div>

                <div class="md:col-span-2">
                    <label class="font-semibold">Remark:</label>
                    <div>{{ asset.remark }}</div>
                </div>
            </div>

            <!-- Barcode / QR Placeholder -->
<!--            <div class="mt-8 text-center">-->
<!--                <div class="inline-block border p-4">-->
<!--                    &lt;!&ndash; Replace src with generated barcode/QR if available &ndash;&gt;-->
<!--                    <img src="https://via.placeholder.com/150x50?text=Barcode" alt="Barcode" class="mx-auto" />-->
<!--                    <div class="text-xs mt-2">Asset Barcode</div>-->
<!--                </div>-->
<!--            </div>-->

            <!-- Footer -->
            <div class="mt-12 text-center text-xs border-t pt-4">
                Printed on: {{ new Date().toLocaleString() }}
            </div>
        </div>

</template>

<style>
@media print {
    body {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }

    .print\:shadow-none {
        box-shadow: none !important;
    }

    .print\:p-0 {
        padding: 0 !important;
    }

    .print\:rounded-none {
        border-radius: 0 !important;
    }
}
</style>
