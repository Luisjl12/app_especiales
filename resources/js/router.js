import {createRouter, createWebHistory} from 'vue-router';
import { sesion, cargarSesion } from './sesion';
import Dashboard from './components/Dashboard.vue';
import AdminUsuarios from './components/AdminUsuarios.vue';
import Proyectos from './components/Proyectos.vue';
import Planteles from './components/Planteles.vue';
import Municipios from './components/Municipios.vue'; 
import Localidades from './components/Localidades.vue'
const routes=[
    {path: '/', component: Dashboard},
    {path: '/usuarios', component: AdminUsuarios, meta: { admin: true }},
    {path: '/proyectos', component: Proyectos},
    {path: '/:pathMatch(.*)*', redirect: '/'},
    {path: '/planteles', component: Planteles, meta: {admin : true}}, 
    {path: '/municipios', component: Municipios, meta: { admin: true, titulo: 'Municipios' } },
    {path: '/localidades', component: Localidades, meta: {admin: true, titulo: 'localidades'} }, 
]; 

const router = createRouter({
    history: createWebHistory(),
    routes, 
}); 

router.beforeEach(async (to) => {
    await cargarSesion();
    if (to.meta.admin && !sesion.usuario?.es_admin) return '/';
}); 

export default router;
