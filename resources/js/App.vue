<template>
  <div class="app-container">
    <header v-if="isAuthenticated">
      <nav>
        <div class="logo">
          <h1>BlueShores Music</h1>
        </div>
        <div class="nav-links">
          <button @click="logout" class="logout-btn">Logout</button>
        </div>
      </nav>
    </header>
    <main>
      <router-view />
    </main>
    <footer>
      <p>&copy; {{ new Date().getFullYear() }} BlueShores Music Taste Tracker</p>
    </footer>
  </div>
</template>

<script lang="ts">
import { defineComponent, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './stores/auth';

export default defineComponent({
  name: 'App',
  setup() {
    const authStore = useAuthStore();
    const router = useRouter();
    
    const isAuthenticated = computed(() => authStore.isAuthenticated);
    
    const logout = async () => {
      await authStore.logout();
      router.push('/login');
    };
    
    return {
      isAuthenticated,
      logout
    };
  }
});
</script>

<style lang="scss">
.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  
  header {
    background-color: #333;
    color: white;
    
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem;
      max-width: 1200px;
      margin: 0 auto;
      width: 100%;
      
      .logo h1 {
        margin: 0;
        font-size: 1.5rem;
      }
      
      .logout-btn {
        background: none;
        border: 1px solid white;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        
        &:hover {
          background-color: white;
          color: #333;
        }
      }
    }
  }
  
  main {
    flex: 1;
    padding: 1rem;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
  }
  
  footer {
    background-color: #f5f5f5;
    padding: 1rem;
    text-align: center;
    
    p {
      margin: 0;
      color: #666;
    }
  }
}
</style>

