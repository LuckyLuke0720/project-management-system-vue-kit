<script setup lang="ts">
import { defineProps, ref } from 'vue';

//TODO: see how to pass the correct user so that assignee is not NULL
interface Task {
  id: number;
  title: string;
  description: string;
  due_date: string;
  status: 'To Do' | 'In Progress' | 'Under Review' | 'Completed';
  assignee_user_id: number;
  assignee?: {
    id: number;
    name: string;
  };
}

interface TaskItemProps {
  task: Task;
  canReorder: boolean;
  canModifyStatus: boolean;
}

const passedProps = defineProps<TaskItemProps>();
const emit = defineEmits(['task-click', 'status-change']);

// Status color mapping
const statusColors = {
  'To Do': 'bg-blue-50 border-blue-200',
  'In Progress': 'bg-yellow-50 border-yellow-200',
  'Under Review': 'bg-purple-50 border-purple-200',
  'Completed': 'bg-green-50 border-green-200'
};

const handleTaskClick = () => {
  emit('task-click', passedProps.task);
};
const statusOptions = ['To Do', 'In Progress', 'Under Review', 'Completed'] as const;
type TaskStatus = typeof statusOptions[number];
const showStatusModal = ref(false);
const isStatusHovered = ref(false);

const handleStatusChange = (newStatus: TaskStatus) => {
  if (passedProps.task.status !== newStatus) {
    emit('status-change', passedProps.task.id, newStatus);
  }
  showStatusModal.value = false;
};

const toggleStatusModal = (event: Event) => {
  event.stopPropagation();
  showStatusModal.value = !showStatusModal.value;
};

// Close modal when clicking outside
const closeStatusModal = () => {
  showStatusModal.value = false;
};

// Handle mouse enter on status badge
const handleStatusMouseEnter = () => {
  if (passedProps.canModifyStatus) {
    isStatusHovered.value = true;
  }
};

// Handle mouse leave on status badge
const handleStatusMouseLeave = () => {
  isStatusHovered.value = false;
};
</script>

<template>
<div :class="['w-full p-4 rounded-lg border transition-all duration-300 hover:shadow-md', statusColors[task.status] || 'bg-gray-50']" @click="handleTaskClick">
    <div class="flex justify-between items-center mb-2">
      <h3 class="font-semibold text-lg text-black">{{ passedProps.task.title }}</h3>
      <span 
        v-if="canReorder" 
        class="cursor-move black hover:text-gray-600"
        title="Drag to reorder"
        @click.stop>
        ⋮⋮
      </span>
    </div>
    
    <p class="text-sm text-gray-600 mb-2">{{ passedProps.task.description }}</p>
    
    <div class="mt-2">
        <p class="text-sm text-gray-600">
          Assignee: {{ passedProps.task.assignee ? passedProps.task.assignee.name : 'Unassigned'  }}
        </p>
    </div>

    <div class="flex justify-between items-center text-sm">
      <span class="text-gray-500">
        Due: {{ new Date(task.due_date).toLocaleDateString() }}
      </span>
      <!-- Status, hover button -->
      <div 
        class="relative" 
        @mouseenter="handleStatusMouseEnter" 
        @mouseleave="handleStatusMouseLeave"
        @click.stop
      >
      <span 
        :class="[
          'px-2 py-1 rounded-full text-xs',
          {
            'bg-blue-100 text-blue-800': task.status === 'To Do',
            'bg-yellow-100 text-yellow-800': task.status === 'In Progress',
            'bg-purple-100 text-purple-800': task.status === 'Under Review',
            'bg-green-100 text-green-800': task.status === 'Completed'
          }
        ]">
        {{ passedProps.task.status }}
      </span>
      <button 
          v-if="isStatusHovered && canModifyStatus && !showStatusModal"
          @click="toggleStatusModal"
          class="absolute inset-0 flex items-center justify-center bg-red-900 bg-opacity-100 rounded-full text-xs text-white">
          Modify
        </button>
        <div 
          v-if="showStatusModal" 
          class="fixed inset-0 z-50 flex items-center justify-center"
          @click="closeStatusModal"
        >
          <div class="absolute inset-0 bg-black opacity-25"></div>
          <div 
            class="bg-white rounded-lg shadow-lg p-4 z-10" 
            @click.stop
          >
            <h4 class="font-medium text-black mb-2">Change Status</h4>
            <div class="flex flex-col space-y-2">
              <button 
                v-for="status in statusOptions.filter(s => s !== task.status)" 
                :key="status"
                @click="handleStatusChange(status)"
                class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 text-black rounded"
              >
                {{ status }}
              </button>
            </div>
          </div>
      </div>
    </div>
    </div>
</div>
</template>