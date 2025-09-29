<template>
  <div id="app">
    <!-- Si estoy en login o registro, solo muestro esa vista -->
    <div v-if="isAuthPage && !isLoggedIn">
      <router-view />
    </div>

    <!-- Si estoy logueado o en home, muestro el layout -->
    <div v-else>
      <!-- Header -->
      <header class="topbar">
        <div class="topbar-left">
          <img src="logo.png" alt="Logo" class="logo-img" />
          <h1 class="topbar-title">Inventario</h1>
        </div>
        <div class="topbar-right">
          <router-link to="/LoginU" class="btn">Login</router-link>
          <button class="btn logout" @click="logout">Cerrar Sesión</button>
        </div>
      </header>

      <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
          <h2 class="logo">Inventario</h2>
          <ul class="menu">
            <li><router-link to="/">🏠 Home</router-link></li>
            <!-- Desplegable de Módulos -->
            <li>
              <div class="menu-item" @click="showModulo = !showModulo">
                📋 Módulo
                <span class="arrow">{{ showModulo ? "▲" : "▼" }}</span>
              </div>
              <ul v-if="showModulo" class="submenu">
                <li>
                  <button
                    @click="selectModuleAndNavigate('Responsable', '/ResponsableView')"
                    :class="{ active: selectedModule === 'Responsable' }"
                  >
                    👤 Responsables
                  </button>
                </li>
                <li>
                  <button
                    @click="selectModuleAndNavigate('Ubicacion', '/UbicacionView')"
                    :class="{ active: selectedModule === 'Ubicacion' }"
                  >
                    📍 Ubicaciones
                  </button>
                </li>
                <li>
                  <button
                    @click="selectModuleAndNavigate('Equipo', '/EquiposMedicosView')"
                    :class="{ active: selectedModule === 'Equipo' }"
                  >
                    🩺 Equipos Médicos
                  </button>
                </li>
              </ul>
            </li>

            <!-- Botones Crear y Editar -->
            <li class="actions">
              <button :disabled="!selectedModule" @click="irCrear(selectedModule)">
                ➕ Crear {{ selectedModule || "" }}
              </button>
              <button :disabled="!selectedModule" @click="irEditar(selectedModule)">
                ✏️ Editar {{ selectedModule || "" }}
              </button>
            </li>
            <li><router-link to="/about">ℹ️ About</router-link></li>
          </ul>
        </aside>

        <!-- Contenido principal -->
        <main class="content">
          <router-view />
        </main>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedModule: null,
      showModulo: false,
    };
  },
  computed: {
    isAuthPage() {
      return this.$route.path === "/LoginU" || this.$route.path === "/RegistrarU";
    },
    isLoggedIn() {
      return !!localStorage.getItem("usuario");
    },
  },
  methods: {
    selectModuleAndNavigate(modulo, route) {
      if (!this.isLoggedIn) {
        this.$router.push("/LoginU");
        return;
      }
      this.selectedModule = modulo;
      this.$router.push(route);
    },
    irCrear(modulo) {
      if (!this.isLoggedIn) {
        this.$router.push("/LoginU");
        return;
      }
      switch (modulo) {
        case "Responsable":
          this.$router.push("/CrearResponsableView");
          break;
        case "Ubicacion":
          this.$router.push("/CrearUbicacionView");
          break;
        case "Equipo":
          this.$router.push("/CrearEquiposMedicosView");
          break;
      }
    },
    irEditar(modulo) {
      if (!this.isLoggedIn) {
        this.$router.push("/LoginU");
        return;
      }
      const id = prompt(`Ingresa el ID del ${modulo} a editar:`);
      if (!id) return;
      switch (modulo) {
        case "Responsable":
          this.$router.push(`/EditarResponsableView/${id}`);
          break;
        case "Ubicacion":
          this.$router.push(`/EditarUbicacionView/${id}`);
          break;
        case "Equipo":
          this.$router.push(`/EditarEquiposMedicosView/${id}`);
          break;
      }
    },
    logout() {
      if (!this.isLoggedIn) {
        alert("No hay sesión activa.");
        return;
      }
      // ✅ Eliminar usuario de localStorage
      localStorage.removeItem("usuario");

      // ✅ Redirigir a login
      this.$router.push("/LoginU");

      // ✅ Refrescar el estado
      this.selectedModule = null;
      this.showModulo = false;
    },
  },
  created() {
    if (this.isLoggedIn && this.$route.path === "/LoginU") {
      this.$router.push("/");
    }
  },
};
</script>


<style>
/* --- Estilos previos del layout --- */
.layout {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 240px;
  background: #2c3e50;
  color: #ecf0f1;
  padding: 20px;
}

.sidebar .logo {
  margin-bottom: 30px;
  font-size: 20px;
  text-align: center;
}

.menu {
  list-style: none;
  padding: 0;
}

.menu li {
  margin: 10px 0;
}

.menu-item {
  display: flex;
  justify-content: space-between;
  cursor: pointer;
  font-weight: bold;
  color: #ecf0f1;
}

.menu-item:hover {
  color: #42b983;
}

.arrow {
  font-size: 12px;
}

.submenu {
  list-style: none;
  padding-left: 20px;
  margin-top: 5px;
}

.submenu li {
  margin: 8px 0;
}

.submenu button {
  width: 100%;
  padding: 8px;
  background: #34495e;
  color: #ecf0f1;
  border: none;
  border-radius: 4px;
  text-align: left;
  cursor: pointer;
}

.submenu button.active {
  background: #42b983;
}

.submenu button:hover {
  background: #2ecc71;
}

.actions button {
  display: block;
  width: 100%;
  margin: 5px 0;
  padding: 8px;
  font-weight: bold;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  background: #34495e;
  color: #ecf0f1;
}

.actions button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.actions button:hover:not(:disabled) {
  background: #42b983;
}

.menu a {
  color: #bdc3c7;
  text-decoration: none;
}

.menu a:hover,
.menu a.router-link-exact-active {
  color: #42b983;
}

.content {
  flex: 1;
  padding: 20px;
  background: #f8f9fa;
}

/* --- Header nuevo --- */
.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #34495e;
  color: #ecf0f1;
  padding: 10px 20px;
}

.topbar-left {
  display: flex;
  align-items: center;
}

.logo-img {
  width: 35px;
  height: 35px;
  margin-right: 10px;
}

.topbar-title {
  font-size: 22px;
  margin: 0;
}

.topbar-right .btn {
  margin-left: 10px;
  padding: 6px 12px;
  background: #2ecc71;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  text-decoration: none;
}

.topbar-right .btn.logout {
  background: #e74c3c;
}

.topbar-right .btn:hover {
  opacity: 0.9;
}
</style>
