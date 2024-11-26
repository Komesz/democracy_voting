<script setup>
import {onMounted, ref} from 'vue';
import axios from 'axios';
import {useRoute} from "vue-router";

const route = useRoute();
const token = route.params.token;

const polls = ref([]);

const title = ref('');
const choices = ref(['Igen', 'Nem', 'Tartózkodik']); // Start with one empty choice

const addChoice = () => {
    choices.value.push('');
};

const removeChoice = (index) => {
    if (choices.value.length > 1) {
        choices.value.splice(index, 1);
    } else {
        alert('Legalább egy válaszlehetőség megadása kötelező.');
    }
};

const createPoll = async () => {
    if (!title.value.trim()) {
        alert('A szavazás címét meg kell adni.');
        return;
    }

    if (choices.value.some((choice) => !choice.trim())) {
        alert('Minden válaszlehetőség megadása kötelező.');
        return;
    }

    try {
        await axios.post('/api/polls', {
            title: title.value,
            choices: choices.value,
            token: token
        });

        // Reset form
        title.value = '';
        choices.value = ['Igen', 'Nem', 'Tartózkodik'];
        alert('Szavazás létrehozása sikeres!');
    } catch (error) {
        console.error(error);
        alert('Szavazás létrehozása SIKERTELEN!');
    }
};

const togglePoll = async (pollId) => {
    try {
        await axios.post(`/api/polls/${pollId}/toggle`, {
            token: token
        });

        fetchPolls();

        alert('Szavazás állapotváltozása sikeres!');
    } catch (error) {
        console.error(error);
        alert('Szavazás állapotváltozása SIKERTELEN!');
    }
}

const fetchPolls = async () => {
    const response = await axios.get('/api/polls');
    polls.value = response.data;
};

const formatDate = (date) => {
    return date.toString().replace(/(\d+)-(\d+)-(\d+)T(\d+):(\d+).*/, "$1-$2-$3 $4:$5");
}

onMounted(() => {
    fetchPolls();
    window.Echo.channel('polls').listen('NewPollCreated', (poll) => {
        polls.value.push(poll.poll);
    });
})
</script>

<template>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">Admin felület</h2>
        <form class="space-y-4" @submit.prevent="createPoll">
            <div>
                <label for="title">Szavazás címe:</label>
                <input
                    class="w-full px-4 py-2 mt-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition ease-in-out duration-150"
                    id="title" v-model="title" placeholder="Kérlek írd be a szavazás címét!" required/>
            </div>
            <div>
                <label>Válaszlehetőségek:</label>
                <ul>
                    <li v-for="(choice, index) in choices" :key="index" class="flex justify-between">
                        <input
                            class="w-full px-4 py-2 mt-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition ease-in-out duration-150"
                            type="text"
                            v-model="choices[index]"
                            placeholder="Kérlek írd be a válaszlehetőséget!"
                            required
                        />
                        <button
                            class="px-4 py-2 mt-2 ml-2 text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition ease-in-out duration-150"
                            type="button" @click="removeChoice(index)">X
                        </button>
                    </li>
                </ul>
            </div>
            <div class="flex justify-between">
                <button
                    class="mt-5 px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition ease-in-out duration-150"
                    type="button" @click="addChoice">Új válaszlehetőség hozzáadása
                </button>
                <button
                    class="mt-5 px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition ease-in-out duration-150"
                    type="submit">Szavazás létrehozása
                </button>
            </div>
        </form>

        <div class="mt-6">
            <h3 class="text-2xl font-semibold text-gray-800 mb-6">Eddigi szavazások:</h3>

            <ul class="space-y-2">
                <li v-for="poll in polls" :key="poll.id" class="flex justify-between items-center py-2 px-4 bg-gray-100 rounded-lg shadow-sm">
                    <router-link :to="`/polls/${poll.id}`" class="w-full h-full text-gray-800 font-medium">
                        <div class="flex justify-between">
                            <span>{{ poll.title }}</span>
                            <span class="text-gray-400">{{ formatDate(poll.created_at) }}</span>
                        </div>
                    </router-link>
                    <form @submit.prevent="togglePoll(poll.id)">
                        <button
                            class="px-4 py-2 ml-2 text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 transition ease-in-out duration-150"
                            type="submit">{{ poll.active === 1 ? 'Lezárom' : 'Újraaktiválom' }}
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</template>
