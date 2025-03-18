<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;
use Laravel\Passport\PersonalAccessClient;

class AuthClientSeeder extends Seeder
{
    public function run(): void
    {
        $clientName = config('services.passport.client_name');

        $client = Client::query()
            ->where('name', $clientName)
            ->firstOr(function () use ($clientName) {
                $client = (new Client)
                    ->forceFill([
                        'name' => $clientName,
                        'secret' => config('services.passport.client_secret'),
                        'provider' => 'users',
                        'redirect' => config('app.url'),
                        'personal_access_client' => true,
                        'password_client' => true,
                        'revoked' => false,
                    ]);
                $client->save();

                return $client;
            });

        PersonalAccessClient::query()
            ->firstOrCreate(['client_id' => $client->id]);
    }
}
