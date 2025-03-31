<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import ProjectShowCard from './ProjectShowCard.vue';

interface Project {
    id: number;
    title: string;
    description: string;
}

const props = defineProps<{ projects: Project[], username: string }>(); 

const selectedProjectId = ref<number | null>(null);

const isLoading = ref(false);

const openProjectDetails = (projectId: number) => {
    selectedProjectId.value = projectId; // set the selected project ID on click
};

const closeProjectDetails = () => {
    selectedProjectId.value = null;
};

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <span class="flex items-baseline justify-between mb-4 px-4">
                <h1 class="text-2xl font-bold mb-4">Your Projects</h1>
                <h2 class="text-xl font-bold mb-4">Welcome, {{ username }}</h2>
            </span>
            

            <div v-if="props.projects && props.projects.length" class="grid auto-rows-min gap-4 md:grid-cols-3">
                <button v-for="project in projects" 
                :key="project.id" 
                class="p-4 border rounded-lg shadow w-full text-left cursor-pointer hover:shadow-lg transition hover:border-white"
                @click="openProjectDetails(project.id)">
                    <h2 class="text-xl font-semibold">{{ project.title }}</h2>
                    <p class="text-gray-500">{{ project.description }}</p>
                </button>
            </div>

            <div v-else class="text-gray-500">No projects found.</div>
        </div>

        <!-- Show loading indicator -->
        <div v-if="isLoading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="text-white text-lg">Loading project details...</div>
        </div>
        
        <!-- Project Details Overlay -->
        <ProjectShowCard v-if="selectedProjectId" :projectId="selectedProjectId" @close="closeProjectDetails" />
    </AppLayout>
</template>