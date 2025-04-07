<template>
  <div class="auth-container">
    <div class="auth-card">
      <h1>Login</h1>
      <form @submit.prevent="handleSubmit" class="auth-form">
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            v-model="email" 
            required 
            placeholder="Enter your email"
          />
          <span v-if="errors.email" class="error-message">{{ errors.email[0] }}</span>
        </div>
        
        <div class="form-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            v-model="password" 
            required 
            placeholder="Enter your password"
          />
          <span v-if="errors.password" class="error-message">{{ errors.password[0] }}</span>
        </div>
        
        <div v-if="loginError" class="alert-error">
          {{ loginError }}
        </div>
        
        <div class="form-actions">
          <button type="submit" class="btn-primary" :disabled="loading">
            {{ loading ? 'Logging in...' : 'Login' }}
          </button>
        </div>
        
        <div class="auth-links">
          Don't have an account? <router-link to="/register">Register</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

export default defineComponent({
  name: 'Login',
  setup() {
    const authStore = useAuthStore();
    const router = useRouter();
    
    const email = ref('');
    const password = ref('');
    const loading = ref(false);
    const errors = ref<Record<string, string[]>>({});
    const loginError = ref('');
    
    const handleSubmit = async () => {
      loading.value = true;
      errors.value = {};
      loginError.value = '';
      
      try {
        await authStore.login(email.value, password.value);
        window.location.href = '/albums';
      } catch (error: any) {
        if (error.response?.status === 422) {
          errors.value = error.response.data.errors;
        } else if (error.response?.status === 401) {
          loginError.value = 'Invalid email or password';
        } else {
          loginError.value = 'An error occurred during login';
          console.error('Login error:', error);
        }
      } finally {
        loading.value = false;
      }
    };
    
    return {
      email,
      password,
      loading,
      errors,
      loginError,
      handleSubmit
    };
  }
});
</script>

<style lang="scss" scoped>
.auth-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  padding: 1rem;
}

.auth-card {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  width: 100%;
  max-width: 400px;
  
  h1 {
    text-align: center;
    margin-bottom: 1.5rem;
    color: #333;
  }
}

.auth-form {
  .form-group {
    margin-bottom: 1rem;
    
    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
    }
    
    input {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
      
      &:focus {
        outline: none;
        border-color: #666;
      }
    }
    
    .error-message {
      color: #e53935;
      font-size: 0.875rem;
      margin-top: 0.25rem;
      display: block;
    }
  }
  
  .alert-error {
    background-color: #ffebee;
    color: #c62828;
    padding: 0.75rem;
    border-radius: 4px;
    margin-bottom: 1rem;
    font-size: 0.875rem;
  }
  
  .form-actions {
    margin-top: 1.5rem;
    
    .btn-primary {
      width: 100%;
      padding: 0.75rem;
      background-color: #333;
      color: white;
      border: none;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s;
      
      &:hover {
        background-color: #555;
      }
      
      &:disabled {
        background-color: #999;
        cursor: not-allowed;
      }
    }
  }
  
  .auth-links {
    margin-top: 1rem;
    text-align: center;
    font-size: 0.875rem;
    
    a {
      color: #333;
      text-decoration: underline;
      
      &:hover {
        color: #555;
      }
    }
  }
}
</style>

