<script setup>
import {PlusIcon, XMarkIcon} from '@heroicons/vue/16/solid';

const props = defineProps({
    ingredients: Object,
    measurements: Array,
})


function addToArray(value, fieldType) {
    fieldType.push({});
}

function removeFromArray(index, fieldType) {
    fieldType.splice(index, 1);
}
</script>
<template>
    <div class="flex flex-row gap-2 items-center" v-for="(input, index) in ingredients" :key="`ingredient-${index}`">
        <div class="flex flex-col gap-1">
            <label for="quantity">Quantity</label>
            <input
                v-model="input.quantity"
                name="ingredients[]"
                type="number"
                class='bg-gray-200 dark:bg-gray-800 p-2 w-20 invalid:border-1 invalid:border-red-500'
                min="0"
                step="0.25"
                required/>
        </div>

        <!--        <MeasurementList/>-->
        <div class="flex flex-col gap-1">
            <label for="measurements">Measurement</label>
            <select
                v-model="input.measurements"
                name="ingredients[]"
                class='bg-gray-200 dark:bg-gray-800 p-2 w-full invalid:border-1 invalid:border-red-500'
                required
            >
                <option value="" disabled>Select unit</option>
                <option
                    v-for="unit in measurements"
                    :key="unit"
                    :value="unit"
                >
                    {{ unit }}
                </option>
            </select>
        </div>
        <div class="flex flex-col w-full gap-1">
            <label for="name">Ingredient</label>
            <input
                v-model="input.name"
                list="ingredient-list"
                name="ingredients[]"
                class='bg-gray-200 dark:bg-gray-800 p-2  w-full invalid:border-1 invalid:border-red-500'
                maxlength="64"
                minlength="1"
                required/>
            <!--        <IngredientDatalist/>-->
            <datalist id="ingredient-list">
                <option value="Potato"></option>
                <option value="Chicken"></option>
                <option value="Cheese"></option>
                <option value="Banana"></option>
                <option value="Flour"></option>
                <option value="Sugar"></option>
                <option value="Salt"></option>
                <option value="Pepper"></option>
            </datalist>
        </div>

        <!-- Remove Ingredient -->
        <button
            v-show="ingredients.length > 1"
            type="button"
            class="cursor-pointer rounded-full border-2 border-red-500 p-2"
            @click="removeFromArray(index, ingredients)">
            <XMarkIcon class="size-4"/>
        </button>
    </div>
    <!-- Add Ingredient -->
    <button
        type="button"
        class="cursor-pointer self-center flex flex-row gap-2 justify-center items-center rounded-full border-2 border-green-500 p-2 w-fit"
        @click="addToArray(input, ingredients)">
        <PlusIcon class="size-4"/>
        Add Ingredient
        <PlusIcon class="invisible size-4"/>
    </button>
</template>
