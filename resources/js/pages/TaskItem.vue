<script setup lang="ts">
import { defineProps } from 'vue';

interface Task {
  id: number;
  title: string;
  description: string;
  due_date: string;
  status: 'To Do' | 'In Progress' | 'Under Review' | 'Completed';
  assignee_user_id: number;
}

interface TaskItemProps {
  task: Task;
  canReorder: boolean;
}

const passedProps = defineProps<TaskItemProps>();
const emit = defineEmits(['task-click']);

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
    
    <div class="flex justify-between items-center text-sm">
      <span class="text-gray-500">
        Due: {{ new Date(task.due_date).toLocaleDateString() }}
      </span>
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
    </div>
</div>
</template>