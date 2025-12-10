<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class ApiTokens extends Component
{
    public ?string $plainTextToken = null;

    /**
     * Create a new API token for the user.
     */
    public function createToken(): void
    {
        $user = Auth::user();

        // Revoke all existing tokens (single token policy)
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('api-token');

        // Store plain text token to show once
        $this->plainTextToken = $token->plainTextToken;
    }

    /**
     * Revoke the user's API token.
     */
    public function revokeToken(): void
    {
        Auth::user()->tokens()->delete();
        $this->plainTextToken = null;
    }

    public function render()
    {
        return view('livewire.settings.api-tokens', [
            'hasToken' => Auth::user()->tokens()->exists(),
        ]);
    }
}
