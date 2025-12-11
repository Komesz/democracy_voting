import { createRouter, createWebHistory } from 'vue-router';
import PollList from '../components/PollList.vue';
import PollDetails from '../components/PollDetails.vue';
import AdminControls from '../components/AdminControls.vue';

const routes = [
    { path: '/:partyToken', component: PollList },
    { path: '/polls/:id/:partyToken', component: PollDetails },
    { path: '/admin/:token', component: AdminControls },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
