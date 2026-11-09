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

      <p class="grupo">Datos generales</p>
      <div class="rejilla">
        <label>CCT *
          <input v-model="form.cct" class="mayus" placeholder="21DPR0001K" maxlength="10" required />
        </label>
        <label>Nombre de la escuela *
          <input v-model="form.nombre_escuela" required />
        </label>
        <label>Nivel educativo
          <input v-model="form.nivel_educativo" list="lista-niveles" maxlength="100" />
        </label>
        <label>Turno
          <input v-model="form.turno" list="lista-turnos" maxlength="50" />
        </label>
        <label>Sostenimiento
          <input v-model="form.sostenimiento" list="lista-sostenimientos" maxlength="100" />
        </label>
        <label>CORDE
          <select v-model="form.corde_id">
            <option :value="null">-- Sin CORDE --</option>
            <option v-for="c in cordes" :key="c.id" :value="c.id">{{ c.clave_corde }} - {{ c.nombre_corde }}</option>
          </select>
        </label>
      </div>

      <p class="grupo">Ubicación</p>
      <div class="rejilla">
        <label>Municipio
          <select v-model="form.municipio_id" @change="form.localidad_id = null">
            <option :value="null">-- Municipio --</option>
            <option v-for="m in municipios" :key="m.id" :value="m.id">{{ m.nombre }}</option>
          </select>
        </label>
        <label>Localidad
          <select v-model="form.localidad_id" :disabled="!form.municipio_id">
            <option :value="null">-- Localidad --</option>
            <option v-for="l in localidadesDelMunicipio" :key="l.id" :value="l.id">{{ l.nombre }}</option>
          </select>
        </label>
        <label>Calle y número
          <input v-model="form.domicilio_calle_numero" maxlength="255" />
        </label>
        <label>Colonia
          <input v-model="form.domicilio_colonia" maxlength="255" />
        </label>
        <label>Código postal
          <input v-model="form.domicilio_cp" maxlength="5" inputmode="numeric" placeholder="75700" />
        </label>
      </div>

      <p class="grupo">Contacto</p>
      <div class="rejilla">
        <label>Teléfono
          <input v-model="form.telefono_plantel" maxlength="50" />
        </label>
        <label>Correo institucional
          <input v-model="form.correo_institucional" type="email" maxlength="255" />
        </label>
      </div>

      <!-- Sugerencias para los campos con lista -->
      <datalist id="lista-niveles">
        <option v-for="n in niveles" :key="n" :value="n" />
      </datalist>
      <datalist id="lista-turnos">
        <option v-for="t in turnos" :key="t" :value="t" />
      </datalist>
      <datalist id="lista-sostenimientos">
        <option v-for="s in sostenimientos" :key="s" :value="s" />
      </datalist>

      <div class="botones">
        <button type="submit" :disabled="guardando">{{ guardando ? 'Guardando...' : 'Guardar' }}</button>
        <button type="button" class="gris" @click="formulario = false">Cancelar</button>
      </div>
    </form>

    <input v-model="buscar" class="buscar" placeholder="Buscar por CCT, nombre, nivel, municipio, localidad o CORDE..." />

    <p v-if="cargando">Cargando...</p>
    <p v-else-if="!filtrados.length" class="vacio">No hay planteles.</p>

    <div v-else class="tabla-scroll">
      <table>
        <thead>
          <tr>
            <th>CCT</th>
            <th>Nombre de la escuela</th>
            <th>Nivel</th>
            <th>Turno</th>
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
            <td>{{ p.nivel_educativo || '—' }}</td>
            <td>{{ p.turno || '—' }}</td>
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
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { api } from '../api';

// Sugerencias (puedes cambiarlas o agregar más)
const niveles = ['Inicial', 'Preescolar', 'Primaria', 'Secundaria', 'Media superior', 'Superior', 'Educación especial'];
const turnos = ['Matutino', 'Vespertino', 'Nocturno', 'Discontinuo', 'Tiempo completo'];
const sostenimientos = ['Federal', 'Estatal', 'Federalizado', 'Particular', 'Autónomo'];

// Campos del formulario (se usa para crear el form vacío y para editar)
const CAMPOS = [
  'cct', 'nombre_escuela', 'turno', 'nivel_educativo', 'sostenimiento',
  'domicilio_calle_numero', 'domicilio_colonia', 'domicilio_cp',
  'telefono_plantel', 'correo_institucional', 'localidad_id', 'corde_id',
];

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
    [p.cct, p.nombre_escuela, p.nivel_educativo, p.turno,
     p.localidad?.nombre, p.localidad?.municipio?.nombre,
     p.corde?.nombre_corde, p.corde?.clave_corde]
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
  const vacio = { id: null, municipio_id: null };
  CAMPOS.forEach(c => (vacio[c] = c.endsWith('_id') ? null : ''));
  form.value = vacio;
  error.value = exito.value = '';
  formulario.value = true;
}

function editar(p) {
  const datos = { id: p.id, municipio_id: p.localidad?.municipio_id ?? null };
  CAMPOS.forEach(c => (datos[c] = p[c] ?? (c.endsWith('_id') ? null : '')));
  form.value = datos;
  error.value = exito.value = '';
  formulario.value = true;
  window.scrollTo({ top: 0, behavior: 'smooth' });
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
.form { background: #f8f8f8; padding: 16px; border-radius: 8px; margin: 12px 0; }
.grupo { font-size: 13px; font-weight: bold; color: #42b883; margin: 14px 0 6px; text-transform: uppercase; }
.rejilla { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 0 14px; }
label { display: block; font-size: 13px; color: #555; }
.form input, .form select, .buscar { width: 100%; box-sizing: border-box; padding: 8px; margin: 4px 0 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
.form select { cursor: pointer; }
.form select:disabled { background: #eee; cursor: not-allowed; }
.mayus { text-transform: uppercase; }
.buscar { margin-top: 12px; }
.botones { display: flex; gap: 8px; margin-top: 6px; }
.tabla-scroll { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; font-size: 14px; }
th, td { padding: 8px; border-bottom: 1px solid #eee; text-align: left; }
.acciones { white-space: nowrap; text-align: right; }
.vacio { color: #777; text-align: center; }
.exito { background: #e6f7ef; color: #2d8a5f; padding: 8px; border-radius: 6px; margin: 10px 0; font-size: 14px; }
</style>
