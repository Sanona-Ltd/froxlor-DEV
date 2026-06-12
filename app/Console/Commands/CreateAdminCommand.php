<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminCommand extends Command
{
    protected $signature = 'froxlor:create-admin
                            {--name=Administrator : Name des Administrators}
                            {--email= : E-Mail-Adresse}
                            {--password= : Passwort}';

    protected $description = 'Erstellt einen neuen Administrator-Account';

    public function handle(): int
    {
        $name  = $this->option('name');
        $email = $this->option('email') ?? $this->ask('E-Mail-Adresse');

        if (User::where('email', $email)->exists()) {
            $this->error("Ein Benutzer mit der E-Mail '{$email}' existiert bereits.");
            return self::FAILURE;
        }

        $password = $this->option('password')
            ?? $this->secret('Passwort (wird nicht angezeigt)');

        if (strlen($password) < 8) {
            $this->error('Das Passwort muss mindestens 8 Zeichen lang sein.');
            return self::FAILURE;
        }

        User::create([
            'name'     => $name,
            'email'    => $email,
            'password' => $password,
            'is_admin' => true,
        ]);

        $this->info("Administrator '{$name}' ({$email}) wurde erfolgreich erstellt.");

        return self::SUCCESS;
    }
}
