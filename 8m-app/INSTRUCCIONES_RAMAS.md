# 📚 Instrucciones para usar el proyecto 8M-Chirper

Este proyecto tiene dos ramas para facilitar la enseñanza de autenticación en Laravel.

## 🌿 Ramas disponibles

### `main` - Versión completa
Contiene el proyecto **CON autenticación completa** implementada:
- ✅ Sistema de registro
- ✅ Sistema de login/logout
- ✅ Protección de rutas con middleware
- ✅ Autorización con Policies
- ✅ Directivas @auth y @can

**Usar esta rama para:** Ver el código final, demostrar funcionamiento completo.

### `leccion-10-base` - Para práctica de estudiantes
Contiene el proyecto **SIN autenticación**, listo para que los estudiantes la implementen:
- ✅ Modelos (User, Meme, Chirp)
- ✅ Migraciones y relaciones
- ✅ CRUD de memes funcionando
- ✅ Vistas y componentes base
- ❌ **NO** tiene autenticación
- 📝 Incluye comentarios `TODO` en el código

**Usar esta rama para:** Que los estudiantes practiquen siguiendo `GUIA_AUTH_CLASE.md`

---

## 🎓 Para estudiantes

### 1. Clonar el repositorio
```bash
git clone <URL-DEL-REPO>
cd chrips-8m/8m-app
```

### 2. Cambiar a la rama de práctica
```bash
git checkout leccion-10-base
```

### 3. Instalar dependencias
```bash
composer install
npm install
```

### 4. Configurar el entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Crear la base de datos
```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

### 6. Seguir la guía
Abre el archivo `GUIA_AUTH_CLASE.md` y sigue los 8 pasos para implementar autenticación.

### 7. Verificar tu trabajo (opcional)
Si te atascas o quieres comparar tu solución:
```bash
git checkout main
```

---

## 👨‍🏫 Para profesores

### Preparar el entorno para la clase

```bash
# Subir ambas ramas al repositorio remoto
git push origin main
git push origin leccion-10-base

# Compartir URL del repo con estudiantes
# Indicarles que clonen y usen la rama leccion-10-base
```

### Estructura de la clase

1. **Demostración (15 min)**: Mostrar app funcionando en rama `main`
2. **Explicación (10 min)**: Explicar qué van a implementar
3. **Práctica (60 min)**: Estudiantes siguen `GUIA_AUTH_CLASE.md` en rama `leccion-10-base`
4. **Revisión (15 min)**: Comparar con rama `main`

---

## 🔄 Diferencias entre ramas

| Característica | `leccion-10-base` | `main` |
|---------------|-------------------|--------|
| Rutas de auth | ❌ No existen | ✅ /register, /login, /logout |
| Controladores Auth | ❌ No existen | ✅ Register, Login, Logout |
| Middleware auth | ❌ Sin protección | ✅ Rutas protegidas |
| MemePolicy | ❌ No existe | ✅ Implementada |
| @auth en layout | ❌ Navegación estática | ✅ Dinámica |
| @can en vistas | ❌ Botones siempre visibles | ✅ Según permisos |
| auth()->user() | ❌ Usa User::first() | ✅ Usuario autenticado |

---

## 📝 Archivos a crear en la práctica

Los estudiantes crearán estos archivos siguiendo la guía:

```
resources/views/auth/
├── register.blade.php

app/Http/Controllers/Auth/
├── Register.php

app/Policies/
├── MemePolicy.php
```

Y modificarán:
- `routes/web.php`
- `app/Http/Controllers/MemeController.php`
- `resources/views/components/layout.blade.php`
- `resources/views/components/meme.blade.php`

---

**¡Buena suerte con la clase! 🎉**
