# 🔐 Guía de Autenticación en Laravel - 8M-Chirper

Esta guía explica cómo hemos implementado autenticación básica en nuestra aplicación de memes siguiendo el tutorial de Laravel.

---

## 📋 Contenido

1. [Formulario de Registro](#1-formulario-de-registro)
2. [Controlador de Registro](#2-controlador-de-registro)
3. [Rutas con Middleware Guest](#3-rutas-con-middleware-guest)
4. [Actualización del Header con @auth](#4-actualización-del-header-con-auth)
5. [Protección de Rutas con Middleware Auth](#5-protección-de-rutas-con-middleware-auth)
6. [Usar auth()->user() en el Controlador](#6-usar-authuser-en-el-controlador)
7. [Autorización con $this->authorize()](#7-autorización-con-thisauthorize)
8. [Vista con @can](#8-vista-con-can)

---

## 1. Formulario de Registro

### � Crear el Directorio
Primero, crea el directorio para las vistas de autenticación:

```bash
mkdir resources/views/auth
```

### �📄 Vista del Formulario
**Archivo:** `resources/views/auth/register.blade.php`

```blade
<x-layout>
    <x-slot:title>
        Registro
    </x-slot:title>

    <div class="min-h-[calc(100vh-16rem)] flex items-center justify-center">
        <div class="w-96 bg-white rounded-lg shadow-lg">
            <div class="p-8">
                <h1 class="text-3xl font-bold text-center mb-6">Crear Cuenta</h1>

                <form method="POST" action="/register">
                    @csrf

                    <!-- Nombre -->
                    <div class="mb-4">
                        <label for="name">Nombre</label>
                        <input type="text" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <p class="text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <p class="text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-4">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" required>
                        @error('password')
                            <p class="text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="mb-6">
                        <label for="password_confirmation">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" required>
                    </div>

                    <button type="submit">Registrarse</button>
                </form>
            </div>
        </div>
    </div>
</x-layout>
```

**Elementos clave:**
- `@csrf`: Token de seguridad obligatorio
- `old('name')`: Mantiene valores en caso de error
- `@error('name')`: Muestra errores de validación
- `password_confirmation`: Laravel valida automáticamente que coincida

---

## 2. Controlador de Registro

### 🔨 Generar el Controlador
Usa Artisan para crear un controlador invokable:

```bash
php artisan make:controller Auth/Register --invokable
```

**¿Qué es un controlador invokable?**
- Es un controlador de **una sola acción**
- Solo tiene el método `__invoke()`
- Ideal para acciones específicas como registro, login, logout
- Más organizado que un controlador con muchos métodos

### 🎮 Controlador Invokable
**Archivo:** `app/Http/Controllers/Auth/Register.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Register extends Controller
{
    public function __invoke(Request $request)
    {
        // Validar datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Crear usuario
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Iniciar sesión automáticamente
        Auth::login($user);

        // Redirigir
        return redirect('/')->with('success', '¡Bienvenido/a a 8M-Chirper!');
    }
}
```

**Puntos importantes:**
- `Hash::make()`: Hashea la contraseña (¡NUNCA guardes en texto plano!)
- `Auth::login($user)`: Inicia sesión automáticamente después del registro
- `unique:users`: Valida que el email no exista en la base de datos

---

## 3. Rutas con Middleware Guest

### 🛣️ Archivo de Rutas
**Archivo:** `routes/web.php`

```php
use App\Http\Controllers\Auth\Register;

// Rutas para invitados (guest middleware)
Route::view('/register', 'auth.register')
    ->middleware('guest')
    ->name('register');

Route::post('/register', Register::class)
    ->middleware('guest');
```

**¿Qué hace el middleware `guest`?**

```php
->middleware('guest')
```

- Solo usuarios **NO autenticados** pueden acceder
- Si ya estás logueado, te redirige a `/home`
- Previene que usuarios autenticados vean páginas de login/registro

---

## 4. Actualización del Header con @auth

### 🎨 Layout con Autenticación
**Archivo:** `resources/views/components/layout.blade.php`

```blade
<header class="bg-blue-600 text-white p-4">
    <div class="max-w-4xl mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">8M-Chirper</h1>
        
        <div class="flex items-center gap-4">
            @auth
                <!-- Usuario autenticado -->
                <span class="text-sm">{{ auth()->user()->name }}</span>
                <form method="POST" action="/logout" class="inline">
                    @csrf
                    <button type="submit">Cerrar Sesión</button>
                </form>
            @else
                <!-- Usuario invitado -->
                <a href="/login">Iniciar Sesión</a>
                <a href="/register">Registrarse</a>
            @endauth
        </div>
    </div>
</header>
```

**Directivas Blade:**

```blade
@auth
    <!-- Se muestra si está autenticado -->
@else
    <!-- Se muestra si NO está autenticado -->
@endauth
```

```blade
{{ auth()->user()->name }}  <!-- Nombre del usuario actual -->
{{ auth()->user()->email }} <!-- Email del usuario actual -->
```

---

## 5. Protección de Rutas con Middleware Auth

### 🔒 Rutas Protegidas
**Archivo:** `routes/web.php`

```php
// Ruta pública (todos pueden verla)
Route::get('/', [MemeController::class, 'index']);

// Rutas protegidas (solo autenticados)
Route::middleware('auth')->group(function () {
    Route::post('/memes', [MemeController::class, 'store']);
    Route::get('/memes/{meme}/edit', [MemeController::class, 'edit']);
    Route::put('/memes/{meme}', [MemeController::class, 'update']);
    Route::delete('/memes/{meme}', [MemeController::class, 'destroy']);
});
```

**¿Qué hace el middleware `auth`?**

```php
Route::middleware('auth')->group(function () {
    // Todas estas rutas requieren autenticación
});
```

- Solo usuarios **autenticados** pueden acceder
- Si NO estás logueado, te redirige a `/login`
- Protege acciones sensibles (crear, editar, eliminar)

---

## 6. Usar auth()->user() en el Controlador

### 🎮 Asociar Recursos con Usuario
**Archivo:** `app/Http/Controllers/MemeController.php`

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'meme_url' => 'required|url|max:500',
        'explicacion' => 'required|string|max:1000',
    ]);

    // Crear meme asociado al usuario autenticado
    auth()->user()->memes()->create([
        'meme_url' => $validated['meme_url'],
        'explicacion' => $validated['explicacion'],
        'fecha_subida' => now(),
    ]);

    return redirect('/')->with('success', '¡Tu meme ha sido publicado!');
}
```

**¿Cómo funciona?**

```php
auth()->user()->memes()->create([...]);
```

1. `auth()->user()`: Obtiene el usuario autenticado actual
2. `->memes()`: Accede a la relación `hasMany` del modelo User
3. `->create([...])`: Crea el meme automáticamente con `user_id`

**Relación en el Modelo:**

```php
// app/Models/User.php
public function memes(): HasMany
{
    return $this->hasMany(Meme::class);
}
```

---

## 7. Autorización con $this->authorize()

### � Generar la Policy
Usa Artisan para crear una policy:

```bash
php artisan make:policy MemePolicy --model=Meme
```

Esto genera automáticamente una policy con métodos para el modelo Meme.

### �🛡️ Políticas de Autorización
**Archivo:** `app/Policies/MemePolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\Meme;
use App\Models\User;

class MemePolicy
{
    public function update(User $user, Meme $meme): bool
    {
        return $meme->user_id === $user->id;
    }

    public function delete(User $user, Meme $meme): bool
    {
        return $meme->user_id === $user->id;
    }
}
```

### ⚠️ Importante: Trait AuthorizesRequests
Para usar `$this->authorize()`, el controlador debe incluir el trait:

```php
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
```

### 🎮 Usar en el Controlador
**Archivo:** `app/Http/Controllers/MemeController.php`

```php
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MemeController extends Controller
{
    use AuthorizesRequests;

    public function edit(Meme $meme)
    {
        // Verifica si el usuario puede actualizar
        $this->authorize('update', $meme);

        return view('memes.edit', compact('meme'));
    }

    public function update(Request $request, Meme $meme)
    {
        $this->authorize('update', $meme);

        $validated = $request->validate([...]);
        $meme->update($validated);

        return redirect('/')->with('success', '¡Meme actualizado!');
    }

    public function destroy(Meme $meme)
    {
        $this->authorize('delete', $meme);

        $meme->delete();

        return redirect('/')->with('success', '¡Meme eliminado!');
    }
}
```

**¿Qué hace `$this->authorize()`?**

1. Llama al método correspondiente de `MemePolicy`
2. Si devuelve `false`, lanza error 403 (Forbidden)
3. Si devuelve `true`, continúa la ejecución

---

## 8. Vista con @can

### 🎨 Mostrar/Ocultar Elementos según Permisos
**Archivo:** `resources/views/components/meme.blade.php`

```blade
@can('update', $meme)
    <div class="flex gap-1">
        <a href="/memes/{{ $meme->id }}/edit">Editar</a>
        
        <form method="POST" action="/memes/{{ $meme->id }}">
            @csrf
            @method('DELETE')
            <button type="submit">Eliminar</button>
        </form>
    </div>
@endcan
```

**¿Qué hace `@can`?**

```blade
@can('update', $meme)
    <!-- Solo se muestra si el usuario PUEDE actualizar -->
@endcan
```

- Verifica el permiso usando la Policy
- Si el usuario NO tiene permiso, el contenido ni siquiera aparece en el HTML
- Solo el creador del meme verá los botones de editar/eliminar

---

## 🎯 Resumen

Tu aplicación ahora tiene:

✅ **Formulario de registro** con validación completa
✅ **Controlador invokable** que crea usuarios y los loguea automáticamente
✅ **Middleware `guest`** en rutas de autenticación
✅ **Header dinámico** que muestra usuario o botones de login
✅ **Middleware `auth`** protegiendo rutas sensibles
✅ **Asociación automática** de memes con usuarios mediante `auth()->user()`
✅ **Autorización con Policies** usando `$this->authorize()`
✅ **Control de UI** con `@can` para mostrar botones según permisos

---

## 👥 Usuarios de Prueba

Puedes probar la aplicación con estos usuarios:

### Usuario 1: Test User
```
📧 Email: test@example.com
🔑 Contraseña: password
```

### Usuario 2: Sofía López
```
📧 Email: sofia@example.com
🔑 Contraseña: password123
```

### Usuario 3: Miguel Fernández
```
📧 Email: miguel@example.com
🔑 Contraseña: 12345678
```

### Usuario 4: Laura Martínez
```
📧 Email: laura@example.com
🔑 Contraseña: larau123
```

---

### 🧪 Pruebas Sugeridas

1. **Regístrate** con un nuevo usuario
2. **Crea un meme** - verás tu nombre asociado
3. **Cierra sesión** y entra con otro usuario
4. **Verifica** que solo puedes editar/eliminar tus propios memes
5. **Intenta acceder** a `/memes/1/edit` de otro usuario (error 403)

**Nota:** Ejecuta `php artisan db:seed` para crear los usuarios de prueba.

---

**¡Sistema de autenticación implementado correctamente! 🎉**
