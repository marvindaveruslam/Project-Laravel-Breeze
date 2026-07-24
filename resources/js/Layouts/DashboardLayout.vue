<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div>
        <div>
            Sidebar
        </div>
        <div>
            <div>
                Header
            </div>
            <main>
                <slot/>
            </main>
            <div>
                Footer
            </div>
        </div>
    </div>
</template>
