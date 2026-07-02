<?php

namespace App\Filament\Resources\Stores\Pages;

use App\Filament\Pages\ManageChats;
use App\Filament\Resources\Stores\Schemas\StoreWizardForm;
use App\Filament\Resources\Stores\StoreResource;
use App\Services\WhatsAppService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EditStore extends EditRecord
{
    protected static string $resource = StoreResource::class;

    public function form(Schema $schema): Schema
    {
        return StoreWizardForm::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        // "Test Connection" moved to WhatsAppPlatformSettingsPage — the
        // WhatsApp connection is now shared across all stores, not per-store.
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Trigger welcome message after store is saved
        WhatsAppService::sendWelcomeMessage($this->record);

        // Show completion notification
        Notification::make()
            ->title('Configuración guardada')
            ->body('Los cambios en tu tienda han sido guardados exitosamente.')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        // Redirect to WhatsApp Chat Center after successful save
        return ManageChats::getUrl();
    }
}

