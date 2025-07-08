<?php

namespace App\Services\ApiClient\Providers;

use App\Services\ApiClient\AbstractApiClient;

class ExampleApiClient extends AbstractApiClient
{
    /**
     * Get health check endpoint
     */
    protected function getHealthCheckEndpoint(): string
    {
        return '/health';
    }

    /**
     * Get custom headers if needed
     */
    protected function getCustomHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'Laravel-API-Client/1.0',
        ];
    }

    /**
     * Get data from external API
     */
    public function getData(array $filters = [])
    {
        return $this->get('/data', $filters);
    }

    /**
     * Send data to external API
     */
    public function sendData(array $payload)
    {
        return $this->post('/data', $payload);
    }

    /**
     * Update data in external API
     */
    public function updateData(string $id, array $payload)
    {
        return $this->put("/data/{$id}", $payload);
    }

    /**
     * Delete data from external API
     */
    public function removeData(string $id)
    {
        return $this->delete("/data/{$id}");
    }

    /**
     * Get analytics data
     */
    public function getAnalytics(string $period = 'today')
    {
        return $this->get('/analytics', ['period' => $period]);
    }

    /**
     * Sync data
     */
    public function syncData(array $data)
    {
        return $this->post('/sync', $data);
    }
} 