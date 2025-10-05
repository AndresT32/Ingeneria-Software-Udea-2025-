<template>
  <div class="container">
    <div class="card">
      <div class="card-header">Editar Equipo Médico</div>
      <div class="card-body">
        <form @submit.prevent="actualizarRegistro">
          <!-- Código e ID (solo lectura) -->
          <div class="row mb-1">
            <label>Código (no editable)</label>
            <input type="text" class="form-control" :value="codigoEM" disabled>
          </div>
          <div class="row mb-1">
            <label>ID (no editable)</label>
            <input type="text" class="form-control" :value="equipo.ID_EM" disabled>
          </div>   

          <!-- Marca -->
          <div class="row mb-1">
            <label for="Marca">Marca</label>
            <input id="Marca" v-model="equipo.Marca" class="form-control">
          </div>

          <!-- Modelo -->
          <div class="row mb-1">
            <label for="Modelo">Modelo</label>
            <input id="Modelo" v-model="equipo.Modelo" class="form-control">
          </div>

          <!-- Lista desplegable de ubicaciones -->
          <div class="row mb-1">
            <label for="Codigo_ubi">Código de Ubicación</label>
            <select class="form-control" id="Codigo_ubi" v-model="equipo.Codigo_ubi">
              <option value="">Seleccione una ubicación</option>
              <option v-for="ubi in ubicaciones" :key="ubi.Codigo_ubi" :value="ubi.Codigo_ubi">
                {{ ubi.Codigo_ubi }} - {{ ubi.Nombre_ubi }}
              </option>
            </select>
          </div>

          <!-- Lista desplegable de responsables -->
          <div class="row mb-1">
            <label for="Codigo_Resp">Código Responsable</label>
            <select class="form-control" id="Codigo_Resp" v-model="equipo.Codigo_Resp">
              <option value="">Seleccione un responsable</option>
              <option v-for="resp in responsables" :key="resp.Codigo_Resp" :value="resp.Codigo_Resp">
                {{ resp.Codigo_Resp }} - {{ resp.Nombre }} {{ resp.Apellido }}
              </option>
            </select>
          </div>

          <!-- Botones -->
          <div class="btn-group" role="group">
            <button type="submit" class="btn btn-success">Guardar</button>
            <router-link :to="{ name: 'EquiposMedicosView' }" class="btn btn-warning">Cancelar</router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['ID_EM'],
  data() {
    return {
      equipo: {
        ID_EM: '',
        Codigo_ubi: '',
        Codigo_Resp: '',
        Marca: '',
        Modelo: '',
        Estado: ''
      },
      ubicaciones: [],
      responsables: []
    }
  },
  computed: {
    codigoEM() {
      return this.ID_EM || this.$route.params.ID_EM || '';
    }
  },
  created() {
    this.obtenerEquipoID();
    this.cargarListas();
  },
  methods: {
    obtenerEquipoID() {
      const codigo = encodeURIComponent(this.codigoEM);
      fetch(`http://localhost/practica1_sgt/apis/equiposMedicos.php?consultar=${codigo}`)
        .then(res => res.json())
        .then(data => {
          if (!data || data.success === 0) {
            alert('Equipo no encontrado');
            this.$router.push({ name: 'EquiposMedicosView' });
            return;
          }
          this.equipo = data;
        })
        .catch(err => {
          console.error(err);
          alert('Error al obtener datos del equipo médico');
        });
    },

    cargarListas() {
      // cargar ubicaciones
      fetch("http://localhost/practica1_sgt/apis/ubicacion.php")
        .then(r => r.json())
        .then(data => this.ubicaciones = data)
        .catch(err => console.log(err));

      // cargar responsables
      fetch("http://localhost/practica1_sgt/apis/responsable.php")
        .then(r => r.json())
        .then(data => this.responsables = data)
        .catch(err => console.log(err));
    },

    actualizarRegistro() {
      const codigoOld = this.codigoEM;
      const datosEnviar = {
        Codigo_ubi: this.equipo.Codigo_ubi,
        Codigo_Resp: this.equipo.Codigo_Resp,
        Marca: this.equipo.Marca,
        Modelo: this.equipo.Modelo,
        Estado: this.equipo.Estado
      };

      fetch(`http://localhost/practica1_sgt/apis/equiposMedicos.php?actualizar=${encodeURIComponent(codigoOld)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosEnviar)
      })
        .then(res => res.json())
        .then(resp => {
          if (resp.success) {
            alert('Actualización exitosa');
            this.$router.push({ name: 'EquiposMedicosView' });
          } else {
            alert('Error al actualizar: ' + (resp.error || 'desconocido'));
          }
        })
        .catch(err => {
          console.error(err);
          alert('Error en la petición de actualización');
        });
    }
  }
}
</script>
