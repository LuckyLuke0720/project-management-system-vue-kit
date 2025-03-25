<script setup lang="ts">
import { defineProps, defineEmits, ref, onMounted } from 'vue';
import axios from 'axios';

interface Task {
    id: number;
    title: string;
    description: string;
    due_date: string;
    priority: number;
    status: 'To Do' | 'In Progress' | 'Under Review' | 'Completed';
    assignee_user_id: number;
}

interface Project {
    id: number;
    title: string;
    description: string;
    tasks: Task[];
}

const props = defineProps<{ projectId: number }>();
const emit = defineEmits(['close']);

const project = ref<Project | null>(null);
const isLoading = ref(true);
const error = ref<string | null>(null);

onMounted(async() => {
    try{
        const response = await axios.get(`/projects/${props.projectId}`);
    
        // Set the project data from the response
        project.value = response.data;

        isLoading.value = false;

    } catch(err){
        error.value = err instanceof Error ? err.message : 'Unexpected error while fetching tasks';
        isLoading.value = false;
    }
});

const closeOverlay = () => {
    emit('close');
};
</script>

<template>
    <!-- Loading state -->
    <div v-if="isLoading" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
      <div class="text-white text-lg">Loading project details...</div>
    </div>
  
    <!-- Error state -->
    <div v-else-if="error" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
      <div class="bg-red-500 p-6 rounded-lg text-white">
        <p>{{ error }}</p>
        <button @click="closeOverlay" class="mt-4 bg-white text-red-500 px-4 py-2 rounded">Close</button>
      </div>
    </div>
  
    <!-- Project details -->
    <div v-else-if="project" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-8 z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl w-full relative">
        <button @click="closeOverlay" class="absolute top-4 right-4 text-gray-500 hover:text-gray-700">
          ✖ Close
        </button>
        <h1 class="text-2xl font-bold mb-2">{{ project.title }}</h1>
        <p class="text-gray-600 mb-4">{{ project.description }}</p>
        
        <h2 class="text-xl font-semibold mb-2">Tasks</h2>
        <ul v-if="project.tasks && project.tasks.length" class="space-y-2">
          <li v-for="task in project.tasks" :key="task.id" class="p-2 border rounded">
            <p class="font-bold">{{ task.title }}</p>
            <p class="text-sm text-gray-500">Due: {{ task.due_date }}</p>
            <p class="text-sm text-gray-500">Priority: {{ task.priority }}</p>
            <p class="text-sm text-gray-500">Status: {{ task.status }}</p>
          </li>
        </ul>
        <p v-else>No tasks assigned.</p>
      </div>
    </div>
  </template>
