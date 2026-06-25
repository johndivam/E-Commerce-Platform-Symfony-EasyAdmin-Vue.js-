import { createRouter, createWebHistory } from 'vue-router'
import Welcome from '../components/Welcome.vue'
import AboutUs from '../components/AboutUs.vue'
import Panel from '../components/client/Panel.vue'
import NotFound from '../components/NotFound.vue'
import Products from '../components/Products.vue'

const routes = [
  { path: '/', name: 'welcome', component: Welcome },
  { path: '/about', name: 'about', component: AboutUs },
  { path: '/products', name: 'products', component: Products },
  { path: '/user', name: 'user', component: Panel, meta: { requiresAuth: true } },
  { path: '/:pathMatch(.*)*', name: 'not-found', component: NotFound, },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const requiresAuth = to.meta.requiresAuth;
  const token = localStorage.getItem('token');

  if (requiresAuth && !token) {
    next({ name: 'welcome' });
  } else {
    next();
  }
});


export default router