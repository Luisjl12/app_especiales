<template>
  <p v-if="!sesion.cargado" class="centro">Cargando...</p>
  <Login v-else-if="!sesion.usuario" @entro="sesion.usuario = $event" />
  <Layout v-else />
</template>

<script setup>
import { onMounted } from 'vue';
import { sesion, cargarSesion } from '../sesion';
import Login from './Login.vue';
import Layout from './Layout.vue';

onMounted(cargarSesion);
</script>

<style>
.centro { text-align: center; margin-top: 80px; color: #777; }
.caja { max-width: 360px; margin: 80px auto; background: #fff; padding: 28px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
.caja h1 { margin-top: 0; text-align: center; }
.caja input[type=email], .caja input[type=password] { width: 100%; box-sizing: border-box; padding: 10px; margin-bottom: 12px; border: 1px solid #ccc; border-radius: 6px; }
.caja button { width: 100%; padding: 10px; border: 0; border-radius: 6px; background: #42b883; color: #fff; font-size: 15px; cursor: pointer; }
.caja button:disabled { opacity: .6; }
.error { background: #fde8e8; color: #c0392b; padding: 8px; border-radius: 6px; margin-bottom: 12px; font-size: 14px; }
</style>
