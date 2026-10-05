<script setup>
import RecipeLayout from '../Components/RecipeLayout.vue';
import {Head, useForm} from '@inertiajs/vue3';
import {ref, watch} from 'vue';
import {showToast} from '../../Composables/useToast';
import IngredientEditor from '../Components/IngredientEditor.vue';
import StepEditor from '../Components/StepEditor.vue';

const props = defineProps({
    tags: Object,
    measurements: Array,
})

const imageUrl = ref('');
let file
const previewImage = (event) => {
    file = event.target.files[0]
    if (!file) return
    const reader = new FileReader()
    reader.onload = (e) => {
        imageUrl.value = e.target.result
    }
    reader.readAsDataURL(file)
}

const ingredients = ref([{}]);
const steps = ref([{}]);

const form = useForm({
    title: '',
    image: '',
    ingredients: [],
    steps: [],
    preparation_time: 0,
    cooking_time: 0,
    servings: 0,
    difficulty: '',
    tags: []
})
const submit = () => {
    form.image = file;
    form.steps = steps.value;
    form.ingredients = ingredients.value;
    form.post(route('recipes.store'), {
        onError: () => {
            Object.keys(form.errors).forEach(key => {
                showToast('error', form.errors[key])
            })
        },
        onSuccess: () => {
            showToast('success', 'Recipe created successfully!')
        }
    });
}

// const shortcutChecker = (event) => {
//     if (event.key === 'Enter') {
//         console.log('Enter pressed, if last step we will add new step and focus new step');
//         // if event div step === step[-1]
//         // otherwise do nothing?
//
//     } else {
//         console.log('Key pressed: ', event.key);
//     }
// }

watch(form.errors, (errors)=> {
    console.log('got an error mate')
    for(const error of errors) {
        showToast('error', error)
    }
})

</script>
<template>
    <Head>
        <title>Create Recipe</title>
    </Head>
    <RecipeLayout>
        <div class='bg-gray-100 dark:bg-gray-900 p-4 flex flex-col'>
            <h2>Create a New Recipe</h2>
            <!-- Image Preview -->
            <div class='dark:bg-gray-950 bg-gray-50 w-full flex justify-center align-items-center'>
                <img v-if="imageUrl" id='image-preview' alt="Recipe Image Preview" :src='imageUrl' class='aspect-auto h-fit max-h-80 self-center'>
            </div>

            <!-- Create Recipe Form -->
            <form @submit.prevent="submit" class='flex flex-col p-4 gap-2' enctype="multipart/form-data">

                <!-- Image File Picker -->
                <div>
                    <label for="image" class='form-label'>Image</label>
                    <input @change="previewImage" type="file" id="image" class='form-control w-full' name="image" accept="image/*">
                </div>

                <!-- Title Input -->
                <div>
                    <label for="title" class='form-label'>Recipe Title</label>
                    <p v-if="form.errors.title" class="text-red-500">{{form.errors.title}}</p>
                    <input type="text" v-model="form.title" id="title" name="title" class='form-control w-full' value='' required>
                </div>

                <!-- Ingredients -->

                <div class="flex flex-col gap-2">
                    <label for="ingredients" class='form-label'>Ingredients</label>
                    <p v-if="form.errors.ingredients" class="text-red-500">{{form.errors.ingredients}}</p>
                    <IngredientEditor :ingredients :measurements></IngredientEditor>
                </div>

                <!-- Steps -->
                <div class="flex flex-col gap-2">
                    <label for="instructions" class='form-label'>Method</label>
                    <p v-if="form.errors.method" class="text-red-500">{{form.errors.method}}</p>
                    <!-- <p><strong><small>Pressing Enter on your last step will add a new step and focus that</small></strong></p> -->
                    <StepEditor :steps></StepEditor>

                </div>

                <!-- Preparation Time -->
                <div class="flex flex-col">
                    <label for="preparation_time" class='form-label'>Preparation Time (minutes)</label>
                    <p v-if="form.errors.preparation_time" class="text-red-500">{{form.errors.preparation_time}}</p>
                    <input v-model="form.preparation_time" type="number" id="preparation_time" class='bg-gray-200 dark:bg-gray-800 p-2' name="preparation_time" value='' required>
                </div>

                <!-- Cooking Time -->
                <div class="flex flex-col">
                    <label for="cooking_time" class='form-label'>Cooking Time (minutes)</label>
                    <p v-if="form.errors.cooking_time" class="text-red-500">{{form.errors.cooking_time}}</p>
                    <input v-model="form.cooking_time" type="number" id="cooking_time" class='bg-gray-200 dark:bg-gray-800 p-2' name="cooking_time" value='' required>
                </div>

                <!-- Servings -->
                <div class="flex flex-col">
                    <label class='form-label' for="servings">Servings</label>
                    <p v-if="form.errors.servings" class="text-red-500">{{form.errors.servings}}</p>
                    <input v-model="form.servings" type="number" id="servings" name="servings" class='bg-gray-200 dark:bg-gray-800 p-2' value='' required>
                </div>

                <!-- Difficulty -->
                <div class='w-full'>
                    <label class='form-label'>Difficulty</label>
                    <p v-if="form.errors.difficulty" class="text-red-500">{{form.errors.difficulty}}</p>
                    <div class="flex  mb-2">
                        <input v-model="form.difficulty" name="difficulty" id='easy' type="radio" value="Easy">
                        <label class='form-label' for="easy">Easy</label>
                    </div>
                    <div class="flex  mb-2">
                        <input v-model="form.difficulty" name="difficulty" id='medium' type="radio" value="Medium">
                        <label class='form-label' for="medium">Medium</label>
                    </div>
                    <div class="flex  mb-2">
                        <input v-model="form.difficulty" name="difficulty" id='hard' type="radio" value="Hard">
                        <label class='form-label' for="hard">Hard</label>
                    </div>
                </div>

                <!-- Tags -->
                <ul class=" select-none  flex flex-row gap-2 flex-wrap">
                    <li v-for="(tag, index) in tags" :key="`tag-${index}`">
                        <input v-model="form.tags" type="checkbox" :id="tag.id" name='tags[]' :value="tag.id" class="hidden peer" />
                        <label :for="tag.id" class="select-none bg-gray-500 cursor-pointer flex items-center justify-center rounded-lg
                                py-3 px-6 font-bold transition-colors duration-200 ease-in-out peer-checked:bg-blue-500  ">
                                <span>{{ tag.name }}</span>
                        </label>
                    </li>
                </ul>

                <!-- Create Recipe -->
                <button class='bg-gray-200 dark:bg-gray-800 p-2' type='submit' :disabled="form.processing">Create Recipe</button>
            </form>
        </div>

    </RecipeLayout>
</template>
