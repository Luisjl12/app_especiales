import { reactive } from 'vue';
import { api } from './api';

export const sesion = reactive({ usuario: null, cargado: false });

export async function cargarSesion() {
  if (sesion.cargado) return;
  try {
    const datos = await api('/usuario');
    sesion.usuario = datos.usuario;
  } catch (e) {
    console.error('Error al cargar la sesión', e);
    sesion.usuario = null;
  } finally {
    sesion.cargado = true;
  }
}
