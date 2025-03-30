<script setup lang="ts">
import { defineProps, defineEmits, ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { VueDraggable } from 'vue-draggable-plus';
import TaskItem from './TaskItem.vue';
import CommentsSideBar from './CommentsSideBar.vue';

interface Task {
    id: number;
    title: string;
    description: string;
    due_date: string;
    order: number;
    status: 'To Do' | 'In Progress' | 'Under Review' | 'Completed';
    assignee_user_id: number;
}

interface Project {
    id: number;
    title: string;
    description: string;
    tasks: Task[];
    pivot?: {
        role: string;
    };
}

const props = defineProps<{ projectId: number }>();
const emit = defineEmits(['close']);

const tasksArray = ref<Task[]>([]);
const project = ref<Project | null>(null);
const isLoading = ref(true);
const error = ref<string | null>(null);
const selectedTask = ref<Task | null>(null);
const showCommentSidebar = ref(false);

// Compute if the current user has authority (role) to be able to modify
const canReorderTasks = computed(() => {
    return project.value?.pivot?.role === ('Owner');
});

onMounted(async () => {
    try {
        const response = await axios.get(`/projects/${props.projectId}`);

        // Set the project data from the response
        project.value = response.data;

        //Saving an order local copy 
        tasksArray.value = [...(response.data.tasks || [])];

        isLoading.value = false;

    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Unexpected error while fetching tasks';
        isLoading.value = false;
    }
});

const handleTaskReorder = async () => {
    if (canReorderTasks.value) {
        // Save order to local storage
        localStorage.setItem(`project_${props.projectId}_task_order`, JSON.stringify(
            tasksArray.value.map(task => task.id)
        ));

        // Send order update to backend
        try {
            await axios.post(`/projects/${props.projectId}/update-task-order`, {
                task_order: tasksArray.value.map((task, index) => ({
                    id: task.id,
                    order: index
                }))
            });
        } catch (error) {
            console.error('Failed to update task order', error);
        }
    }
}

const handleTaskClick = (task: Task) => {
    selectedTask.value = task;
    showCommentSidebar.value = true;
}

const closeCommentSidebar = () => {
    showCommentSidebar.value = false;
    selectedTask.value = null;
}

const closeOverlay = () => {
    emit('close');
};
</script>

<template>
    <!-- Loading -->
    <div v-if="isLoading" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
        <div class="text-white text-lg">Loading project details...</div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-red-500 p-6 rounded-lg text-white">
            <p>{{ error }}</p>
            <button @click="closeOverlay" class="mt-4 bg-white text-red-500 px-4 py-2 rounded">Close</button>
        </div>
    </div>

    <!-- Project details -->
    <div v-else-if="project" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-8 z-50">
        <div class="bg-black p-6 rounded-lg shadow-lg max-w-2xl w-full relative max-h-[80vh] flex flex-col"
            :class="{'max-w-2xl': !showCommentSidebar, 'max-w-5xl': showCommentSidebar}">
            <button @click="closeOverlay"
                class="close-button absolute top-4 right-4 text-red-500 hover:text-red-100 hover:bg-red-500/20 rounded-full p-1 transition-colors duration-300 ease-in-out">
                Close
            </button>
            <h1 class="text-2xl font-bold mb-2">{{ project.title }}</h1>
            <p class="text-gray-600 mb-4">{{ project.description }}</p>

            <div class="flex-grow flex overflow-hidden">
                <!-- Tasks section -->
                <div class="flex-grow" :class="{'pr-4': showCommentSidebar}">
                    <h2 class="text-xl font-semibold mb-2 sticky top-0 bg-black z-10 pb-2">Tasks</h2>
                    <div class="overflow-y-auto" style="max-height: calc(80vh - 100px);">
                        <div v-if="project.tasks && project.tasks.length" class="flex flex-col gap-4">
                            <VueDraggable v-model="project.tasks" :disabled="!canReorderTasks" @end="handleTaskReorder" class="flex flex-col gap-4">
                                <div v-for="task in project.tasks" :key="task.id" class="w-full">
                                    <TaskItem 
                                        :task="task" 
                                        :can-reorder="canReorderTasks" 
                                        class="w-full" 
                                        @task-click="handleTaskClick"
                                    />
                                </div>
                            </VueDraggable>
                        </div>
                        <p v-else>No tasks assigned.</p>
                    </div>
                </div>
                <!-- Comments sidebar -->
                <div v-if="showCommentSidebar && selectedTask" class="w-1/3 ml-4 border-l border-gray-700">
                    <CommentsSideBar 
                        :task-id="selectedTask.id" 
                        :task-title="selectedTask.title"
                        @close="closeCommentSidebar"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.close-button:hover {
    color: theme('colors.red.100');
    background-color: theme('colors.red.500 / 0.5');
}
</style>