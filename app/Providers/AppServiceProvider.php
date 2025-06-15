<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User; // Pastikan ini juga ada (untuk tipe hinting di Gate)
use App\Models\Konselor; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define Gates for authorization

        // Gate untuk mengelola konselor (create, store, delete)
        Gate::define('manage-konselor', function (User $user) {
            return $user->role === 'admin';
        });

        // Gate untuk mengupdate konselor tertentu
        Gate::define('update-konselor', function (User $user, Konselor $konselor) {
            // Misalnya, hanya admin yang bisa mengupdate konselor
            // Atau jika ada skenario di mana konselor bisa mengedit profilnya sendiri,
            // Anda bisa menambahkan kondisi:
            // return $user->role === 'admin' || ($user->id === $konselor->user_id && $user->role === 'konselor');
            return $user->role === 'admin'; // Paling umum: hanya admin
        });

        // Pastikan Anda juga memiliki Gate 'manage-konselor' jika digunakan di tempat lain
        // Gate::define('manage-konselor', function (User $user) {
        //     return $user->role === 'admin';
        // });
    
    }
}
