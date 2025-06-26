<script setup>
import {usePage} from '@inertiajs/vue3';

// Access the 'transfer' prop directly from Inertia's usePage().props
// This 'transfer' object is expected to have nested objects for
// 'from_user', 'to_user', 'from_location', 'to_location', and 'asset.category',
// each containing a 'name' or 'category_name' property.
const {transfer} = usePage().props;

/**
 * Initiates the browser's print dialog for the current page.
 * The CSS media query in the <style> block ensures the print button
 * and other non-essential elements are hidden during printing.
 */
const print = () => {
    window.print();
};

// Helper function to safely get a date string or 'N/A'
const formatDate = (dateString) => {
    return dateString
        ? new Date(dateString).toLocaleDateString('en-GB', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        })
        : 'N/A';
};

// Helper function to safely get a currency formatted cost or '0.00'
const formatCost = (cost) => {
    return cost !== null && cost !== undefined
        ? parseFloat(cost).toFixed(2)
        : '0.00';
};
</script>

<template>
    <!-- Main container for the printable content -->
    <div class="max-w-4xl mx-auto my-8 p-8 bg-white shadow-xl rounded-lg
                print:shadow-none print:my-0 print:p-0 print:border-none print:bg-white font-inter">

        <!-- Header Section -->
        <!-- Header Section -->
        <header class="pb-6 mb-8 border-b-2 border-gray-900 print:border-b print:border-gray-400 print:mb-4 print:pb-3">
            <div class="flex flex-col items-center text-center">
                <h3 class="text-xl font-medium text-gray-700 mb-1">People &amp; Partners Insurance PLC</h3>
                <h6 class="text-xl font-extrabold text-gray-900 leading-tight print:text-3xl">
                    Asset Transfer Record
                </h6>
            </div>


            <div class="text-right text-sm text-gray-700 print:text-xs">
                <p class="font-bold text-gray-800">Record ID: #{{ transfer.id }}</p>
                <p>Generated: {{ formatDate(new Date()) }}</p>
            </div>
        </header>

        <!-- Transfer Information Section - Now using a table -->
        <section
            class="mb-10 px-6 py-6 border border-gray-200 rounded-lg bg-gray-50 print:border-none print:bg-white print:p-0 print:mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-300 print:text-xl print:mb-4 print:pb-1">
                Transfer Details</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="bg-white divide-y divide-gray-200 print:divide-gray-300">
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 w-1/2 print:py-1 print:text-xs">Transfer
                            Date
                        </td>
                        <td class="py-2 px-4 text-base text-gray-900 w-1/2 print:py-1 print:text-sm">
                            {{ formatDate(transfer.transfer_date) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">From User</td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ transfer.from_user?.name || 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">To User</td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ transfer.to_user?.name || 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">Approved By
                        </td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ transfer.approved_by || 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">From Location
                        </td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ transfer.from_location?.name || transfer.from_location || 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">To Location
                        </td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ transfer.to_location?.name || transfer.to_location || 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">User Status
                        </td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ transfer.user_status || 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">Reason</td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ transfer.reason || 'No reason provided.' }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Asset Information Section - Now using a table -->
        <section
            class="mb-10 px-6 py-6 border border-gray-200 rounded-lg bg-gray-50 print:border-none print:bg-white print:p-0 print:mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-2 border-b border-gray-300 print:text-xl print:mb-4 print:pb-1">
                Asset Details</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="bg-white divide-y divide-gray-200 print:divide-gray-300">
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 w-1/2 print:py-1 print:text-xs">Asset
                            Code
                        </td>
                        <td class="py-2 px-4 text-base text-gray-900 w-1/2 print:py-1 print:text-sm">
                            {{ transfer.asset.asset_code }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">Name</td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">{{
                                transfer.asset.name
                            }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">Category</td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ transfer.asset.category?.category_name || transfer.asset.category || 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">Model</td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ transfer.asset.model || 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">Serial Number
                        </td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ transfer.asset.serial_number || 'N/A' }}
                        </td>
                    </tr>

                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">Purchase Date
                        </td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ formatDate(transfer.asset.purchase_date) }}
                        </td>
                    </tr>


                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">Status</td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ transfer.asset.status || 'N/A' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2 px-4 text-sm font-medium text-gray-600 print:py-1 print:text-xs">Notes</td>
                        <td class="py-2 px-4 text-base text-gray-900 print:py-1 print:text-sm">
                            {{ transfer.asset.notes || 'No notes provided.' }}
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </section>

        <!-- Signature Section (for print purposes) - Moved to bottom -->
        <footer class="" style="margin-top: 120px">
            <div class="grid grid-cols-2 gap-12 text-sm print:gap-8 print:text-xs">
                <div>
                    <p class="mb-12 border-b border-gray-400 pb-1 w-2/3 print:border-gray-500 print:mb-8 print:w-3/4"></p>
                    <p class="text-gray-800 font-semibold">Transferred By (Signature)</p>
                </div>
                <div class="text-right">
                    <p class="mb-12 border-b border-gray-400 pb-1 w-2/3 ml-auto print:border-gray-500 print:mb-8 print:w-3/4"></p>
                    <p class="text-gray-800 font-semibold">Received By (Signature)</p>
                </div>
            </div>
            <!--            <div class="text-center mt-8 text-xs text-gray-500 print:mt-4 print:text-xxs">-->
            <!--                <p>This document is computer-generated and may not require a physical signature for validity.</p>-->
            <!--                <p class="mt-1">Generated by Asset Management System - Confidential</p>-->
            <!--            </div>-->
        </footer>

        <!-- Print Button - Hidden when printing -->
        <div class="mt-10 text-center print:hidden">
            <button @click="print" class="inline-flex items-center px-10 py-4 border border-transparent text-base font-medium rounded-lg shadow-lg text-white bg-gray-800 hover:bg-gray-700
                                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-300 ease-in-out transform hover:scale-105">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m0 0h6m-6 0v4a2 2 0 002 2h2a2 2 0 002-2v-4m0-12H9M13 14H7m6 6h2m-2-6h.01M17 14h.01M17 17h.01M17 21h.01M17 3v4M5 3v4M3 3h4M3 21h4M21 3h-4M21 21h-4"/>
                </svg>
                Print Transfer Record
            </button>
        </div>
    </div>
</template>

<style>
/* Load Inter font from Google Fonts for a modern look */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');

/* GLOBAL STYLES */
body {
    font-family: 'Inter', sans-serif;
    color: #333;
    background-color: #f8f8f8;
    line-height: 1.6;
    margin: 0;
    padding: 0;
}

/* Base styling for the print view */
.font-inter {
    font-family: 'Inter', sans-serif;
}

/* Tailwind CSS is assumed to be loaded and provides utility classes */
/* If not, include: <script src="https://cdn.tailwindcss.com"></script> in HTML */

/* Print Specific Styles */
@media print {
    body {
        background-color: #fff !important;
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Page size and margins for A4 portrait */
    @page {
        size: A4 portrait;
        margin: 10mm; /* Reduced margins to help fit content on one page */
    }

    /* Hide the print button when printing */
    .print\:hidden {
        display: none !important;
    }

    /* Remove shadows and excessive margins/paddings for print */
    .print\:shadow-none {
        box-shadow: none !important;
    }

    .print\:my-0 {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }

    .print\:p-0 {
        padding: 0 !important;
    }

    .print\:border-none {
        border: none !important;
    }

    .print\:bg-white {
        background-color: #fff !important;
    }

    /* Adjust border thickness and color for print */
    .print\:border-b {
        border-bottom-width: 1px !important;
    }

    .print\:border-t {
        border-top-width: 1px !important;
    }

    .print\:divide-gray-300 {
        border-color: #ccc !important; /* Lighter gray for table borders */
    }

    .print\:border-gray-400 {
        border-color: #a0a0a0 !important;
    }

    .print\:border-gray-500 {
        border-color: #707070 !important;
    }

    /* Further reduced font sizes and adjusted spacing for print */
    .print\:text-xxs {
        font-size: 0.6rem !important;
    }

    /* New extra-extra small size */
    .print\:text-xs {
        font-size: 0.7rem !important;
    }

    .print\:text-sm {
        font-size: 0.8rem !important;
    }

    .print\:text-base {
        font-size: 0.9rem !important;
    }

    .print\:text-xl {
        font-size: 1.1rem !important;
    }

    .print\:text-3xl {
        font-size: 1.8rem !important;
    }

    /* Adjusting margins and padding for print view */
    .print\:mb-4 {
        margin-bottom: 1rem !important;
    }

    /* reduced section margin */
    .print\:pb-3 {
        padding-bottom: 0.75rem !important;
    }

    /* header bottom padding */
    .print\:mb-6 {
        margin-bottom: 1.5rem !important;
    }

    /* section bottom margin */
    .print\:pb-1 {
        padding-bottom: 0.25rem !important;
    }

    /* h2 bottom padding */
    .print\:py-1 {
        padding-top: 0.25rem !important;
        padding-bottom: 0.25rem !important;
    }

    /* table cell padding */
    .print\:mt-8 {
        margin-top: 2rem !important;
    }

    /* footer top margin */
    .print\:pt-4 {
        padding-top: 1rem !important;
    }

    /* footer top padding */
    .print\:mb-8 {
        margin-bottom: 2rem !important;
    }

    /* signature line margin */
    .print\:w-3

/4 {
    width: 75% !important;
} /* signature line width */
    .print\:gap-8 {
        gap: 2rem !important;
    }

    /* signature grid gap */
    /* Ensure elements don't break across pages */
    table, tbody, tr, td {
        page-break-inside: avoid;
    }

    .no-break {
        page-break-inside: avoid;
    }
}
</style>
