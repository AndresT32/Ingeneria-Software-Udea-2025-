<template>
  <div class="container">
    <div class="card">
      <div class="card-header">Formulario para crear equipo médico nuevo</div>
      <div class="card-body">
        <form v-on:submit.prevent="agregarEquipo">

          <div class="row mb-1">
            <label for="Num_activo">Número de Activo</label>
            <input 
              type="text" 
              required 
              class="form-control" 
              id="Num_activo" 
              v-model="equipo.Num_activo">
          </div>

          <div class="row mb-1">
            <label for="Marca">Marca</label>
            <input 
              type="text" 
              required 
              class="form-control" 
              id="Marca" 
              v-model="equipo.Marca">
          </div>

          <div class="row mb-1">
            <label for="Modelo">Modelo</label>
            <input 
              type="text" 
              required 
              class="form-control" 
              id="Modelo" 
              v-model="equipo.Modelo">
          </div>

          <!-- Lista desplegable de ubicaciones -->
          <div class="row mb-1">
            <label for="Codigo_ubi">Código de Ubicación</label>
            <select 
              class="form-control" 
              id="Codigo_ubi" 
              v-model="equipo.Codigo_ubi">
              <option value="">Seleccione una ubicación</option>
              <option v-for="ubi in ubicaciones" :key="ubi.Codigo_ubi" :value="ubi.Codigo_ubi">
                {{ ubi.Codigo_ubi }} - {{ ubi.Nombre_ubi }}
              </option>
            </select>
          </div>

          <!-- Lista desplegable de responsables -->
          <div class="row mb-1">
            <label for="Codigo_Resp">Código de Responsable</label>
            <select 
              class="form-control" 
              id="Codigo_Resp" 
              v-model="equipo.Codigo_Resp">
              <option value="">Seleccione un responsable</option>
              <option v-for="resp in responsables" :key="resp.Codigo_Resp" :value="resp.Codigo_Resp">
                {{ resp.Codigo_Resp }} - {{ resp.Nombre }} {{ resp.Apellido }}
              </option>
            </select>
          </div>

          <div class="btn-group" role="group">
            <button type="submit" class="btn btn-success">Guardar</button>
            <router-link :to="{name:'EquiposMedicosView'}" class="btn btn-warning">Cancelar</router-link>
          </div>
        </form>
      </div>
    </div> 
  </div>
</template>

<script>
export default {
  data() {
    return {
      equipo: {
        Num_activo: "",
        Marca: "",
        Modelo: "",
        Codigo_ubi: "",
        Codigo_Resp: ""
      },
      ubicaciones: [],
      responsables: []
    }
  },
  methods: {
    agregarEquipo() {
      let datosEnviar = { ...this.equipo };

      fetch("http://localhost/practica1_sgt/apis/equiposmedicos.php?insertar=1", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datosEnviar)
      })
      .then(respuesta => respuesta.json())
      .then(datoRespuesta => {
        console.log(datoRespuesta);
        if (datoRespuesta.success == 1) {
          this.$router.push({ name: "EquiposMedicosView" })
        } else {
          alert("Error al guardar el equipo: " + (datoRespuesta.error || "verifica los datos."));
        }
      })
      .catch(error => console.log(error));
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
    }
  },
  mounted() {
    this.cargarListas();
  }
}
</script>
