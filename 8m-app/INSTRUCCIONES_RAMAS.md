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
cd trabajoClase8M/8m-app
```

### 2. Instalar dependencias
```bash
composer install
npm install
```

### 3. Configurar el entorno
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Crear la base de datos
```bash
touch database/database.sqlite
php artisan migrate
php artisan db:seed
```

### 5. Seguir la guía
Abre el archivo `GUIA_AUTH_CLASE.md` y sigue los 8 pasos para implementar autenticación.


