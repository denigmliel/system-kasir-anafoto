<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $passwordHash = Hash::make('anafoto76');

        $users = [
            [
                'email' => 'kasiranafoto@gmail.com',
                'name' => 'Kasir',
                'role' => 'kasir',
            ],
            [
                'email' => 'gudanganafoto@gmail.com',
                'name' => 'Gudang',
                'role' => 'gudang',
            ],
        ];

        foreach ($users as $user) {
            $existing = DB::table('users')->where('email', $user['email'])->first();

            if ($existing) {
                DB::table('users')
                    ->where('id', $existing->id)
                    ->update([
                        'name' => $user['name'],
                        'password' => $passwordHash,
                        'role' => $user['role'],
                        'is_active' => true,
                        'updated_at' => $now,
                    ]);
            } else {
                DB::table('users')->insert([
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'password' => $passwordHash,
                    'role' => $user['role'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('users')
            ->whereIn('email', [
                'kasiranafoto@gmail.com',
                'gudanganafoto@gmail.com',
            ])
            ->delete();
    }
};
