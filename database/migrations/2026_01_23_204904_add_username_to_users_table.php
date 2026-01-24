<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Creamos la columna nullable primero
            $table->string('username')->nullable()->after('name');
        });

        // 2. Rellenamos los usuarios existentes (Migración de datos)
        $users = User::all();
        foreach ($users as $user) {
            // Si el email es 'pepe@test.com', el nick será 'pepe'
            $baseNick = explode('@', $user->email)[0];
            $nick = $baseNick;
            $counter = 1;

            // Aseguramos que sea único (por si hay pepe@gmail y pepe@hotmail)
            while (User::where('username', $nick)->exists()) {
                $nick = $baseNick . $counter++;
            }

            $user->username = $nick;
            $user->save();
        }

        // 3. Ahora que está llena, la hacemos obligatoria y única
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
