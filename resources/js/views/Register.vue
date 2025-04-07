<template>
  <div class="auth-container">
    <div class="auth-card">
      <h1>Register</h1>
      <form @submit.prevent="handleSubmit" class="auth-form">
        <div class="form-group">
          <label for="name">Name</label>
          <input 
            type="text" 
            id="name" 
            v-model="name" 
            required 
            placeholder="Enter your name"
          />
          <span v-if="errors.name" class="error-message">{{ errors.name[0] }}</span>
          <span v-if="errors.name" class="error-message">{{ errors.name[0] }}</span>
        </div>
        
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
        
        <div class="form-group">
          <label for="password_confirmation">Confirm Password</label>
          <input 
            type="password" 
            id="password_confirmation" 
            v-model="passwordConfirmation" 
            required 
            placeholder="Confirm your password"
          />
        </div>
        
        <div class="form-actions">
          <button type="submit" class="btn-primary" :disabled="loading">
            {{ loading ? 'Registering...' : 'Register' }}
          </button>
        </div>
        
        <div class="auth-links">
          Already have an account? <router-link to="/login">Login</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { defineComponent, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

  const authStore = useAuthStore();
  const router = useRouter();
  
  const name = ref('');
  const email = ref('');
  const password = ref('');
  const passwordConfirmation = ref('');
  const loading = ref(false);
  const errors = ref<Record<string, string[]>>({});
  
  const handleSubmit = async () => {
    loading.value = true;
    errors.value = {};
    
    try {
      await authStore.register(
        name.value,
        email.value,
        password.value,
        passwordConfirmation.value
      );
      
      router.push('/albums');
    } catch (error: any) {
      if (error.response?.status === 422) {
        errors.value = error.response.data.errors;
      } else {
        console.error('Registration error:', error);
      }
    } finally {
      loading.value = false;
    }
  };
  
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

