<script setup lang="ts">
import { ref, PropType } from 'vue';
import axios from 'axios';

interface TaskFormData {
  title: string;
  description: string;
  due_date: string;
  assignee_user_id: number | null;
  status: 'To Do' | 'In Progress' | 'Under Review' | 'Completed';
}

const props = defineProps({
  projectId: {
    type: Number,
    required: true,
  },
  users: {
    type: Array as PropType<{ id: number; name: string }[]>,
    required: true,
  },
});


const emit = defineEmits(['task-created', 'cancel']);

const formData = ref<TaskFormData>({
  title: '',
  description: '',
  due_date: '',
  assignee_user_id: null,
  status: 'To Do'
});

const isSubmitting = ref(false);
const errorMessage = ref('');


const validateForm = () => {
  if (!formData.value.title.trim()) {
    errorMessage.value = 'Title is required';
    return false;
  }
  
  if (!formData.value.description.trim()) {
    errorMessage.value = 'Description is required';
    return false;
  }
  
  if (!formData.value.due_date) {
    errorMessage.value = 'Due date is required';
    return false;
  }
  
  return true;
};

const createTask = async () => {
  if (!validateForm()) return;
  
  isSubmitting.value = true;
  errorMessage.value = '';
  
  try {
    const response = await axios.post(`/projects/${props.projectId}/tasks`, {
      ...formData.value,
      order: 9999 // order attribute not used 
    });
    
    emit('task-created', response.data);
    
    formData.value = {
      title: '',
      description: '',
      due_date: '',
      assignee_user_id: null,
      status: 'To Do'
    };
    
  } catch (error) {
    console.error('Failed to create task', error);
    errorMessage.value = 'Failed to create task. Please try again.';
  } finally {
    isSubmitting.value = false;
  }
};

const cancelForm = () => {
  emit('cancel');
};
</script>

<template>
  <div class = "text-black overflow-y-auto">
    <h3 class="text-lg font-semibold mb-4">Create New Task</h3>
    
    <form @submit.prevent="createTask" class="space-y-4">
      <div v-if="errorMessage" class="p-3 bg-red-100 text-red-700 rounded-lg mb-4">
        {{ errorMessage }}
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Title -->
        <div class="space-y-2 md:col-span-2">
          <label for="title" class="block text-sm font-medium">Title</label>
          <input
            id="title"
            v-model="formData.title"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-black"
            placeholder="Task title"
          />
        </div>
        
        <!-- Description -->
        <div class="space-y-2 md:col-span-2">
          <label for="description" class="block text-sm font-medium">Description</label>
          <textarea
            id="description"
            v-model="formData.description"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-black"
            placeholder="Task description"
          ></textarea>
        </div>
        
        <!-- Due Date -->
        <div class="space-y-2">
          <label for="due_date" class="block text-sm font-medium">Due Date</label>
          <input
            id="due_date"
            v-model="formData.due_date"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-black"
          />
        </div>
        
        <!-- Assignee -->
        <div class="space-y-2">
          <label for="assignee" class="block text-sm font-medium">Assignee</label>
          <select
            id="assignee"
            v-model="formData.assignee_user_id"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-black"
          >
            <option :value="null">Select Assignee</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }}
            </option>
          </select>
        </div>
        
        <!-- Status -->
        <div class="space-y-2">
          <label for="status" class="block text-sm font-medium">Status</label>
          <select
            id="status"
            v-model="formData.status"
            class="w-full px-3 py-2 border border-gray-300 rounded-md text-black"
          >
            <option value="To Do">To Do</option>
            <option value="In Progress">In Progress</option>
            <option value="Under Review">Under Review</option>
            <option value="Completed">Completed</option>
          </select>
        </div>
      </div>
      
      <div class="pt-4 flex justify-end space-x-3">
        <button
          type="button"
          @click="cancelForm"
          class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="isSubmitting"
        >
          {{ isSubmitting ? 'Creating...' : 'Create Task' }}
        </button>
      </div>
    </form>
  </div>
</template>