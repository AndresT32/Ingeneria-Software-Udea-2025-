<template>
  <div class="container">
    <div class="card">
      <div class="card-header">Formulario para crear responsable nuevo</div>
      <div class="card-body">
        <form v-on:submit.prevent="agregarRegistro">
          
          <div class="row mb-1">
            <label for="Cedula">Cédula</label>
            <input 
              type="text" 
              required 
              class="form-control" 
              id="Cedula" 
              name="Cedula" 
              v-model="paciente.Cedula">
          </div>

          <div class="row mb-1">
            <label for="Nombre">Nombre</label>
            <input 
              type="text" 
              required 
              class="form-control" 
              id="Nombre" 
              name="Nombre" 
              v-model="paciente.Nombre">
          </div>

          <div class="row mb-1">
            <label for="Apellido">Apellido</label>
            <input 
              type="text" 
              required 
              class="form-control" 
              id="Apellido" 
              name="Apellido" 
              v-model="paciente.Apellido">
          </div>

          <div class="row mb-1">
            <label for="Cargo">Cargo</label>
            <input 
              type="text" 
              class="form-control" 
              id="Cargo" 
              name="Cargo" 
              v-model="paciente.Cargo">
          </div>

          <div class="row mb-1">
            <label for="Telefono">Teléfono</label>
            <input 
              type="text" 
              class="form-control" 
              id="Telefono" 
              name="Telefono" 
              v-model="paciente.Telefono">
          </div>

          <div class="btn-group" role="group">
            <button type="submit" class="btn btn-success">Guardar</button>
            <router-link :to="{name:'ResponsableView'}" class="btn btn-warning">Cancelar</router-link>
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
      paciente: {
        Cedula: "",
        Nombre: "",
        Apellido: "",
        Cargo: "",
        Telefono: ""
      }
    }
  },
  methods: {
    agregarRegistro() {
      let datosEnviar = {
        Cedula: this.paciente.Cedula,
        Nombre: this.paciente.Nombre,
        Apellido: this.paciente.Apellido,
        Cargo: this.paciente.Cargo,
        Telefono: this.paciente.Telefono
      };

      fetch("http://localhost/practica1_sgt/apis/responsable.php?insertar=1", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datosEnviar)
      })
      .then(respuesta => respuesta.json())
      .then(datoRespuesta => {
        console.log(datoRespuesta);
        this.$router.push({ name: "ResponsableView" })
      })
      .catch(error => console.log(error));
    }
  }
}
</script>
