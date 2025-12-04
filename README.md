
# 🚀 Proyecto NexusV‑V2

### Plataforma de Cursos con Control Jerárquico Avanzado

**NexusV‑V2** es una plataforma web desarrollada en **Laravel 11+** que
simula un sistema completo de **gestión y venta de cursos en tiempo
real**.\
El proyecto se centra fuertemente en la **administración de roles**, el
**control de permisos** y la gestión integral de usuarios, cursos e
inscripciones.

Su estructura se basa en cuatro perfiles principales:\
**Administrador Maestro**, **Administrador Secundario**, **Vendedor** y
**Comprador**, cada uno con capacidades cuidadosamente aisladas mediante
**Gates**, **Policies** y verificaciones adicionales.

------------------------------------------------------------------------

## 📚 Índice

1.  [🔑 Roles y Jerarquía](#-roles-y-jerarquía)\
2.  [💡 Lecciones Aprendidas](#-lecciones-aprendidas)\
3.  [⚙️ Instalación Local](#️-instalación-local)\
4.  [🧪 Flujo de Pruebas](#-flujo-de-pruebas)\
5.  [🌎 Despliegue y Repositorio](#-despliegue-y-repositorio)

------------------------------------------------------------------------

## 🔑 Roles y Jerarquía

La plataforma implementa un sistema jerárquico pensado para operaciones
reales de manejo de personal y control administrativo.

### 🏆 **Administrador Maestro (Super Admin)**

-   Acceso total a todo el sistema.\
-   Puede crear/eliminar a cualquier usuario, incluyendo Admins
    Secundarios.\
-   Inmune a restricciones de edición y propiedad en cursos y recursos.\
-   Puede ver, modificar y eliminar **cualquier** registro sin
    limitación.

### 🛡️ **Administrador Secundario**

-   Maneja tareas operativas: usuarios, cursos e inscripciones.\
-   **No puede modificar ni eliminar** al Administrador Maestro.\
-   Puede editar cursos de vendedores, pero siempre bajo restricciones
    de seguridad.

### 🛒 **Vendedor**

-   Puede crear, gestionar y publicar cursos propios.\
-   No puede editar cursos de otros vendedores.\
-   Interfaz reducida enfocada únicamente en su catálogo.

### 🎓 **Comprador**

-   Puede explorar el catálogo.\
-   Puede inscribirse en cursos y visualizarlos en su dashboard
    personal.\
-   Acceso limitado únicamente a experiencias de aprendizaje.

------------------------------------------------------------------------

## 💡 Lecciones Aprendidas

Durante el desarrollo del proyecto se encontraron problemas técnicos
complejos que ayudaron a fortalecer la estabilidad del sistema:

### 🔧 Problemas y Soluciones

-   **Clases críticas de Breeze no generadas**\
    Breeze omitió archivos esenciales como
    `AuthenticatedSessionController`.\
    → *Solución:* creación manual, revisión de namespaces y limpieza del
    entorno.

-   **Error 403 para el Admin Maestro al editar cursos ajenos**\
    La verificación `$course->user_id === Auth::id()` bloqueaba al Admin
    Maestro.\
    → *Solución:* excepción explícita mediante
    `if (Auth::user()->isMasterAdmin())`.

-   **Esquema de base de datos corrupto**\
    La tabla `enrollments` se generó sin `course_id`.\
    → *Solución:* `php artisan migrate:fresh` y verificación del esquema
    completo.

-   **Problemas con alias de rutas en Windows**\
    Vistas que dependían de `route('seller.courses.index')` fallaban.\
    → *Solución:* uso directo de rutas absolutas (`/seller/courses`)
    para mejorar compatibilidad.

------------------------------------------------------------------------

## ⚙️ Instalación Local

Requisitos previos:\
✔️ PHP 8.2+\
✔️ Composer\
✔️ Node.js + NPM\
✔️ SQLite / MySQL / PostgreSQL

------------------------------------------------------------------------

### 🔹 **Paso 1: Clonar e Instalar Dependencias**

``` bash
git clone https://github.com/AnthonnyM31/Proyecto_NexusV-V2.git
cd Proyecto_NexusV-V2

copy .env.example .env
php artisan key:generate

composer install
npm install
```

------------------------------------------------------------------------

### 🔹 **Paso 2: Configurar, Migrar y Crear Usuario Maestro**

``` bash
# Crear base de datos SQLite (opcional)
touch database/database.sqlite

# Ejecutar migraciones
php artisan migrate

# Sembrar Administrador Maestro y usuarios de prueba
php artisan db:seed --class=AdminSeeder
```

Cuenta inicial: - **Email:** admin@nexusv.com\
- **Password:** password123

------------------------------------------------------------------------

### 🔹 **Paso 3: Ejecutar la Aplicación**

Ejecutar backend y frontend en paralelo:

``` bash
php artisan serve
npm run dev
```

URL local:\
👉 http://127.0.0.1:8000

------------------------------------------------------------------------

## 🧪 Flujo de Pruebas

### 🔸 **Administrador Maestro**

-   Acceso a panel global.\
-   Edición y eliminación de cualquier curso.\
-   Verificación de permisos sin restricciones.

### 🔸 **Vendedor**

-   Creación/publicación de cursos.\
-   Gestión acotada únicamente a su contenido.

### 🔸 **Comprador**

-   Inscripción en cursos.\
-   Visualización en "Mis Cursos Inscritos".

------------------------------------------------------------------------

## 🌎 Despliegue y Repositorio

El proyecto está optimizado para despliegue en **Render** utilizando
**PostgreSQL**.

🔗 Repositorio Oficial:\
https://github.com/AnthonnyM31/Proyecto_NexusV-V2
