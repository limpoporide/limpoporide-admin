<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebSocketService
{
    private $nodeServerUrl;
    private $apiKey;

    public function __construct()
    {
        $this->nodeServerUrl = config('websocket.node_server_url', 'http://localhost:3000');
        $this->apiKey = config('websocket.api_key', 'laravel-server-1-key');
    }

    /**
     * Send message to specific user
     */
    public function sendMessageToUser($toUserId, $fromUserId, $message, $attachment = null, $messageType = 'text', $roomId = null, $chatData = [])
    {
        $data = [
            'to_user_id' => $toUserId,
            'from_user_id' => $fromUserId,
            'message' => $message,
            'attachment' => $attachment,
            'message_type' => $messageType,
            'room_id' => $roomId,
            'chat_data' => $chatData
        ];
        
        // dd($data);
        
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->nodeServerUrl . '/api/send-message', $data);

            // Log::info('WebSocket sendMessageToUser response: ' . $response);
            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('WebSocket sendMessageToUser error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'WebSocket server unavailable'];
        }
    }

    /**
     * Send message to room/group
     */
    public function sendMessageToRoom($roomId, $fromUserId, $message, $attachment = null, $messageType = 'text', $excludeUsers = [], $chatData = [])
    {
        try {
            
            $data = [
                'room_id' => $roomId,
                'from_user_id' => $fromUserId,
                'message' => $message,
                'attachment' => $attachment,
                'message_type' => $messageType,
                'exclude_users' => $excludeUsers,
                'chat_data' => $chatData
            ];
            
            // dd($data);
            
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->nodeServerUrl . '/api/send-room-message', $data);

            // Log::info('WebSocket sendMessageToRoom response: ' . $response);
            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('WebSocket sendMessageToRoom error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'WebSocket server unavailable'];
        }
    }

    /**
     * Get list of online users
     */
    public function getOnlineUsers()
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey
            ])->get($this->nodeServerUrl . '/api/online-users');

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('WebSocket getOnlineUsers error: ' . $e->getMessage());
            return ['success' => false, 'online_users' => [], 'count' => 0];
        }
    }

    /**
     * Check if specific users are online
     */
    public function checkUsersOnline(array $userIds)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->nodeServerUrl . '/api/check-users-online', [
                'user_ids' => $userIds
            ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('WebSocket checkUsersOnline error: ' . $e->getMessage());
            return ['success' => false, 'users_status' => []];
        }
    }

    /**
     * Send notification to users
     */
    public function sendNotification(array $userIds, $title, $message, array $data = [])
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->nodeServerUrl . '/api/send-notification', [
                'user_ids' => $userIds,
                'title' => $title,
                'message' => $message,
                'data' => $data
            ]);
            
            // Log::info('WebSocket sendNotification response: ' . $response);
            // Log::info($userIds);
            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('WebSocket sendNotification error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'WebSocket server unavailable'];
        }
    }

    /**
     * Force disconnect a user
     */
    public function disconnectUser($userId, $reason = 'Disconnected by server')
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->nodeServerUrl . '/api/disconnect-user', [
                'user_id' => $userId,
                'reason' => $reason
            ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('WebSocket disconnectUser error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'WebSocket server unavailable'];
        }
    }

    /**
     * Check WebSocket server health
     */
    public function checkHealth()
    {
        try {
            $response = Http::get($this->nodeServerUrl . '/health');
            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('WebSocket health check error: ' . $e->getMessage());
            return ['status' => 'DOWN', 'message' => 'WebSocket server unavailable'];
        }
    }

    /**
     * Handle HTTP response
     */
    private function handleResponse($response)
    {
        if ($response->successful()) {
            return $response->json();
        }

        Log::error('WebSocket API error: ' . $response->status() . ' - ' . $response->body());
        return ['success' => false, 'message' => 'WebSocket API error: ' . $response->status()];
    }
}
