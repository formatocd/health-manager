<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserCredentials;
use App\Models\Invitation;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $name;
    public $email;
    public ?string $generatedLink = null;

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $password = Str::password(10);

        $baseNick = explode('@', $this->email)[0];
        $nick = $baseNick;
        $counter = 1;
        while (User::where('username', $nick)->exists()) {
            $nick = $baseNick . $counter++;
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'username' => $nick,
            'password' => Hash::make($password),
            'role' => 'user',
        ]);

        try {
            Mail::to($user->email)->send(new NewUserCredentials($user->email, $password));
            session()->flash('status', "Usuario creado y correo enviado a {$this->email}");
        } catch (\Exception $e) {
            session()->flash('error', "Usuario creado, pero falló el envío del correo. Contraseña: $password");
        }

        $this->reset(['name', 'email']);
        $this->dispatch('close-modal', 'create-user');
    }

    public function deleteUser($id)
    {
        if ($id === auth()->id()) return;

        User::find($id)?->delete();
    }

    public function render()
    {
        return view('livewire.admin.user-management', [
            'users' => User::paginate(10)
        ])->layout('layouts.app');
    }

    public function generateInviteLink()
    {
        // Generamos un token aleatorio
        $token = Str::random(40);

        // Guardamos la invitación en la base de datos
        Invitation::create([
            'token' => $token,
            'role' => 'user', // O el rol que corresponda
            'expires_at' => now()->addHours(24), // Caduca en 24h
        ]);

        // Generamos la URL completa
        $this->generatedLink = route('register.invite', ['token' => $token]);
    }

    public function resetModal()
    {
        $this->reset(['name', 'email', 'generatedLink']);
    }
}
