<template>
  <h1>Bienvenido, {{ sesion.usuario.name }}</h1>

  <div class="tarjetas">
    <router-link v-for="s in secciones" :key="s.ruta" :to="s.ruta" class="tarjeta">
      <span class="icono">{{ s.icono }}</span>
      <strong>{{ s.titulo }}</strong>
      <small>{{ s.descripcion }}</small>
    </router-link>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { sesion } from '../sesion';

// Aquí agregas tus secciones
const todas = [
  { ruta: '/proyectos', titulo: 'Proyectos', descripcion: 'Ver y administrar proyectos'},
  { ruta: '/usuarios',  titulo: 'Usuarios',  descripcion: 'Gestión de usuarios y roles',  admin: true },
  { ruta: '/planteles', titulo: 'Planteles', descripcion: 'Gestión de planteles y CCT',  admin: true },
];

const secciones = computed(() =>
  todas.filter(s => !s.admin || sesion.usuario.es_admin)
);
</script>

<style scoped>
h1 { margin-top: 0; }
.tarjetas { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
.tarjeta { background: #fff; border-radius: 10px; padding: 20px; text-decoration: none; color: #333;
           box-shadow: 0 2px 8px rgba(0,0,0,.06); display: flex; flex-direction: column; gap: 6px; transition: transform .15s; }
.tarjeta:hover { transform: translateY(-3px); }
.icono { font-size: 28px; }
small { color: #777; }
</style>
