<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Populate existing users with proper names
        User::whereNull('first_name')->orWhereNull('last_name')->get()->each(function($user) {
            // Try to extract names from email if available
            $emailParts = explode('@', $user->email);
            $namePart = $emailParts[0] ?? '';
            
            // Replace dots and underscores with spaces
            $namePart = str_replace(['.', '_'], ' ', $namePart);
            $nameParts = explode(' ', $namePart);
            
            // Generate names based on email or use defaults
            if (count($nameParts) >= 2) {
                $firstName = ucfirst($nameParts[0]);
                $lastName = ucfirst($nameParts[1]);
            } elseif (count($nameParts) == 1 && !empty($nameParts[0])) {
                $firstName = ucfirst($nameParts[0]);
                $lastName = 'User';
            } else {
                // Fallback names based on email domain or generic names
                $firstName = $this->getFirstNameFromEmail($user->email);
                $lastName = $this->getLastNameFromEmail($user->email);
            }
            
            $user->update([
                'first_name' => $firstName,
                'last_name' => $lastName
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally clear the names (but we'll keep them)
        // User::whereNotNull('first_name')->orWhereNotNull('last_name')->update([
        //     'first_name' => null,
        //     'last_name' => null
        // ]);
    }
    
    /**
     * Get first name from email
     */
    private function getFirstNameFromEmail($email)
    {
        $emailParts = explode('@', $email);
        $namePart = $emailParts[0] ?? '';
        
        // Handle common patterns
        if (str_contains($namePart, 'john')) return 'John';
        if (str_contains($namePart, 'robert')) return 'Robert';
        if (str_contains($namePart, 'mike') || str_contains($namePart, 'mmarks')) return 'Mike';
        if (str_contains($namePart, 'pat')) return 'Patricia';
        if (str_contains($namePart, 'taylor')) return 'Taylor';
        if (str_contains($namePart, 'admin')) return 'Admin';
        if (str_contains($namePart, 'user')) return 'User';
        
        // Default fallback
        return 'User';
    }
    
    /**
     * Get last name from email
     */
    private function getLastNameFromEmail($email)
    {
        $emailParts = explode('@', $email);
        $namePart = $emailParts[0] ?? '';
        
        // Handle common patterns
        if (str_contains($namePart, 'due') || str_contains($namePart, 'doe')) return 'Doe';
        if (str_contains($namePart, 'slaughter')) return 'Slaughter';
        if (str_contains($namePart, 'ali')) return 'Ali';
        if (str_contains($namePart, 'marks')) return 'Marks';
        if (str_contains($namePart, 'swift')) return 'Swift';
        if (str_contains($namePart, 'admin')) return 'Administrator';
        if (str_contains($namePart, 'user')) return 'User';
        
        // Default fallback
        return 'User';
    }
};