<x-mail::message>
# ¡Bienvenido a Health Manager! 👋

Se ha creado una cuenta para ti en la plataforma. Aquí tienes tus datos de acceso:

- **Email:** {{ $email }}
- **Contraseña:** {{ $password }}

<x-mail::button :url="route('login')">
Iniciar Sesión
</x-mail::button>

Por favor, cambia tu contraseña una vez hayas entrado.

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
