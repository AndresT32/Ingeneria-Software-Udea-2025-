<template>
  <div class="home-container">
    <!-- Encabezado de bienvenida -->
    <div class="welcome-card">
      <img src="../assets/Health.jpg" alt="Logo Inventario" class="logo" />
      <h1>Bienvenido al Sistema de Inventario</h1>
      <p>
        Desde este panel puedes gestionar el registro, ubicación y responsables de los equipos médicos.
      </p>
    </div>

    <!-- Tarjetas de acceso rápido -->
    <div class="modules">
  <!-- Equipos Médicos -->
  <div
    class="module-card"
    @click="selectModuleAndNavigate('Equipo', '/EquiposMedicosView')"
    :class="{ active: selectedModule === 'Equipo' }"
  >
    <i class="fas fa-stethoscope"></i>
    <h3>Equipos Médicos</h3>
    <p>Consulta, agrega o modifica los equipos registrados.</p>
  </div>

  <!-- Responsables -->
  <div
    class="module-card"
    @click="selectModuleAndNavigate('Responsable', '/ResponsableView')"
    :class="{ active: selectedModule === 'Responsable' }"
  >
    <i class="fas fa-user-md"></i>
    <h3>Responsables</h3>
    <p>Administra la información del personal encargado de los equipos.</p>
  </div>

  <!-- Ubicaciones -->
  <div
    class="module-card"
    @click="selectModuleAndNavigate('Ubicacion', '/UbicacionView')"
    :class="{ active: selectedModule === 'Ubicacion' }"
  >
    <i class="fas fa-map-marker-alt"></i>
    <h3>Ubicaciones</h3>
    <p>Gestiona las áreas y lugares donde se encuentran los equipos médicos.</p>
  </div>

    </div>
  </div>
</template>

<script>
export default {
  name: "HomeView",

  data() {
    return {
      loggedIn: !!localStorage.getItem("usuario"),
      selectedModule: null,
    };
  },

  methods: {
    selectModuleAndNavigate(modulo, route) {
      if (!this.loggedIn) {
        this.$router.push("/LoginU");
        return;
      }
      this.selectedModule = modulo;
      this.$router.push(route);
    },
  },
};
</script>

<style scoped>
.home-container {
  text-align: center;
  padding: 50px 20px;
}

.welcome-card {
  background-color: #ffffff;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  margin-bottom: 40px;
}

.logo {
  width: 90px;
  margin-bottom: 20px;
}

.modules {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 25px;
}

.module-card {
  width: 250px;
  background-color: #f5f7fb;
  border-radius: 12px;
  padding: 20px;
  cursor: pointer;
  transition: 0.3s;
  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.1);
}

.module-card:hover {
  background-color: #eaf3ff;
  transform: translateY(-5px);
}

.module-card i {
  font-size: 40px;
  color: #3b82f6;
  margin-bottom: 10px;
}

.module-card h3 {
  color: #1f2937;
  margin-bottom: 8px;
}

.module-card p {
  color: #6b7280;
  font-size: 14px;
}
</style>

