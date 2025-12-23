<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Crypt;

class ChatHelper
{
    /**
     * Encrypt a message for storage
     */
    public static function encryptMessage(string $message): string
    {
        return Crypt::encryptString($message);
    }

    /**
     * Decrypt a message for display
     */
    public static function decryptMessage(string $encryptedMessage): string
    {
        try {
            return Crypt::decryptString($encryptedMessage);
        } catch (\Exception $e) {
            // If decryption fails, return the original message (for backward compatibility)
            return $encryptedMessage;
        }
    }

    /**
     * Format message for display with proper escaping
     */
    public static function formatMessageForDisplay(string $message): string
    {
        // Decrypt if needed
        $decryptedMessage = self::decryptMessage($message);
        
        // Escape HTML but preserve line breaks
        $escapedMessage = htmlspecialchars($decryptedMessage, ENT_QUOTES, 'UTF-8');
        
        // Convert line breaks to <br> tags
        return nl2br($escapedMessage);
    }

    /**
     * Sanitize message input
     */
    public static function sanitizeMessage(string $message): string
    {
        // Remove potentially dangerous content
        $message = strip_tags($message);
        
        // Trim whitespace
        $message = trim($message);
        
        // Limit length
        return substr($message, 0, 1000);
    }

    /**
     * Generate conversation slug
     */
    public static function generateConversationSlug(string $title, int $contactId): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = trim($slug, '-');
        
        return $slug . '-' . $contactId . '-' . time();
    }

    /**
     * Check if user can access conversation
     */
    public static function canAccessConversation($user, int $conversationId): bool
    {
        if (!$user) {
            return false;
        }

        $conversation = \App\Models\Conversation::find($conversationId);
        if (!$conversation) {
            return false;
        }

        // Check if user is a participant
        return $conversation->participants()
            ->where('user_id', $user->id)
            ->exists();
    }

    /**
     * Get conversation participants
     */
    public static function getConversationParticipants(int $conversationId): array
    {
        $conversation = \App\Models\Conversation::find($conversationId);
        if (!$conversation) {
            return [];
        }

        $participants = [];
        
        // Add contact
        if ($conversation->creator) {
            $participants[] = [
                'type' => 'contact',
                'id' => $conversation->creator->id,
                'name' => $conversation->creator->first_name . ' ' . $conversation->creator->last_name,
                'email' => $conversation->creator->email,
            ];
        }

        // Add admin participants
        foreach ($conversation->participants as $participant) {
            if ($participant->user) {
                $participants[] = [
                    'type' => 'admin',
                    'id' => $participant->user->id,
                    'name' => $participant->user->first_name . ' ' . $participant->user->last_name,
                    'email' => $participant->user->email,
                ];
            }
        }

        return $participants;
    }

    /**
     * Format time for display
     */
    public static function formatTime($timestamp): string
    {
        if (!$timestamp) {
            return '';
        }

        $time = is_string($timestamp) ? strtotime($timestamp) : $timestamp;
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . 'm ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . 'h ago';
        } else {
            return date('M j, Y', $time);
        }
    }

    /**
     * Check if message is from current user
     */
    public static function isMessageFromUser($message, $currentUserId): bool
    {
        return $message->user_id === $currentUserId;
    }

    /**
     * Get message sender name
     */
    public static function getMessageSenderName($message): string
    {
        if ($message->user) {
            return $message->user->first_name . ' ' . $message->user->last_name;
        } elseif ($message->contact) {
            return $message->contact->first_name . ' ' . $message->contact->last_name;
        }
        
        return 'System';
    }
}

