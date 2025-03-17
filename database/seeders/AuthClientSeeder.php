<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;

class AuthClientSeeder extends Seeder
{
    public function run(): void
    {
        $clientName = config('services.passport.client_name');

        Client::query()
            ->where('name', $clientName)
            ->firstOr(function () use ($clientName) {
                (new Client)
                    ->forceFill([
                        'name' => $clientName,
                        'secret' => config('services.passport.client_secret'),
                        'provider' => 'users',
                        'redirect' => config('app.url'),
                        'personal_access_client' => false,
                        'password_client' => true,
                        'revoked' => false,
                    ])
                    ->save();
            });
    }
}
