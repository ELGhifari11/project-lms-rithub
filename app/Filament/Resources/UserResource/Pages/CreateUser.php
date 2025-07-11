<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Http\Controllers\WhatsAppController;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
      public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        // logger($data);

        $password = Str::random(6);

        $data['role'] = 'mentor';

        $data['password'] = bcrypt($password);

        // Send email with login credentials
        // Mail::send('emails.user-credentials', [
        //     'email' => $data['email'],
        //     'password' => $password,
        // ], function($message) use ($data) {
        //     $message->to($data['email'])
        //             ->subject('Your Login Credentials');
        // });

        $whatsAppController = new WhatsAppController();
        $whatsAppController->messagePasswordRegister($data['phone'], $password, $data['name'], now()->toDateString() . ' ' . now()->format('l'));

        // logger($data);

        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->icon('heroicon-s-user-plus')
            ->title('User registered')
            ->body('The user has been created successfully, The password has been sent to the user WhatsApp.');
    }
}
