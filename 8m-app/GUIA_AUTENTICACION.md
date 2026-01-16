# 🔐 Guía de Autenticación en Laravel - 8M-Chirper

Esta guía explica cómo hemos implementado un sistema completo de autenticación en nuestra aplicación de memes.

---

## 📋 Índice

1. [Registro de Usuarios](#1-registro-de-usuarios)
2. [Inicio de Sesión (Login)](#2-inicio-de-sesión-login)
3. [Cierre de Sesión (Logout)](#3-cierre-de-sesión-logout)
4. [Protección de Rutas](#4-protección-de-rutas)
5. [Autorización con Policies](#5-autorización-con-policies)
6. [Actualización de la Interfaz](#6-actualización-de-la-interfaz)

---

## 1. Registro de Usuarios

### 📄 Vista del Formulario
**Archivo:** `resources/views/auth/register.blade.php`

```blade
<form method="POST" action="/register">
    @csrf

    <!-- Campo Nombre -->
    <div class="mb-4">
        <label for="name">Nombre</label>
        <input type="text" name="name" value="{{ old('name') }}" required>
        @error('name')
            <p class="text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Campo Email -->
    <div class="mb-4">
        <label for="email">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email')
            <p class="text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <!-- Campo Contraseña -->
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
```

**¿Qué hace cada parte?**

- `@csrf`: Token de seguridad obligatorio en todos los formularios POST/PUT/DELETE
- `value="{{ old('name') }}"`: Mantiene el valor anterior si hay errores de validación
- `@error('name')`: Muestra mensajes de error específicos de cada campo
- `password_confirmation`: Laravel automáticamente valida que coincida con `password`

---

### 🎮 Controlador de Registro
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
        // 1. Validar los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Crear el usuario con contraseña hasheada
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 3. Iniciar sesión automáticamente
        Auth::login($user);

        // 4. Redirigir a la página principal
        return redirect('/')->with('success', '¡Bienvenido/a a 8M-Chirper!');
    }
}
```

**Conceptos importantes:**

#### ✅ Validación
```php
'email' => 'required|string|email|max:255|unique:users'
```
- `required`: Campo obligatorio
- `email`: Debe ser formato de email válido
- `unique:users`: El email no debe existir en la tabla `users`

#### 🔒 Hash de Contraseñas
```php
'password' => Hash::make($validated['password'])
```
**¡NUNCA guardes contraseñas en texto plano!** Laravel usa bcrypt para hashear de forma segura.

#### 🔑 Login Automático
```php
Auth::login($user);
```
Inicia sesión al usuario recién creado automáticamente.

---

## 2. Inicio de Sesión (Login)

### 📄 Vista del Formulario
**Archivo:** `resources/views/auth/login.blade.php`

```blade
<form method="POST" action="/login">
    @csrf

    <div class="mb-4">
        <label for="email">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required>
        @error('email')
            <p class="text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label for="password">Contraseña</label>
        <input type="password" name="password" required>
        @error('password')
            <p class="text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit">Entrar</button>
</form>
```

---

### 🎮 Controlador de Login
**Archivo:** `app/Http/Controllers/Auth/Login.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Login extends Controller
{
    public function __invoke(Request $request)
    {
        // 1. Validar credenciales
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Intentar autenticar
        if (Auth::attempt($credentials)) {
            // 3. Regenerar sesión (seguridad)
            $request->session()->regenerate();

            return redirect('/')->with('success', '¡Bienvenido/a de nuevo!');
        }

        // 4. Si falla, volver con error
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }
}
```

**¿Qué hace `Auth::attempt()`?**

```php
Auth::attempt(['email' => 'user@example.com', 'password' => 'secret'])
```

Laravel automáticamente:
1. Busca el usuario por email
2. Compara la contraseña hasheada
3. Si coincide, inicia la sesión
4. Devuelve `true` o `false`

**Seguridad:**
```php
$request->session()->regenerate();
```
Regenera el ID de sesión para prevenir ataques de fijación de sesión.

---

## 3. Cierre de Sesión (Logout)

### 🎮 Controlador de Logout
**Archivo:** `app/Http/Controllers/Auth/Logout.php`

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Logout extends Controller
{
    public function __invoke(Request $request)
    {
        // 1. Cerrar sesión del usuario
        Auth::logout();

        // 2. Invalidar la sesión actual
        $request->session()->invalidate();

        // 3. Regenerar el token CSRF
        $request->session()->regenerateToken();

        return redirect('/')->with('success', '¡Sesión cerrada!');
    }
}
```

**¿Por qué tres pasos?**

1. `Auth::logout()`: Elimina la autenticación del usuario
2. `invalidate()`: Destruye todos los datos de la sesión
3. `regenerateToken()`: Previene ataques CSRF con tokens antiguos

---

## 4. Protección de Rutas

### 🛣️ Rutas con Middleware
**Archivo:** `routes/web.php`

```php
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;

// Ruta pública (todos pueden verla)
Route::get('/', [MemeController::class, 'index']);

// Rutas para INVITADOS (guest middleware)
Route::view('/register', 'auth.register')->middleware('guest');
Route::post('/register', Register::class)->middleware('guest');
Route::view('/login', 'auth.login')->middleware('guest');
Route::post('/login', Login::class)->middleware('guest');

// Ruta para AUTENTICADOS (auth middleware)
Route::post('/logout', Logout::class)->middleware('auth');

// Grupo de rutas protegidas
Route::middleware('auth')->group(function () {
    Route::post('/memes', [MemeController::class, 'store']);
    Route::get('/memes/{meme}/edit', [MemeController::class, 'edit']);
    Route::put('/memes/{meme}', [MemeController::class, 'update']);
    Route::delete('/memes/{meme}', [MemeController::class, 'destroy']);
});
```

**¿Qué son los Middleware?**

Los middleware son "filtros" que se ejecutan antes de llegar al controlador.

#### Middleware `guest`
```php
->middleware('guest')
```
- Solo usuarios NO autenticados pueden acceder
- Si estás logueado, te redirige a `/home`
- Útil para páginas de login/registro

#### Middleware `auth`
```php
->middleware('auth')
```
- Solo usuarios autenticados pueden acceder
- Si NO estás logueado, te redirige a `/login`
- Protege rutas sensibles

#### Agrupar rutas con middleware
```php
Route::middleware('auth')->group(function () {
    // Todas estas rutas están protegidas
    Route::post('/memes', ...);
    Route::delete('/memes/{meme}', ...);
});
```

---

## 5. Autorización con Policies

Las Policies controlan **quién puede hacer QUÉ** con los recursos.

### 📜 Policy de Memes
**Archivo:** `app/Policies/MemePolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\Meme;
use App\Models\User;

class MemePolicy
{
    /**
     * Determina si el usuario puede actualizar el meme
     */
    public function update(User $user, Meme $meme): bool
    {
        return $meme->user_id === $user->id;
    }

    /**
     * Determina si el usuario puede eliminar el meme
     */
    public function delete(User $user, Meme $meme): bool
    {
        return $meme->user_id === $user->id;
    }
}
```

**Explicación:**

Solo el **creador del meme** (`user_id`) puede editarlo o eliminarlo.

---

### 🎮 Usar Policies en el Controlador
**Archivo:** `app/Http/Controllers/MemeController.php`

```php
public function edit(Meme $meme)
{
    // Verificar autorización
    $this->authorize('update', $meme);

    return view('memes.edit', compact('meme'));
}

public function update(Request $request, Meme $meme)
{
    $this->authorize('update', $meme);

    $validated = $request->validate([
        'meme_url' => 'required|url|max:500',
        'explicacion' => 'required|string|max:1000',
    ]);

    $meme->update($validated);

    return redirect('/')->with('success', '¡Meme actualizado!');
}

public function destroy(Meme $meme)
{
    $this->authorize('delete', $meme);

    $meme->delete();

    return redirect('/')->with('success', '¡Meme eliminado!');
}
```

**¿Qué hace `$this->authorize()`?**

```php
$this->authorize('update', $meme);
```

1. Llama al método `update()` de `MemePolicy`
2. Pasa el usuario autenticado y el meme
3. Si devuelve `false`, lanza error 403 (Forbidden)
4. Si devuelve `true`, continúa la ejecución

---

### 🎨 Usar Policies en las Vistas
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

**Directiva `@can`:**

```blade
@can('update', $meme)
    <!-- Solo se muestra si el usuario PUEDE actualizar -->
@endcan
```

Si el usuario NO es el creador, los botones ni siquiera aparecen en el HTML.

---

## 6. Actualización de la Interfaz

### 🎨 Header con Autenticación
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

**Directivas de Blade:**

#### `@auth` / `@guest`
```blade
@auth
    <!-- Se muestra si el usuario está autenticado -->
@endauth

@guest
    <!-- Se muestra si el usuario NO está autenticado -->
@endguest
```

#### `auth()->user()`
```blade
{{ auth()->user()->name }}
{{ auth()->user()->email }}
{{ auth()->user()->id }}
```
Accede a los datos del usuario autenticado actual.

---

### 🔗 Asociar Memes con Usuarios
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

1. `auth()->user()`: Obtiene el usuario autenticado
2. `->memes()`: Accede a la relación hasMany definida en el modelo User
3. `->create([...])`: Crea un nuevo meme asociado automáticamente

Laravel automáticamente establece `user_id` con el ID del usuario actual.

---

## 📚 Modelo User con Relaciones

**Archivo:** `app/Models/User.php`

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    public function memes(): HasMany
    {
        return $this->hasMany(Meme::class);
    }
}
```

**Relación hasMany:**

"Un usuario tiene muchos memes"

```php
$user->memes; // Colección de todos los memes del usuario
$user->memes()->create([...]); // Crear nuevo meme asociado
$user->memes()->count(); // Contar cuántos memes tiene
```

---

## 🔄 Flujo Completo de Autenticación

### Registro:
1. Usuario llena formulario → `/register` (POST)
2. `Register` controller valida datos
3. Crea usuario con contraseña hasheada
4. Inicia sesión automáticamente con `Auth::login()`
5. Redirige a home

### Login:
1. Usuario ingresa credenciales → `/login` (POST)
2. `Login` controller valida con `Auth::attempt()`
3. Si es correcto, regenera sesión
4. Redirige a home

### Crear Meme (Protegido):
1. Usuario crea meme → `/memes` (POST)
2. Middleware `auth` verifica autenticación
3. Controller asocia meme con `auth()->user()`
4. Guarda en base de datos con `user_id`

### Editar Meme (Protegido + Autorizado):
1. Usuario hace clic en "Editar"
2. Middleware `auth` verifica autenticación
3. Policy verifica que `user_id === auth()->user()->id`
4. Si pasa, muestra formulario de edición

### Logout:
1. Usuario hace clic en "Cerrar Sesión" → `/logout` (POST)
2. `Logout` controller cierra sesión
3. Invalida sesión y regenera token
4. Redirige a home

---

## 🔑 Conceptos Clave para Recordar

### 1. Hash de Contraseñas
```php
// ✅ CORRECTO
Hash::make('password123')

// ❌ NUNCA HAGAS ESTO
'password' => $request->password
```

### 2. Middleware
- `guest`: Solo invitados (no autenticados)
- `auth`: Solo autenticados
- Se aplican en rutas con `->middleware('auth')`

### 3. Policies
- Controlan **permisos** sobre recursos específicos
- Método en Policy: `public function update(User $user, Meme $meme)`
- Uso en controller: `$this->authorize('update', $meme)`
- Uso en vistas: `@can('update', $meme)`

### 4. Helpers de Autenticación
```php
auth()->user()        // Usuario actual
auth()->id()          // ID del usuario
auth()->check()       // ¿Está autenticado? (true/false)
auth()->guest()       // ¿Es invitado? (true/false)
```

### 5. Directivas Blade
```blade
@auth ... @endauth      // Si está autenticado
@guest ... @endguest    // Si NO está autenticado
@can('update', $meme)   // Si tiene permiso
```

---

## 🎯 Resultado Final

Ahora la aplicación tiene:

✅ Sistema completo de registro/login/logout
✅ Contraseñas seguras con hash
✅ Rutas protegidas con middleware
✅ Autorización basada en policies
✅ UI que muestra/oculta elementos según autenticación
✅ Memes asociados a usuarios
✅ Solo el creador puede editar/eliminar sus memes

---

## 🚀 Próximos Pasos

Puedes mejorar la autenticación con:

- **Verificación de email**: Confirmar email antes de usar la cuenta
- **Recuperación de contraseña**: Sistema "Olvidé mi contraseña"
- **Remember me**: Checkbox para recordar sesión
- **Roles y permisos**: Admin, moderador, usuario normal
- **Two-Factor Authentication (2FA)**: Capa extra de seguridad

---

**¡Felicidades! Has implementado un sistema de autenticación completo en Laravel 🎉**

---

## 👥 Usuarios de Prueba

Para probar la aplicación, puedes usar estos usuarios que ya están registrados en la base de datos:

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

1. **Inicia sesión** con cualquiera de estos usuarios
2. **Crea un nuevo meme** con tu usuario
3. **Intenta editar** un meme de otro usuario (verás que no aparecen los botones)
4. **Cierra sesión** y vuelve a entrar con otro usuario
5. **Verifica** que cada usuario solo puede editar/eliminar sus propios memes

**Nota:** Si ejecutaste las migraciones con `--seed`, estos usuarios ya están disponibles. Si no, ejecuta:
```bash
php artisan db:seed
```

