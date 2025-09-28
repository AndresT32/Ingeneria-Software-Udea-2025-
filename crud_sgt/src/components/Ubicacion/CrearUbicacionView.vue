<template>
  <div class="container">
    <div class="card">
      <div class="card-header">Formulario para crear ubicación nueva</div>
      <div class="card-body">
        <form @submit.prevent="agregarUbicacion">
          
          <div class="row mb-1">
            <label for="Nombre_ubi">Nombre de la Ubicación *</label>
            <input 
              type="text" 
              required 
              class="form-control" 
              id="Nombre_ubi" 
              v-model="ubicacion.Nombre_ubi">
          </div>

          <div class="row mb-1">
            <label for="Ubicacion">Ubicación física *</label>
            <input 
              type="text" 
              required
              class="form-control" 
              id="Ubicacion" 
              v-model="ubicacion.Ubicacion">
          </div>

          <div class="row mb-1">
            <label for="Telefono">Teléfono</label>
            <input 
              type="text" 
              class="form-control" 
              id="Telefono" 
              v-model="ubicacion.Telefono">
          </div>

          <div class="btn-group" role="group">
            <button type="submit" class="btn btn-success">Guardar</button>
            <router-link :to="{name:'UbicacionView'}" class="btn btn-warning">Cancelar</router-link>
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
      ubicacion: {
        Nombre_ubi: "",
        Ubicacion: "",
        Telefono: ""
      }
    }
  },
  methods: {
    agregarUbicacion() {
      // Validar campos obligatorios
      if (!this.ubicacion.Nombre_ubi.trim() || !this.ubicacion.Ubicacion.trim()) {
        alert("Nombre y Ubicación son obligatorios");
        return;
      }

      let datosEnviar = { ...this.ubicacion }; // evitar referencias directas

      fetch("http://localhost/practica1_sgt/apis/ubicacion.php?insertar=1", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datosEnviar)
      })
      .then(res => res.json())
      .then(resp => {
        console.log(resp);

        if (resp.success) {
          // Reiniciar campos para evitar doble envío
          this.ubicacion = { Nombre_ubi: "", Ubicacion: "", Telefono: "" };
          this.$router.push({ name: "UbicacionView" });
        } else {
          alert("Error al guardar: " + (resp.error || "desconocido"));
        }
      })
      .catch(err => console.error(err));
    }
  }
}
</script>
