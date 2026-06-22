<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Database\Factories\PerfilFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // ── Distribución de roles ─────────────────────────────────────────────
        // 10 admin · 20 editor · 70 user  (total 100)
        // ── Distribución de estados ───────────────────────────────────────────
        // ~70% activo · ~20% inactivo · ~10% bloqueado
        // ── Eliminados lógicamente ────────────────────────────────────────────
        // ~10 usuarios con SoftDelete para probar la restauración

        $roles  = ['admin', 'editor', 'user'];
        $counts = ['admin' => 10, 'editor' => 20, 'user' => 70];

        foreach ($counts as $role => $total) {
            foreach (range(1, $total) as $i) {
                $status = $this->randomStatus($i, $total);

                /** @var User $user */
                $user = User::factory()->withPersonalTeam()->create([
                    'password' => $password,
                    'status'   => $status->value,
                ]);

                $user->assignRole($role);

                // Crear perfil (80% con perfil completo, 20% básico/sin datos)
                $factory = (new PerfilFactory())->for($user);
                if (fake()->boolean(20)) {
                    $factory->basico()->create();
                } else {
                    $factory->create();
                }
            }
        }

        // ── Usuarios con SoftDelete (10 registros) ───────────────────────────
        User::factory(10)
            ->withPersonalTeam()
            ->create([
                'password' => $password,
                'status'   => UserStatus::Inactive->value,
            ])
            ->each(function (User $user) {
                $user->assignRole('user');
                (new PerfilFactory())->for($user)->basico()->create();
                $user->delete(); // SoftDelete
            });

        $this->command->info('✔ 100 usuarios de prueba + 10 eliminados creados correctamente.');
    }

    private function randomStatus(int $i, int $total): UserStatus
    {
        $pct = ($i / $total) * 100;

        if ($pct <= 70) return UserStatus::Active;
        if ($pct <= 90) return UserStatus::Inactive;
        return UserStatus::Banned;
    }
}
