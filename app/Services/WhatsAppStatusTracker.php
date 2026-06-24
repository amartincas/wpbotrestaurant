<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class WhatsAppStatusTracker
{
    public const TTL_HOURS = 24;

    public static function markPending(int $messageId): void
    {
        Cache::put(self::messageStatusKey($messageId), 'pending', now()->addHours(self::TTL_HOURS));
    }

    public static function trackWamid(int $messageId, string $wamid): void
    {
        Cache::put(self::messageWamidKey($messageId), $wamid, now()->addHours(self::TTL_HOURS));
        Cache::put(self::wamidMessageKey($wamid), $messageId, now()->addHours(self::TTL_HOURS));

        $status = Cache::get(self::messageStatusKey($messageId), 'pending');
        Cache::put(self::wamidStatusKey($wamid), $status, now()->addHours(self::TTL_HOURS));
        Cache::put(self::messageStatusKey($messageId), $status, now()->addHours(self::TTL_HOURS));
    }

    public static function setStatusForMessage(int $messageId, string $status): void
    {
        Cache::put(self::messageStatusKey($messageId), $status, now()->addHours(self::TTL_HOURS));

        if ($wamid = Cache::get(self::messageWamidKey($messageId))) {
            Cache::put(self::wamidStatusKey($wamid), $status, now()->addHours(self::TTL_HOURS));
        }
    }

    public static function setStatusForWamid(string $wamid, string $status): void
    {
        Cache::put(self::wamidStatusKey($wamid), $status, now()->addHours(self::TTL_HOURS));

        if ($messageId = Cache::get(self::wamidMessageKey($wamid))) {
            Cache::put(self::messageStatusKey($messageId), $status, now()->addHours(self::TTL_HOURS));
        }
    }

    public static function getStatusForMessage(int $messageId): ?string
    {
        return Cache::get(self::messageStatusKey($messageId));
    }

    public static function getWamidForMessage(int $messageId): ?string
    {
        return Cache::get(self::messageWamidKey($messageId));
    }

    private static function messageStatusKey(int $messageId): string
    {
        return "whatsapp_status:message_status:{$messageId}";
    }

    private static function messageWamidKey(int $messageId): string
    {
        return "whatsapp_status:message_wamid:{$messageId}";
    }

    private static function wamidStatusKey(string $wamid): string
    {
        return "whatsapp_status:wamid_status:{$wamid}";
    }

    private static function wamidMessageKey(string $wamid): string
    {
        return "whatsapp_status:wamid_message:{$wamid}";
    }
}
