<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const polls = ref([]);

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
});
</script>

<template>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-lg">
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">Szavazások:</h2>

        <div class="mt-6">
            <ul class="space-y-2">
                <li v-for="poll in polls" :key="poll.id" class="flex justify-between items-center py-2 px-4 bg-gray-100 rounded-lg shadow-sm">
                    <router-link :to="`/polls/${poll.id}`" class="w-full h-full text-gray-800 font-medium">
                        <div class="flex justify-between">
                            <span>{{ poll.title }}</span>
                            <span class="text-gray-400">{{ formatDate(poll.created_at) }}</span>
                        </div>
                    </router-link>
                </li>
            </ul>
        </div>
    </div>
</template>
