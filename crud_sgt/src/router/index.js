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

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/EditarResponsableView/:Codigo_Resp',
    name: 'EditarResponsableView',
    component: EditarResponsableView,
    props: true
  },
  {
    path: '/CrearResponsableView',
    name: 'CrearResponsableView',
    component: CrearResponsableView
  },
    {
    path: '/ResponsableView',
    name: 'ResponsableView',
    component: ResponsableView
  },
  {  
    path: '/CrearUbicacionView',
    name: 'CrearUbicacionView',
    component: CrearUbicacionView
  },
  {
    path: '/EditarUbicacionView/:Codigo_ubi',
    name: 'EditarUbicacionView',
    component: EditarUbicacionView,
    props: true
  },
  {
    path: '/UbicacionView',
    name: 'UbicacionView',
    component: UbicacionView
  },
  {
    path: '/CrearEquiposMedicosView',
    name: 'CrearEquiposMedicosView',
    component: CrearEquiposMedicosView
  },
  {
    path: '/EditarEquiposMedicosView/:ID_EM',
    name: 'EditarEquiposMedicosView',
    component: EditarEquiposMedicosView,
    props: true
  },
  {
    path: '/EquiposMedicosView',
    name: 'EquiposMedicosView',
    component: EquiposMedicosView
  },
  {
    path: '/about',
    name: 'about',
    component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
