import { defineStore } from "pinia"
import axios from "axios"
import { useAuthStore } from "./auth"

interface Album {
  id: number
  song_name: string
  artist_name: string
  cover_image: string | null
  total_votes: number
  user_vote?: number
  created_at: string
  updated_at: string
}

interface AlbumState {
  albums: Album[]
  loading: boolean
  error: string | null
  currentPage: number
  lastPage: number
  total: number
  searchQuery: string
}

export const useAlbumStore = defineStore("album", {
  state: (): AlbumState => ({
    albums: [],
    loading: false,
    error: null,
    currentPage: 1,
    lastPage: 1,
    total: 0,
    searchQuery: "",
  }),

  getters: {
    sortedAlbums: (state) => {
      return [...state.albums].sort((a, b) => {
        // First sort by votes
        if (a.total_votes !== b.total_votes) {
          return b.total_votes - a.total_votes
        }
        // Then sort alphabetically by song name
        return a.song_name.localeCompare(b.song_name)
      })
    },
  },

  actions: {
    async fetchAlbums(page = 1, search = "") {
      this.loading = true
      this.error = null

      try {
        const response = await axios.get("/api/albums", {
          params: {
            page,
            search,
          },
        })

        this.albums = response.data.data
        this.currentPage = response.data.current_page
        this.lastPage = response.data.last_page
        this.total = response.data.total

        return response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || "Failed to fetch albums"
        throw error
      } finally {
        this.loading = false
      }
    },

    async voteOnAlbum(albumId: number, vote: number) {
      try {
        const response = await axios.post(`/api/albums/${albumId}/vote`, { vote })
    
        const index = this.albums.findIndex((album) => album.id === albumId)
        if (index !== -1 && response.data && response.data.data) {
          this.albums[index] = response.data.data
        }
    
        return response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || "Failed to vote on album"
        throw error
      }
    },

    async deleteAlbum(albumId: number) {
      const authStore = useAuthStore()

      if (!authStore.isAdmin) {
        this.error = "Unauthorized"
        throw new Error("Unauthorized")
      }

      try {
        await axios.delete(`/api/albums/${albumId}`)

        // Remove the album from the store
        this.albums = this.albums.filter((album) => album.id !== albumId)

        return true
      } catch (error: any) {
        this.error = error.response?.data?.message || "Failed to delete album"
        throw error
      }
    },

    setSearchQuery(query: string) {
      this.searchQuery = query
      this.fetchAlbums(1, query)
    },
  },
})

