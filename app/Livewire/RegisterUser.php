<?php

namespace App\Livewire;

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class RegisterUser extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $store_name = '';
    public bool $agreeToTerms = false;

    public function register(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'store_name' => 'required|string|max:255',
            'agreeToTerms' => 'accepted',
        ]);

        try {
            DB::transaction(function () {
                // 1. Create the Store with defaults
                // WhatsApp/AI credentials are no longer per-store — the platform
                // uses a single shared configuration (see WhatsAppPlatformSetting).
                $store = Store::create([
                    'name' => $this->store_name,
                    'personality_type' => 'asesor',
                    'system_prompt' => 'You are a helpful assistant.',
                ]);

                // 2. Create the User and assign to the store
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'store_id' => $store->id,
                    'is_super_admin' => false,
                ]);

                // 3. Log the user in
                Auth::login($user);
            });

            // 4. Redirect to Filament dashboard
            $this->redirect(route('filament.yes.pages.dashboard'));
        } catch (\Exception $e) {
            $this->addError('general', 'Registration failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.register-user');
    }
}
