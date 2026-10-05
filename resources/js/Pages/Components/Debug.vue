<script setup>
defineProps({
  variable: {},
})
</script>

<template>
  <div class="bg-black text-green-400 p-2 text-xs font-mono mb-2 rounded">
    <p><strong>measurements defined?</strong> {{ variable !== undefined }}</p>
    <p><strong>is Array?</strong> {{ Array.isArray(variable) }}</p>
    <p><strong>length:</strong> {{ variable?.length }}</p>
    <p><strong>raw contents:</strong> {{ JSON.stringify(variable) }}</p>
  </div>
</template>
