// resources/js/stores/auth.ts
import { defineStore } from 'pinia';
import axios from 'axios';

interface User {
  id: number;
  name: string;
  email: string;
}

interface AuthState {
  user: User | null;
  token: string | null;
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: localStorage.getItem('token'),
  }),
  
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  
  actions: {
    async login(email: string, password: string) {
      try {
        const response = await axios.post('/api/login', { email, password });
        this.setAuthData(response.data.user, response.data.token);
        return response;
      } catch (error) {
        throw error;
      }
    },
    
    async register(name: string, email: string, password: string, password_confirmation: string) {
      try {
        const response = await axios.post('/api/register', { 
          name, email, password, password_confirmation 
        });
        this.setAuthData(response.data.user, response.data.token);
        return response;
      } catch (error) {
        throw error;
      }
    },
    
    async logout() {
      try {
        await axios.post('/api/logout');
      } catch (error) {
        console.error('Logout error:', error);
      } finally {
        this.clearAuthData();
      }
    },
    
    setAuthData(user: User, token: string) {
      this.user = user;
      this.token = token;
      localStorage.setItem('token', token);
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    },
    
    clearAuthData() {
      this.user = null;
      this.token = null;
      localStorage.removeItem('token');
      delete axios.defaults.headers.common['Authorization'];
    },
    
    initializeAuth() {
      const token = localStorage.getItem('token');
      if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
      }
    }
  }
});