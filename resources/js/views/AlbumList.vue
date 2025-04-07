<template>
  <div class="album-container">
    <div class="album-header">
      <div v-if="errorMessage" class="error-alert">
        {{ errorMessage }}
        <button @click="dismissError" class="dismiss-btn">×</button>
      </div>
      <h1>Music Albums</h1>
      <div class="search-container">
        <input 
          type="text" 
          v-model="searchQuery" 
          @input="handleSearch" 
          placeholder="Search albums or artists..." 
          class="search-input"
        />
      </div>
    </div>
    
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Loading albums...</p>
    </div>
    
    <div v-else-if="albums.length === 0" class="no-results">
      <p>No albums found. Try a different search term.</p>
    </div>
    
    <div v-else class="album-grid">
      <div v-for="album in albums" :key="album.id" class="album-card">
        <div class="album-image">
          <img 
            :src="album.cover_image || '/images/placeholder-album.jpg'" 
            :alt="`${album.song_name} by ${album.artist_name}`"
          />
        </div>
        <div class="album-info">
          <h3 class="album-title">{{ album.song_name }}</h3>
          <p class="album-artist">{{ album.artist_name }}</p>
          <div class="album-votes">
            <span class="vote-count" :class="{ 'positive': album.total_votes > 0, 'negative': album.total_votes < 0 }">
              {{ album.total_votes }} votes
            </span>
          </div>
          <div class="album-actions">
            <button 
              @click="voteOnAlbum(album.id, 1)" 
              class="vote-btn upvote" 
              :class="{ 'active': album.user_vote === 1 }"
              aria-label="Upvote"
            >
              <span class="vote-icon">👍</span>
            </button>
            <button 
              @click="voteOnAlbum(album.id, -1)" 
              class="vote-btn downvote" 
              :class="{ 'active': album.user_vote === -1 }"
              aria-label="Downvote"
            >
              <span class="vote-icon">👎</span>
            </button>
            <button 
              v-if="isAdmin" 
              @click="confirmDelete(album.id)" 
              class="delete-btn"
              aria-label="Delete album"
            >
              <span class="delete-icon">🗑️</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <div v-if="lastPage > 1" class="pagination">
      <button 
        @click="changePage(currentPage - 1)" 
        :disabled="currentPage === 1" 
        class="pagination-btn"
      >
        Previous
      </button>
      <span class="pagination-info">Page {{ currentPage }} of {{ lastPage }}</span>
      <button 
        @click="changePage(currentPage + 1)" 
        :disabled="currentPage === lastPage" 
        class="pagination-btn"
      >
        Next
      </button>
    </div>
    
    <div v-if="showDeleteModal" class="modal">
      <div class="modal-content">
        <h2>Confirm Delete</h2>
        <p>Are you sure you want to delete this album?</p>
        <div class="modal-actions">
          <button @click="cancelDelete" class="btn-secondary">Cancel</button>
          <button @click="deleteAlbum" class="btn-danger">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { defineComponent, ref, computed, onMounted, watch } from 'vue';
import { useAlbumStore } from '../stores/album';
import { useAuthStore } from '../stores/auth';
import debounce from 'lodash/debounce';

  const albumStore = useAlbumStore();
  const authStore = useAuthStore();
  
  const searchQuery = ref('');
  const showDeleteModal = ref(false);
  const albumToDelete = ref<number | null>(null);

  const errorMessage = ref('');

  // Computed properties
  const albums = computed(() => albumStore.albums);
  const loading = computed(() => albumStore.loading);
  const currentPage = computed(() => albumStore.currentPage);
  const lastPage = computed(() => albumStore.lastPage);
  const isAdmin = computed(() => authStore.isAdmin);
  
  const dismissError = () => {
    errorMessage.value = '';
  };
  const fetchAlbums = () => {
    albumStore.fetchAlbums(currentPage.value, searchQuery.value);
  };
  
  const handleSearch = debounce(() => {
    albumStore.setSearchQuery(searchQuery.value);
  }, 300);
  
  const changePage = (page: number) => {
    if (page >= 1 && page <= lastPage.value) {
      albumStore.fetchAlbums(page, searchQuery.value);
    }
  };
  
  const voteOnAlbum = async (albumId: number, vote: number) => {
    try {
      await albumStore.voteOnAlbum(albumId, vote);
    } catch (error) {
      console.error('Error voting on album:', error);
    }
  };
  
  const confirmDelete = (albumId: number) => {
    albumToDelete.value = albumId;
    showDeleteModal.value = true;
  };
  
  const cancelDelete = () => {
    albumToDelete.value = null;
    showDeleteModal.value = false;
  };
  
  const deleteAlbum = async () => {
    if (albumToDelete.value) {
      try {
        await albumStore.deleteAlbum(albumToDelete.value);
        showDeleteModal.value = false;
        albumToDelete.value = null;
      } catch (error) {
        console.error('Error deleting album:', error);
      }
    }
  };
  
  // Lifecycle hooks
  onMounted(() => {
    fetchAlbums();
  });
</script>

<style lang="scss" scoped>
.album-container {
  padding: 1rem 0;
}

.album-header {
  display: flex;
  flex-direction: column;
  margin-bottom: 2rem;
  
  @media (min-width: 768px) {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
  
  h1 {
    margin: 0 0 1rem 0;
    
    @media (min-width: 768px) {
      margin: 0;
    }
  }
  
  .search-container {
    width: 100%;
    
    @media (min-width: 768px) {
      width: auto;
      min-width: 300px;
    }
    
    .search-input {
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
  }
}

.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 0;
  
  .loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-radius: 50%;
    border-top-color: #333;
    animation: spin 1s ease-in-out infinite;
    margin-bottom: 1rem;
  }
  
  @keyframes spin {
    to { transform: rotate(360deg); }
  }
}

.no-results {
  text-align: center;
  padding: 3rem 0;
  color: #666;
}

.album-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
  
  @media (min-width: 640px) {
    grid-template-columns: repeat(2, 1fr);
  }
  
  @media (min-width: 1024px) {
    grid-template-columns: repeat(3, 1fr);
  }
}

.album-card {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
  
  &:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
  }
  
  .album-image {
    height: 200px;
    overflow: hidden;
    
    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s;
      
      &:hover {
        transform: scale(1.05);
      }
    }
  }
  
  .album-info {
    padding: 1rem;
    
    .album-title {
      margin: 0 0 0.5rem 0;
      font-size: 1.25rem;
    }
    
    .album-artist {
      color: #666;
      margin: 0 0 1rem 0;
    }
    
    .album-votes {
      margin-bottom: 1rem;
      
      .vote-count {
        font-weight: 500;
        
        &.positive {
          color: #4caf50;
        }
        
        &.negative {
          color: #f44336;
        }
      }
    }
    
    .album-actions {
      display: flex;
      gap: 0.5rem;
      
      .vote-btn {
        background: none;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 0.5rem;
        cursor: pointer;
        transition: all 0.3s;
        
        &:hover {
          background-color: #f5f5f5;
        }
        
        &.active {
          background-color: #333;
          color: white;
          border-color: #333;
        }
      }
      
      .delete-btn {
        background: none;
        border: 1px solid #f44336;
        border-radius: 4px;
        padding: 0.5rem;
        cursor: pointer;
        margin-left: auto;
        color: #f44336;
        transition: all 0.3s;
        
        &:hover {
          background-color: #f44336;
          color: white;
        }
      }
    }
  }
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 2rem;
  gap: 1rem;
  
  .pagination-btn {
    background-color: #333;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 0.5rem 1rem;
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
  
  .pagination-info {
    color: #666;
  }
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  
  .modal-content {
    background-color: white;
    border-radius: 8px;
    padding: 2rem;
    max-width: 400px;
    width: 100%;
    
    h2 {
      margin-top: 0;
    }
    
    .modal-actions {
      display: flex;
      justify-content: flex-end;
      gap: 1rem;
      margin-top: 1.5rem;
      
      button {
        padding: 0.5rem 1rem;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        
        &.btn-secondary {
          background-color: #f5f5f5;
          color: #333;
          
          &:hover {
            background-color: #e0e0e0;
          }
        }
        
        &.btn-danger {
          background-color: #f44336;
          color: white;
          
          &:hover {
            background-color: #d32f2f;
          }
        }
      }
    }
  }
}
</style>

