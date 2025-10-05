<template>
  <div id="app">
    <!-- Si estoy en login o registro, muestro esas vistas y oculto todo el resto -->
    <div v-if="isAuthPage">
      <router-view />
    </div>

    <!-- Si estoy logueado (home o cualquier otra vista del sistema), muestro topbar + layout -->
    <div v-else-if="isLoggedIn">
      <!-- ✅ Header SIEMPRE visible -->
      <header class="topbar">
        <div class="topbar-left">
          <img src="../src/assets/usuario.jpg" alt="Logo Inventario" class="logo" />
          <h1 class="topbar-title">Hola, {{ usuario || "Invitado" }}</h1>
        </div>
        <div class="topbar-right">
          <button class="btn logout" @click="logout">Cerrar Sesión</button>
        </div>
      </header>

      <!-- ✅ Layout general -->
      <div class="layout">
        <!-- Sidebar solo si NO estoy en Home -->
        <aside v-if="!isHomePage" class="sidebar">
          <div class="sidebar-header">
            <img src="../src/assets/Health.jpg" alt="Logo Inventario" class="logo" />
            <h2 class="sidebar-title">Inventario</h2>
          </div>

          <ul class="menu">
            <li>
              <router-link to="/" class="menu-link">
                <i class="fas fa-home"></i> Home
              </router-link>
            </li>

            <li>
              <div class="menu-item" @click="showModulo = !showModulo">
                <i class="fas fa-folder-open"></i> Módulo
                <i class="fas" :class="showModulo ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
              </div>

              <ul v-if="showModulo" class="submenu">
                <li>
                  <button
                    @click="selectModuleAndNavigate('Responsable', '/ResponsableView')"
                    :class="{ active: selectedModule === 'Responsable' }"
                  >
                    <i class="fas fa-user-md"></i> Responsables
                  </button>
                </li>
                <li>
                  <button
                    @click="selectModuleAndNavigate('Ubicacion', '/UbicacionView')"
                    :class="{ active: selectedModule === 'Ubicacion' }"
                  >
                    <i class="fas fa-map-marker-alt"></i> Ubicaciones
                  </button>
                </li>
                <li>
                  <button
                    @click="selectModuleAndNavigate('Equipo', '/EquiposMedicosView')"
                    :class="{ active: selectedModule === 'Equipo' }"
                  >
                    <i class="fas fa-stethoscope"></i> Equipos Médicos
                  </button>
                </li>
              </ul>
            </li>

            <li class="actions">
              <button :disabled="!selectedModule" @click="irCrear(selectedModule)">
                <i class="fas fa-plus-circle"></i> Crear {{ selectedModule || "" }}
              </button>
              <button :disabled="!selectedModule" @click="irEditar(selectedModule)">
                <i class="fas fa-edit"></i> Editar {{ selectedModule || "" }}
              </button>
            </li>

            
          </ul>
        </aside>

        <!-- Contenido principal -->
        <main class="content">
          <router-view />
        </main>
      </div>
    </div>

    <!-- Si no está logueado y no es login/registro → redirigir -->
    <div v-else>
      <router-view />
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      selectedModule: null,
      showModulo: false,
      loggedIn: !!localStorage.getItem("usuario"),
      usuario: "",
    };
  },
  computed: {
    isAuthPage() {
      return this.$route.path === "/LoginU" || this.$route.path === "/RegistrarU";
    },
    isLoggedIn() {
      return this.loggedIn;
    },
    isHomePage() {
      return this.$route.name === "home" || this.$route.path === "/";
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
      localStorage.removeItem("usuario");
      this.$router.push("/LoginU");
      this.selectedModule = null;
      this.showModulo = false;
      this.loggedIn = false;
      this.usuario = "";
    },
  },
  created() {
    const userData = localStorage.getItem("usuario");
    if (userData) {
      const parsed = JSON.parse(userData);
      this.usuario = parsed.usuario || "";
      this.loggedIn = true;
    }
    // 🔒 Si no está logueado y no está en login/registro, mandarlo al login
    if (!this.loggedIn && !this.isAuthPage) {
      this.$router.push("/LoginU");
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

/* --- Sidebar moderno --- */
.sidebar {
  width: 250px;
  background: linear-gradient(180deg, #1e2a38 0%, #2c3e50 100%);
  color: #ecf0f1;
  padding: 20px 15px;
  display: flex;
  flex-direction: column;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
}

/* Header del sidebar */

/* 🔹 Imagen circular en el header */
.topbar .logo {
  width: 40px;              /* Ancho fijo */
  height: 40px;             /* Alto fijo (mismo que el ancho) */
  border-radius: 50%;       /* Hace el círculo */
  object-fit: cover;        /* Rellena el círculo sin deformar */
  margin-right: 10px;
}

/* 🔹 Imagen cuadrada con bordes curvos en el sidebar */
.sidebar .logo {
  width: 120px;              /* Tamaño del logo */
  height: 100px;             /* Cuadrado */
  border-radius: 15px;      /* Bordes redondeados */
  /*object-fit: cover;        /* Ajuste sin deformar */
  display: block;
  margin: 0 auto 12px;      /* Centrado */
}

.sidebar-title {
  font-size: 1.4rem;
  font-weight: 600;
  color: #ffffff;
}

/* --- Menú principal --- */
.menu {
  list-style: none;
  padding: 0;
  margin: 0;
  flex: 1;
}

.menu li {
  margin: 10px 0;
}

/* Enlaces principales */
.menu-link {
  display: flex;
  align-items: center;
  color: #bdc3c7;
  text-decoration: none;
  font-weight: 500;
  padding: 10px;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.menu-link i {
  margin-right: 10px;
  width: 20px;
  text-align: center;
}

.menu-link:hover,
.menu-link.router-link-exact-active {
  background: rgba(255, 255, 255, 0.1);
  color: #42b983;
}

/* --- Submenú desplegable --- */
.menu-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  cursor: pointer;
  color: #ecf0f1;
  border-radius: 8px;
  transition: background 0.2s;
}

.menu-item:hover {
  background: rgba(255, 255, 255, 0.1);
}

.submenu {
  list-style: none;
  padding-left: 10px;
  margin-top: 5px;
}

.submenu button {
  width: 100%;
  background: none;
  border: none;
  color: #bdc3c7;
  padding: 8px 10px;
  text-align: left;
  border-radius: 6px;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
}

.submenu button i {
  margin-right: 8px;
}

.submenu button:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #42b983;
}

.submenu button.active {
  background: #42b983;
  color: white;
}

/* --- Botones de acciones --- */
.actions button {
  width: 100%;
  padding: 10px;
  margin-top: 8px;
  font-weight: 600;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s;
  background: rgba(255, 255, 255, 0.1);
  color: #ecf0f1;
}

.actions button:hover:not(:disabled) {
  background: #42b983;
  color: #fff;
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
  padding: 12px 24px;
  height: 60px;
  background: linear-gradient(180deg, #2c3e50 0%, #1e2a38 100%);
  color: #ecf0f1;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  width: 100%;
  position: relative;
  z-index: 10;
}


.topbar-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.topbar-title {
  font-size: 1.2rem;
  font-weight: 600;
  color: #ecf0f1;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn {
  background: #ecf0f1;
  color: #1e2a38;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.btn:hover {
  background: #bdc3c7;
}

.logout {
  background: #e74c3c;
  color: white;
}

.logout:hover {
  background: #c0392b;
}


  </style>
