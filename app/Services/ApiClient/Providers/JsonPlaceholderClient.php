<?php

namespace App\Services\ApiClient\Providers;

use App\Services\ApiClient\AbstractApiClient;

class JsonPlaceholderClient extends AbstractApiClient
{
    /**
     * Get health check endpoint
     */
    protected function getHealthCheckEndpoint(): string
    {
        return '/posts/1';
    }

    /**
     * Get custom headers if needed
     */
    protected function getCustomHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Get all posts
     */
    public function getPosts(array $params = [])
    {
        return $this->get('/posts', $params);
    }

    /**
     * Get specific post
     */
    public function getPost(int $id)
    {
        return $this->get("/posts/{$id}");
    }

    /**
     * Create new post
     */
    public function createPost(array $data)
    {
        return $this->post('/posts', $data);
    }

    /**
     * Update post
     */
    public function updatePost(int $id, array $data)
    {
        return $this->put("/posts/{$id}", $data);
    }

    /**
     * Delete post
     */
    public function deletePost(int $id)
    {
        return $this->delete("/posts/{$id}");
    }

    /**
     * Get users
     */
    public function getUsers(array $params = [])
    {
        return $this->get('/users', $params);
    }

    /**
     * Get specific user
     */
    public function getUser(int $id)
    {
        return $this->get("/users/{$id}");
    }
} 