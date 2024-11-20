import { createRouter, createWebHistory } from 'vue-router';
import Home from '../Pages/Home.vue';
import Schedule from '../Pages/Agendar.vue';
import Consult from '../Pages/Consultar.vue';
import Users from '@/Pages/Users.vue';

const routes = [
  { path: '/', component: Home, name: 'login' },
  { path: '/home', component: Home, name: 'home' },
  { path: '/agendar', component: Schedule, name: 'schedule' },
  { path: '/consultar', component: Consult, name: 'consult' },
  { path: '/users', component: Users, name: 'users' },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
