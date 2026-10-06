<script setup>
import {ref} from "vue";
import {usePage} from "@inertiajs/vue3";

defineProps({
  input: {},
})
const page = usePage();
const term = ref('');
async function searchIngredients(term) {
  console.log('Term:', term);
  try {
    let response = await fetch(route('ingredients.searchByTerm', term), {
      method: 'Get',
      headers: {
        'X-CSRF-TOKEN': page._token
      }
    })
  }
}
</script>

<template>
  <div class="flex flex-col w-full gap-1">
    <label for="name">Ingredient</label>
    <input
        v-model="term"
        list="ingredient-list"
        name="ingredients[]"
        @keyup="searchIngredients(term)"
        class='bg-gray-200 dark:bg-gray-800 p-2  w-full invalid:border-1 invalid:border-red-500'
        maxlength="64"
        minlength="1"
        required/>
    <!--        <IngredientDatalist/>-->
    <datalist id="ingredient-list">
      <option v-for="ingredient in ingredients" :value="ingredient.id">{{ ingredient.name }}</option>
    </datalist>
  </div>
</template>