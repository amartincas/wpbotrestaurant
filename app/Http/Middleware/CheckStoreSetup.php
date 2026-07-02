<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStoreSetup
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Verification for cronjob
        if (app()->runningInConsole()) {
            return $next($request);
        }

       // Skip checks for WhatsApp webhook routes
        if ($request->is('api/whatsapp/webhook', 'api/whatsapp/webhook/*')) {
            return $next($request);
        }

        // Only check for authenticated users accessing Filament
        if (Auth::check() && $request->path() !== 'yes/store-settings') {
            $user = Auth::user();

            // WhatsApp/AI credentials are now a single platform-wide setting
            // (no longer per-store), so store-level completeness is judged by
            // what's still actually store-owned.
            if ($user->store) {
                session(['store_setup_incomplete' => empty($user->store->system_prompt)]);
            }

            if ($user->is_super_admin) {
                $settings = \App\Models\WhatsAppPlatformSetting::current();
                session(['platform_setup_incomplete' => empty($settings->wa_access_token) || empty($settings->ai_api_key)]);
            }
        }

        return $next($request);
    }
}
