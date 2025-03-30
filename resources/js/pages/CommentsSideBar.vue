<script setup lang="ts">
import { defineProps, defineEmits, ref, onMounted, watch } from 'vue';
import axios from 'axios';

interface Comment {
  id: number;
  content: string;
  task_id: number;
  user_id: number;
  user: {
    name: string;
  };
  created_at: string;
  updated_at: string;
}

const props = defineProps<{
  taskId: number;
  taskTitle: string;
}>();

const emit = defineEmits(['close']);

const comments = ref<Comment[]>([]);
const isLoading = ref(true);
const error = ref<string | null>(null);
const newComment = ref('');
const isSaving = ref(false);

const fetchComments = async () => {
  isLoading.value = true;
  try {
    const response = await axios.get(`/tasks/${props.taskId}/comments`);
    comments.value = response.data;
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Unexpected error while fetching comments';
  } finally {
    isLoading.value = false;
  }
};

const saveComment = async () => {
  if (!newComment.value.trim()) return;
  
  isSaving.value = true;
  try {
    const response = await axios.post(`/tasks/${props.taskId}/comments`, {
      content: newComment.value
    });
    
    comments.value.push(response.data);
    newComment.value = ''; // clear the input
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Unexpected error while saving comment';
  } finally {
    isSaving.value = false;
  }
};

const handleKeyDown = (event: KeyboardEvent) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault();
    saveComment();
  }
};

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleString();
};

const closeSidebar = () => {
  emit('close');
};

onMounted(fetchComments);

// re-fetch comments when taskId changes
watch(() => props.taskId, fetchComments);
</script>

<template>
  <div class="h-full flex flex-col shadow-lg">
    <div class="p-4 border-b border-gray-700 flex justify-between items-center bg-black-100">
      <h2 class="text-xl text-white font-semibold">Comments: {{ taskTitle }}</h2>
      <button @click="closeSidebar" class="text-red-500">
        <span>×</span>
      </button>
    </div>
    
    <!-- Loading state -->
    <div v-if="isLoading" class="flex-grow flex items-center justify-center">
      <p class="text-gray-500">Loading comments...</p>
    </div>
    
    <!-- Error state -->
    <div v-else-if="error" class="flex-grow flex items-center justify-center">
      <p class="text-red-500">{{ error }}</p>
    </div>
    
    <!-- Comments list -->
    <div v-else class="flex-grow overflow-y-auto p-4">
      <div v-if="comments.length === 0" class="flex items-center justify-center h-full">
        <p class="text-gray-500">This task has no comments. Write one!</p>
      </div>
      
      <div v-else class="space-y-4">
        <div 
          v-for="comment in comments" 
          :key="comment.id" 
          class="p-3 bg-white rounded-lg shadow-sm"
        >
          <div class="flex justify-between items-start">
            <span class="font-medium text-gray-800">{{ comment.user.name }}</span>
            <span class="text-xs text-gray-800">{{ formatDate(comment.created_at) }}</span>
          </div>
          <p class="mt-2 text-gray-700 whitespace-pre-wrap">{{ comment.content }}</p>
        </div>
      </div>
    </div>
    
    <!-- Comment input -->
    <div class="p-4 border-t">
      <div class="relative">
        <textarea
          v-model="newComment"
          @keydown="handleKeyDown"
          placeholder="Write a comment..."
          class="w-full border text-black border-gray-300 rounded-lg p-3 pr-20 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
          rows="3"
        ></textarea>
        <button
          @click="saveComment"
          :disabled="isSaving || !newComment.trim()"
          class="absolute bottom-3 right-3 px-4 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:bg-gray-500 disabled:cursor-not-allowed"
        >
          {{ isSaving ? 'Saving...' : 'Send' }}
        </button>
      </div>
    </div>
  </div>
</template>