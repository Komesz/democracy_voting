<script setup>
import {ref, onMounted} from 'vue';
import {useRoute} from 'vue-router';
import axios from 'axios';

const route = useRoute();
const pollId = route.params.id;
const partyToken = route.params.partyToken;

const poll = ref({});
const party = ref({});
const choices = ref([]);
const results = ref({});
const voted = ref(false);

const fetchParty = async () => {
    const response = await axios.get(`/api/parties/${partyToken}`);
    party.value = response.data;
};

const fetchPoll = async () => {
    const response = await axios.get(`/api/polls/${pollId}`);
    poll.value = response.data;
    choices.value = poll.value.choices;
    results.value = poll.value.results;
};

const submitVote = async (choice) => {
    await axios.post(`/api/polls/${pollId}/vote`, {choice, partyToken});
    voted.value = true;
    window.localStorage.setItem(`voted-${pollId}`, true)
};

onMounted(() => {
    fetchParty();
    voted.value = window.localStorage.getItem(`voted-${pollId}`) ?? false;

    fetchPoll();
    window.Echo.channel(`poll.${pollId}`).listen('VoteReceived', (vote) => {
        if (results.value[vote.choice]) {
            results.value[vote.choice] += vote.mandates;
        } else {
            results.value[vote.choice] = 1;
        }
    });

    window.Echo.channel(`polls`).listen('PollActiveChanged', (poll) => {
        fetchPoll()
    });
});
</script>

<template>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-lg">
        <h1 class="text-5xl font-semibold mb-6">
            {{ party.name }}
            <span class="text-2xl text-gray-400">Mandátumok: ({{ party.mandates }})</span>
        </h1>

        <h2 class="text-3xl font-semibold text-gray-800 mb-6">{{ poll.title }}</h2>

        <div v-if="poll.active !== 1" class="mt-6 text-center">
            <h3 class="text-2xl text-red-600 font-semibold">A szavazás már lezárult, nem szavazhatsz!</h3>
        </div>
        <div v-else-if="!voted" class="space-y-4">
            <h3 class="text-xl font-medium text-gray-700">Szavazok:</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button
                    v-for="choice in choices"
                    :key="choice"
                    @click="submitVote(choice)"
                    class="w-full py-3 px-6 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition"
                >
                    {{ choice }}
                </button>
            </div>
        </div>
        <div v-else class="mt-6 text-center">
            <h3 class="text-2xl text-green-600 font-semibold">Sikeres szavazat leadás!</h3>
        </div>

        <div class="mt-6">
            <h3 class="text-xl font-medium text-gray-700 mb-3">Eredmények:</h3>
            <ul class="space-y-2">
                <li v-for="(count, choice) in results" :key="choice"
                    class="flex justify-between items-center py-2 px-4 bg-gray-100 rounded-lg shadow-sm">
                    <span class="text-gray-800 font-medium">{{ choice }}</span>
                    <span class="text-gray-600">{{ count }} szavazat</span>
                </li>
            </ul>
        </div>

        <div class="mt-6">
            <router-link :to="`/${partyToken}`"
                         class="mt-5 px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition ease-in-out duration-150">
                Vissza
            </router-link>
        </div>
    </div>
</template>
