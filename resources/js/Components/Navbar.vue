<template>
  <nav class="navbar">
      <ul>
          <li class="home"><Link class="hover:bg-white hover:text-black hover:rounded-md" href="/">Início</Link></li>
          <li class="agendar"><Link class="hover:bg-white hover:text-black hover:rounded-md" href="/agendar">Agendar</Link></li>
          <li class="consultar"><Link class="hover:bg-white hover:text-black hover:rounded-md"  href="/consultar">Consultar</Link></li>
      </ul>

        <!-- Botão de perfil e logout -->
        <div class="hidden sm:ms-6 sm:flex sm:items-center">
            <!-- Settings Dropdown -->
            <div class="relative ms-3">
              <Dropdown align="right" width="48">
                  <template #trigger>
                    <span class="inline-flex rounded-md">
                      <button
                          type="button"
                          class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                      >
                          {{ $page.props.auth.user.name }}
                          <svg
                              class="-me-0.5 ms-2 h-4 w-4"
                              xmlns="http://www.w3.org/2000/svg"
                              viewBox="0 0 20 20"
                              fill="currentColor"
                          >
                            <path
                                fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                          </svg>
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <DropdownLink :href="route('profile.edit')"  as="button">
                      <i class="fa-solid fa-user mr-2"></i><span>Perfil</span>
                    </DropdownLink>

                    <DropdownLink v-if="$page.props.auth.user.role === 'ADMIN'" :href="route('admin.users')"  as="button">
                      <i class="fa-solid fa-users mr-2"></i><span>Usuarios</span>
                    </DropdownLink>
                    
                    <button class="button-logout" @click="handleLogout" as="button">
                      <i class="fas fa-sign-out-alt mr-2"></i><span class="span-logout">Sair</span>
                    </button>
                  </template>
              </Dropdown>
            </div>
        </div>
  </nav>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

export default {
  name: "Navbar",
  components: {
    Link,
    Dropdown,
    DropdownLink
  },

  methods: {
    handleLogout() {
      localStorage.clear();
      sessionStorage.clear();

      Inertia.post(this.route('logout'));
    }
  }
};
</script>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(to bottom, #000000, #3d3d3d);
  padding: 1em;
  border-radius: 0%;
}
ul {
  list-style: none;
  width: 500px;
  display: flex;
  gap: 1em;
}
li a {
  color: #fff;
  text-decoration: none;
}

button {
  background-color: #fff;
  color: #000000;
}

.button-logout {
  width: 100%;
  display: flex;
}

.span-logout {
  margin-top: -3px;
}

button:hover {
  background-color: #0050a7;
  color: #fff;
}
</style>
