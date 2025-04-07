<?php

namespace App\Services\Interfaces;

use App\Models\User;
use Illuminate\Http\Request;

interface AuthServiceInterface
{
    /**
     * Register a new user.
     *
     * @param array $data
     * @return array
     */
    public function register(array $data): array;
    
    /**
     * Authenticate a user and generate a token.
     *
     * @param array $credentials
     * @return array
     */
    public function login(array $credentials): array;
    
    /**
     * Revoke the user's current access token.
     *
     * @param User $user
     * @return bool
     */
    public function logout(User $user): bool;
    
    /**
     * Get the authenticated user.
     *
     * @param User $user
     * @return User
     */
    public function getAuthenticatedUser(User $user): User;
}