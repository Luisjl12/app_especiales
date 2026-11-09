<template>
  <div class="seccion">
    <div class="encabezado">
      <h2>Planteles</h2>
      <button v-if="!formulario" @click="nuevo">+ Nuevo plantel</button>
    </div>

    <div v-if="error" class="error">{{ error }}</div>
    <div v-if="exito" class="exito">{{ exito }}</div>

    <form v-if="formulario" class="form" @submit.prevent="guardar">
      <h3>{{ form.id ? 'Editar plantel' : 'Nuevo plantel' }}</h3>
      <input v-model="form.cct" class="mayus" placeholder="CCT (ej. 21DPR0001K)" maxlength="10" required />
      <input v-model="form.nombre_escuela" placeholder="Nombre de la escuela" required />

      <select v-model="form.municipio_id" @change="form.localidad_id = null">
        <option :value="null">-- Municipio --</option>
        <option v-for="m in municipios" :key="m.id" :value="m.id">{{ m.nombre }}</option>
      </select>

      <select v-model="form.localidad_id" :disabled="!form.municipio_id">
        <option :value="null">-- Localidad --</option>
        <option v-for="l in localidadesDelMunicipio" :key="l.id" :value="l.id">{{ l.nombre }}</option>
      </select>

      <select v-model="form.corde_id">
        <option :value="null">-- CORDE --</option>
        <option v-for="c in cordes" :key="c.id" :value="c.id">{{ c.clave_corde }} - {{ c.nombre_corde }}</option>
      </select>

      <div class="botones">
        <button type="submit" :disabled="guardando">{{ guardando ? 'Guardando...' : 'Guardar' }}</button>
        <button type="button" class="gris" @click="formulario = false">Cancelar</button>
      </div>
    </form>

    <input v-model="buscar" class="buscar" placeholder="Buscar por CCT, nombre, municipio, localidad o CORDE..." />

    <p v-if="cargando">Cargando...</p>
    <p v-else-if="!filtrados.length" class="vacio">No hay planteles.</p>

    <table v-else>
      <thead>
        <tr>
          <th>CCT</th>
          <th>Nombre de la escuela</th>
          <th>Municipio</th>
          <th>Localidad</th>
          <th>CORDE</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in filtrados" :key="p.id">
          <td><strong>{{ p.cct }}</strong></td>
          <td>{{ p.nombre_escuela }}</td>
          <td>{{ p.localidad?.municipio?.nombre || '—' }}</td>
          <td>{{ p.localidad?.nombre || '—' }}</td>
          <td>{{ p.corde?.nombre_corde || '—' }}</td>
          <td class="acciones">
            <button class="chico" @click="editar(p)">Editar</button>
            <button class="chico rojo" @click="eliminar(p)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { api } from '../api';

const planteles = ref([]);
const municipios = ref([]);
const cordes = ref([]);
const cargando = ref(true);
const guardando = ref(false);
const error = ref('');
const exito = ref('');
const formulario = ref(false);
const form = ref({});
const buscar = ref('');

// Solo las localidades del municipio elegido
const localidadesDelMunicipio = computed(() => {
  const m = municipios.value.find(m => m.id === form.value.municipio_id);
  return m ? m.localidades : [];
});

const filtrados = computed(() => {
  const t = buscar.value.toLowerCase();
  return planteles.value.filter(p =>
    [p.cct, p.nombre_escuela, p.localidad?.nombre, p.localidad?.municipio?.nombre, p.corde?.nombre_corde, p.corde?.clave_corde]
      .some(v => (v || '').toLowerCase().includes(t))
  );
});

function mensajeError(e, porDefecto) {
  if (e.errors) return Object.values(e.errors)[0][0];
  return e.mensaje || e.message || porDefecto;
}

async function cargar() {
  try {
    const datos = await api('/admin/planteles');
    planteles.value = datos.planteles;
    municipios.value = datos.municipios;
    cordes.value = datos.cordes;
  } catch (e) {
    error.value = mensajeError(e, 'No se pudieron cargar los planteles');
  } finally {
    cargando.value = false;
  }
}

onMounted(cargar);

function nuevo() {
  form.value = {
    id: null,
    cct: '',
    nombre_escuela: '',
    municipio_id: null,
    localidad_id: null,
    corde_id: null,
  };
  error.value = exito.value = '';
  formulario.value = true;
}

function editar(p) {
  form.value = {
    id: p.id,
    cct: p.cct,
    nombre_escuela: p.nombre_escuela,
    municipio_id: p.localidad?.municipio_id ?? null,
    localidad_id: p.localidad_id,
    corde_id: p.corde_id,
  };
  error.value = exito.value = '';
  formulario.value = true;
}

async function guardar() {
  error.value = exito.value = '';
  guardando.value = true;
  try {
    if (form.value.id) {
      await api(`/admin/planteles/${form.value.id}`, 'PUT', form.value);
      exito.value = 'Plantel actualizado';
    } else {
      await api('/admin/planteles', 'POST', form.value);
      exito.value = 'Plantel creado';
    }
    formulario.value = false;
    await cargar();
  } catch (e) {
    error.value = mensajeError(e, 'No se pudo guardar');
  } finally {
    guardando.value = false;
  }
}

async function eliminar(p) {
  if (!confirm(`¿Eliminar el plantel ${p.cct} - ${p.nombre_escuela}?`)) return;
  error.value = exito.value = '';
  try {
    await api(`/admin/planteles/${p.id}`, 'DELETE');
    exito.value = 'Plantel eliminado';
    await cargar();
  } catch (e) {
    error.value = mensajeError(e, 'No se pudo eliminar');
  }
}
</script>

<style scoped>
.seccion { background: #fff; padding: 20px; border-radius: 10px; }
.encabezado { display: flex; justify-content: space-between; align-items: center; }
h2 { font-size: 18px; margin: 0; }
h3 { margin-top: 0; font-size: 16px; }
button { border: 0; border-radius: 6px; background: #42b883; color: #fff; padding: 8px 14px; font-size: 14px; cursor: pointer; }
button:hover { opacity: .9; }
button:disabled { opacity: .6; cursor: not-allowed; }
.gris { background: #999; }
.rojo { background: #e74c3c; }
.chico { padding: 4px 10px; font-size: 12px; margin-left: 4px; }
.form { background: #f8f8f8; padding: 14px; border-radius: 8px; margin: 12px 0; }
.form input, .form select, .buscar { width: 100%; box-sizing: border-box; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 6px; }
.form select { cursor: pointer; }
.form select:disabled { background: #eee; cursor: not-allowed; }
.mayus { text-transform: uppercase; }
.buscar { margin-top: 12px; }
.botones { display: flex; gap: 8px; }
table { width: 100%; border-collapse: collapse; font-size: 14px; }
th, td { padding: 8px; border-bottom: 1px solid #eee; text-align: left; }
.acciones { white-space: nowrap; text-align: right; }
.vacio { color: #777; text-align: center; }
.exito { background: #e6f7ef; color: #2d8a5f; padding: 8px; border-radius: 6px; margin: 10px 0; font-size: 14px; }
</style>
