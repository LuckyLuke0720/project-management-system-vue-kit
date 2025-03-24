<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
// import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { defineProps } from 'vue';

interface Project {
    id: number;
    title: string;
    description: string;
    // tasks: { id: number }[]; // Adjust if tasks have a more complex structure
}

const props = defineProps<{ projects: Project[] }>(); 


const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <h1 class="text-2xl font-bold mb-4">Your Projects</h1>

            <div v-if="props.projects && props.projects.length" class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div v-for="project in projects" :key="project.id" class="p-4 border rounded-lg shadow">
                    <h2 class="text-xl font-semibold">{{ project.title }}</h2>
                    <p class="text-gray-500">{{ project.description }}</p>
                </div>
            </div>

            <div v-else class="text-gray-500">No projects found.</div>
        </div>
    </AppLayout>
</template>

<!-- <script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <PlaceholderPattern />
            </div>
        </div>
    </AppLayout>
</template> -->