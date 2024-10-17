import { createRouter, createWebHistory } from 'vue-router';
import Home from '../Pages/Home.vue';
import Schedule from '../Pages/Agendar.vue';
import Consult from '../Pages/Consultar.vue';

const routes = [
  { path: '/', component: Home, name: 'home' },
  { path: '/agendar', component: Schedule, name: 'schedule' },
  { path: '/consultar', component: Consult, name: 'consult' },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
