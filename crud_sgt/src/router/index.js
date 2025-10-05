import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import EditarResponsableView from '../components/Responsable/EditarResponsableView.vue'
import ResponsableView from '../components/Responsable/ResponsableView.vue'
import CrearResponsableView from '../components/Responsable/CrearResponsableView.vue'
import CrearUbicacionView from '../components/Ubicacion/CrearUbicacionView.vue'
import EditarUbicacionView from '../components/Ubicacion/EditarUbicacionView.vue'
import UbicacionView from '../components/Ubicacion/UbicacionView.vue'
import CrearEquiposMedicosView from '../components/EquiposMedicos/CrearEquiposMedicosView.vue'
import EditarEquiposMedicosView from '../components/EquiposMedicos/EditarEquiposMedicosView.vue'
import EquiposMedicosView from '../components/EquiposMedicos/EquiposMedicosView.vue'      
import LoginU from '@/components/Login/LoginU.vue'
import RegistrarU from '@/components/Login/RegistrarU.vue'
const routes = [
  {
    path: "/",
    name: "home",
    component: HomeView
  },
  {
    path: "/EditarResponsableView/:Codigo_Resp",
    name: "EditarResponsableView",
    component: EditarResponsableView,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: "/CrearResponsableView",
    name: "CrearResponsableView",
    component: CrearResponsableView,
    meta: { requiresAuth: true }
  },
  {
    path: "/ResponsableView",
    name: "ResponsableView",
    component: ResponsableView,
    meta: { requiresAuth: true }
  },
  {
    path: "/CrearUbicacionView",
    name: "CrearUbicacionView",
    component: CrearUbicacionView,
    meta: { requiresAuth: true }
  },
  {
    path: "/EditarUbicacionView/:Codigo_ubi",
    name: "EditarUbicacionView",
    component: EditarUbicacionView,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: "/UbicacionView",
    name: "UbicacionView",
    component: UbicacionView,
    meta: { requiresAuth: true }
  },
  {
    path: "/CrearEquiposMedicosView",
    name: "CrearEquiposMedicosView",
    component: CrearEquiposMedicosView,
    meta: { requiresAuth: true }
  },
  {
    path: "/EditarEquiposMedicosView/:ID_EM",
    name: "EditarEquiposMedicosView",
    component: EditarEquiposMedicosView,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: "/EquiposMedicosView",
    name: "EquiposMedicosView",
    component: EquiposMedicosView,
    meta: { requiresAuth: true }
  },
  {
    path: "/LoginU",
    name: "LoginU",
    component: LoginU
  },
  {
    path: "/RegistrarU",
    name: "RegistrarU",
    component: RegistrarU
  },

  {
    path: "/:catchAll(.*)",
    redirect: { name: "LoginU" }
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});

// 🔒 Guard de autenticación
router.beforeEach((to, from, next) => {
  const isLoggedIn = !!localStorage.getItem("usuario");

  // Si intenta entrar a login o registro y ya está logueado → redirigir a Home
  if ((to.path === "/LoginU" || to.path === "/RegistrarU") && isLoggedIn) {
    next("/");
    return;
  }

  // Si intenta entrar a una ruta protegida sin estar logueado → redirigir a Login
  const publicPages = ["/LoginU", "/RegistrarU"];
  const authRequired = !publicPages.includes(to.path);

  if (authRequired && !isLoggedIn) {
    next("/LoginU");
    return;
  }

  // En cualquier otro caso, permitir
  next();
});


export default router;