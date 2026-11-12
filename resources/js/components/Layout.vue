<template>
  <div class="layout">
    <aside class="menu">
      <h2>App Especiales</h2>
      <router-link to="/">Inicio</router-link>
      <router-link to="/proyectos">Proyectos</router-link>
      <router-link v-if="sesion.usuario.es_admin" to="/usuarios">Usuarios</router-link>
      <router-link v-if="sesion.usuario.es_admin" to="/planteles">Planteles</router-link>
      <router-link v-if="sesion.usuario.es_admin" to="/municipios">Municipios</router-link>
      <router-link v-if="sesion.usuario.es_admin" to="/localidades">Localidades</router-link>
    </aside>

    <div class="contenido">
      <header>
        <span>{{ sesion.usuario.name }} · {{ sesion.usuario.es_admin ? 'Administrador' : 'Usuario' }}</span>
        <button @click="salir">Cerrar sesión</button>
      </header>

      <main>
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { api, setToken } from '../api';
import { sesion } from '../sesion';

const router = useRouter();

async function salir() {
  try {
    const datos = await api('/logout', 'POST');
    setToken(datos.token);
  } finally {
    sesion.usuario = null;
    router.push('/');
  }
}
</script>

<style scoped>
.layout { display: flex; min-height: 100vh; }
.menu { width: 220px; background: #2c3e50; color: #fff; padding: 20px; box-sizing: border-box; }
.menu h2 { font-size: 18px; margin: 0 0 20px; }
.menu a { display: block; color: #cfd8dc; text-decoration: none; padding: 10px; border-radius: 6px; margin-bottom: 4px; }
.menu a:hover { background: #34495e; }
.menu a.router-link-exact-active { background: #42b883; color: #fff; }
.contenido { flex: 1; display: flex; flex-direction: column; }
header { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 12px 24px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
header button { background: #e74c3c; color: #fff; border: 0; padding: 8px 14px; border-radius: 6px; cursor: pointer; }
main { padding: 24px; }
</style>