# Finca — App Productividad Agrícola

Sistema web para gestión de productividad agrícola: lotes, ciclos de siembra, cosechas, costos, rentabilidad, calendario y reportes. Multiplataforma responsive, PWA offline-first.

---

## Stack técnico

| Capa | Tecnología | Versión |
|---|---|---|
| Backend | Laravel | 12.x |
| Lenguaje | PHP | 8.4.x (Herd) / 8.2.12 (XAMPP — legacy) |
| Frontend | Inertia.js | 2.x |
| UI Framework | Vue | 3.x (Composition API + `<script setup>`) |
| Build tool | Vite | 8.x |
| Estilos | Tailwind CSS | 4.x |
| Base de datos | MySQL / MariaDB | 8.x (Herd incluido) |
| Storage fotos | MinIO (S3-compatible) | latest (Docker Desktop o binario Windows) |
| Autenticación | Laravel Breeze (Inertia Vue stack) | — |
| Permisos | spatie/laravel-permission | 7.x |
| Cola | Laravel Queue (DB driver) | — |
| Mail | SMTP local (Mailhog dev) | — |
| Mapas | Leaflet + OpenStreetMap | — |
| Calendario | vue-cal o custom CSS grid | — |
| Charts | Chart.js + vue-chartjs | — |
| PWA | vite-plugin-pwa + Workbox | — |
| Tests backend | Pest PHP | — |
| Tests frontend | Vitest | — |

### Stack confirmado por el dueño

- ✅ Inertia.js + Vue 3 SPA
- ✅ Roles: Admin, Supervisor, Operador
- ✅ Área en manzanas (Mz)
- ✅ Moneda: NIO (Córdoba nicaragüense)
- ✅ Storage: MinIO (S3-compatible local)
- ✅ Alertas: email + notificación in-app
- ✅ PWA offline-first con sync
- ✅ Deploy: servidor local de la finca
- ✅ Tailwind CSS 4

---

## Entorno de desarrollo (Windows + Laravel Herd)

### Estado actual (30/06/2026)

| Componente | Estado | Notas |
|---|---|---|
| Laravel Herd GUI | ✅ Instalado `%LOCALAPPDATA%\Programs\Herd` | No ejecutado aún |
| Herd PHP CLI | ❌ `%USERPROFILE%\.config\herd\bin` vacío | Abrir Herd GUI primero |
| PHP activo | ⚠️ XAMPP (`E:\xampp\php\php.exe` 8.2.12) | Herd aún no en PATH |
| MySQL activo | ❌ No detectado en PATH | Herd incluye MariaDB |
| Composer | ✅ 2.8.4 | Instalación global |
| Node | ✅ v24.15.0 | — |
| npm | ✅ 11.17.0 | — |
| Laravel Installer | ✅ 5.24.5 | — |

### Setup inicial (una sola vez)

```powershell
# 1. Detener XAMPP (conflicto puertos 80/443/3306)
Stop-Service -Name Apache2.4, mysql -ErrorAction SilentlyContinue

# 2. Abrir Laravel Herd desde Inicio → "Laravel Herd"
#    Click "Start" → aceptar permisos admin
#    Minimizar a bandeja

# 3. Cerrar y reabrir terminal (PowerShell / Windsurf / VS Code)

# 4. Verificar
php -v                 # Debe mostrar PHP 8.3.x de Herd
mysql --version        # MariaDB
composer --version     # 2.8.x
laravel --version      # 5.24.x
node -v && npm -v      # 24.x / 11.x
```

### Configuración php.ini (Herd)

Herd administra `php.ini` desde su GUI. Verificar:
- `extension=zip`
- `extension=gd`
- `extension=intl`
- `extension=bcmath`
- `extension=pdo_mysql`
- `extension=fileinfo`
- `extension=exif`
- `upload_max_filesize = 40M`
- `post_max_size = 40M`
- `memory_limit = 512M`

### Creación del proyecto Laravel

```powershell
cd E:\Documents\MarangeloDev\Finca

# Crear proyecto con Inertia + Vue + SSR + Pest + Git init
laravel new finca --stack=vue --ssr --pest --git

cd finca

# Migrar Tailwind 3→4 y Vite a 8.x
npm uninstall tailwindcss postcss autoprefixer
npm install tailwindcss@latest @tailwindcss/vite@latest
npm install -D @vitejs/plugin-vue@latest

# Vite 8 requiere Node 20.19+ (tienes 24.x, ok)
```

**Setup post-instalación:**

1. `resources/css/app.css` — reemplazar:
   ```css
   /* Antes (Tailwind 3) */
   @tailwind base;
   @tailwind components;
   @tailwind utilities;

   /* Después (Tailwind 4) */
   @import "tailwindcss";
   @custom-variant dark (&:where(.dark, .dark *));
   ```

2. `vite.config.js` — agregar `tailwindcss()` plugin:
   ```js
   import { defineConfig } from 'vite'
   import laravel from 'laravel-vite-plugin'
   import vue from '@vitejs/plugin-vue'
   import tailwindcss from '@tailwindcss/vite'

   export default defineConfig({
     plugins: [
       tailwindcss(),
       vue(),
       laravel({ input: 'resources/js/app.js' }),
     ],
   })
   ```

3. Eliminar `postcss.config.js` y `tailwind.config.js` (ya no se usan).

### Configuración .env

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=finca
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=minioadmin
AWS_SECRET_ACCESS_KEY=minioadmin
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=fotos
AWS_ENDPOINT=http://127.0.0.1:9000
AWS_USE_PATH_STYLE_ENDPOINT=true

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@finca.local"
MAIL_FROM_NAME="Finca App"
```

### MinIO en Windows

```powershell
# Opción A: Docker Desktop (recomendada)
docker run -d -p 9000:9000 -p 9001:9001 `
  --name minio `
  -e MINIO_ROOT_USER=minioadmin `
  -e MINIO_ROOT_PASSWORD=minioadmin `
  -v minio_data:/data `
  quay.io/minio/minio server /data --console-address ":9001"

# Abrir http://127.0.0.1:9001 → login minioadmin/minioadmin
# Crear bucket "fotos" → Access Policy: Private

# Opción B: Binario Windows (sin Docker)
# Descargar https://dl.min.io/server/minio/release/windows-amd64/minio.exe
# mover a C:\minio\minio.exe
C:\minio\minio.exe server C:\minio\data --console-address :9001
```

### Verificación final

```powershell
php artisan serve
# Abrir http://127.0.0.1:8000 → página default Laravel
```

---

## Roles y permisos

| Rol | Permisos |
|---|---|
| `admin` | CRUD todo + usuarios + catálogos + reportes globales |
| `supervisor` | Ver/editar lotes asignados, registrar cosechas, reportes de su scope |
| `operador` | Ver lotes asignados, registrar cosechas/subir fotos en ciclos activos |

Implementación: `spatie/laravel-permission` + Policies Laravel.

Asignación lote ↔ usuario mediante tabla pivote `lote_user` con columna `rol_en_lote`.

---

## Modelo de datos

### Diagrama lógico

```
users ──┬──< ciclos_siembra (created_by)
        ├──< cosechas (registrado_por)
        ├──< fotos (uploaded_by)
        └──< lote_user >── lotes

lotes ──┬──< ciclos_siembra
        ├──< fotos (polimorphic)
        └──< lote_user

ciclos_siembra ──┬──< ciclo_insumos >── insumos
                 ├──< ciclo_mano_obra
                 ├──< ciclo_otros_costos
                 ├──< cosechas
                 └──< fotos (polymorphic)
```

### Esquema de tablas

**users**
- `id` bigint PK
- `name` varchar(255)
- `email` varchar(255) unique
- `password` varchar(255)
- `phone` varchar(20) nullable
- `role` enum('admin','supervisor','operador') — espejo de spatie
- timestamps

**roles, permissions, model_has_roles, model_has_permissions**
- tablas de `spatie/laravel-permission` (migración publicada)

**lotes**
- `id` bigint PK
- `codigo` varchar(20) unique
- `nombre` varchar(255)
- `area_manzanas` decimal(10,4)
- `tipo_suelo` varchar(100) nullable
- `lat` decimal(10,7) nullable
- `lng` decimal(10,7) nullable
- `descripcion` text nullable
- `activo` bool default true
- `created_by` bigint FK → users
- timestamps
- index: `(activo)`

**cultivos**
- `id` bigint PK
- `nombre` varchar(255)
- `variedad` varchar(255) nullable
- `ciclo_dias` int unsigned
- `unidad_cosecha_default` enum('quintal','libra','kg','unidad')
- `activo` bool default true
- timestamps

**ciclos_siembra**
- `id` bigint PK
- `lote_id` bigint FK → lotes
- `cultivo_id` bigint FK → cultivos
- `fecha_siembra` date
- `fecha_cosecha_estimada` date (calculated: fecha_siembra + cultivo.ciclo_dias)
- `fecha_cosecha_real` date nullable
- `estado` enum('planificado','activo','cosechado','cancelado')
- `notas` text nullable
- `created_by` bigint FK → users
- timestamps
- index: `(lote_id, estado)`, `(fecha_cosecha_estimada)`

**insumos** (catálogo)
- `id` bigint PK
- `nombre` varchar(255)
- `tipo` enum('semilla','fertilizante','pesticida','herbicida','otro')
- `unidad` enum('kg','libra','litro','unidad')
- timestamps

**ciclo_insumos** (movimientos por ciclo)
- `id` bigint PK
- `ciclo_id` bigint FK → ciclos_siembra
- `insumo_id` bigint FK → insumos
- `cantidad` decimal(12,3)
- `costo_unitario` decimal(12,2)
- `costo_total` decimal(12,2) (calculated: cantidad × costo_unitario)
- `fecha` date nullable
- `notas` text nullable
- timestamps
- index: `(ciclo_id)`

**ciclo_mano_obra**
- `id` bigint PK
- `ciclo_id` bigint FK → ciclos_siembra
- `concepto` varchar(255)
- `personas` int unsigned
- `horas` decimal(8,2)
- `costo_hora` decimal(12,2)
- `costo_total` decimal(12,2) (calculated: personas × horas × costo_hora)
- `fecha` date nullable
- `notas` text nullable
- timestamps

**ciclo_otros_costos**
- `id` bigint PK
- `ciclo_id` bigint FK → ciclos_siembra
- `concepto` varchar(255)
- `monto` decimal(12,2)
- `fecha` date nullable
- `notas` text nullable
- timestamps

**cosechas** (múltiples por ciclo, cosechas parciales)
- `id` bigint PK
- `ciclo_id` bigint FK → ciclos_siembra
- `fecha` date
- `cantidad` decimal(12,3)
- `unidad` enum('quintal','libra','kg','unidad')
- `calidad` enum('A','B','C') nullable
- `perdidas_cantidad` decimal(12,3) nullable
- `perdidas_unidad` varchar(20) nullable
- `notas` text nullable
- `registrado_por` bigint FK → users
- timestamps
- index: `(ciclo_id, fecha)`

**fotos** (polimórfica — lote o ciclo)
- `id` bigint PK
- `fotoable_type` varchar(255)
- `fotoable_id` bigint
- `ruta` varchar(255) (path en MinIO)
- `mime` varchar(50)
- `size` bigint (bytes)
- `taken_at` datetime nullable
- `lat` decimal(10,7) nullable
- `lng` decimal(10,7) nullable
- `uploaded_by` bigint FK → users
- timestamps
- index: `(fotoable_type, fotoable_id)`

**lote_user** (asignación supervisor/operador a lote)
- `id` bigint PK
- `lote_id` bigint FK → lotes
- `user_id` bigint FK → users
- `rol_en_lote` enum('supervisor','operador')
- timestamps
- unique: `(lote_id, user_id)`

**notifications** (Laravel built-in)
- `id` char(36) PK
- `type` varchar(255)
- `notifiable_type` varchar(255)
- `notifiable_id` bigint
- `data` json
- `read_at` timestamp nullable
- timestamps
- index: `(notifiable_type, notifiable_id)`

### Moneda y unidades

- **Moneda:** NIO (Córdoba nicaragüense) — todos los campos monetarios `decimal(12,2)`
- **Área:** manzanas (1 Mz ≈ 0.7048 ha) — campo `decimal(10,4)`
- **Cosecha:** quintal por defecto (configurable por cultivo: quintal, libra, kg, unidad)
- **Insumos:** kg, libra, litro, unidad

---

## Pantallas y secciones

### Auth
- `/login` — Login con email + password
- `/forgot-password` — Solicitar reset
- `/reset-password` — Reset con token

### Dashboard (`/dashboard`)
**Admin/Supervisor:**
- KPI cards: lotes activos, ciclos en curso, cosechas próximas (7/15/30 días), costos vs ingresos del mes
- Gráfico rendimiento por cultivo (últ. 12 meses)
- Lista próximas cosechas
- Alertas activas

**Operador:**
- "Mis lotes asignados" (cards con foto)
- Acciones rápidas: Registrar cosecha, Subir foto, Reportar avance
- Pendientes sync offline

### Catálogos (solo Admin)
- `/cultivos` — CRUD: nombre, variedad, ciclo_dias, unidad_cosecha_default
- `/insumos` — CRUD: nombre, tipo, unidad

### Lotes
- `/lotes` — Index: tabla con filtros (activo, tipo suelo, cultivo actual), búsqueda, paginación, mini-mapa con marcadores Leaflet
- `/lotes/create`, `/lotes/{id}/edit` — Form: código, nombre, área (Mz), tipo suelo, mapa clickable (lat/lng), descripción, fotos iniciales subidas a MinIO
- `/lotes/{id}` — Show:
  - Header con foto principal, código, área, ubicación
  - Historial completo de ciclos (timeline vertical)
  - KPIs del lote: ciclos totales, producción total, costo total, ingreso total, margen
  - Galería de fotos
  - Botón "Nuevo ciclo"

### Ciclos de siembra
- `/ciclos` — Index: filtros (lote, cultivo, estado, rango fechas), tabla con código lote, cultivo, fechas, estado, días restantes
- `/ciclos/create` — Wizard 3 pasos:
  1. Seleccionar lote
  2. Cultivo + fecha siembra (auto-calcula `fecha_cosecha_estimada = fecha_siembra + cultivo.ciclo_dias`)
  3. Notas + fotos
- `/ciclos/{id}` — Show con tabs:
  - **General:** datos, estado, días para cosecha
  - **Costos:** sub-tabs Insumos / Mano de obra / Otros — tabla + form inline
  - **Cosechas:** tabla de cosechas parciales + botón "Registrar cosecha"
  - **Fotos:** galería drag-drop, subida múltiple con preview + compresión client-side
  - **Timeline:** log de eventos (created, costos, fotos, cosechas, cambios de estado)

### Cosechas
- Modal rápido desde ciclo o ruta dedicada
- Form: fecha, cantidad, unidad, calidad (A/B/C), pérdidas (cantidad + unidad)
- Notifica si cantidad < 80% de lo esperado

### Calendario (`/calendario`)
- Toggle vistas: **Mes** (grid mensual) / **Gantt** (filas = lotes, barras = ciclos coloreadas por cultivo)
- Filtros: cultivo, lote, rango fechas
- Click en evento → drawer con detalle del ciclo
- Código de color fijo por cultivo

### Reportes (`/reportes`)
- **Productividad por lote:** gráfico barras (kg/Mz por ciclo, tendencia)
- **Rentabilidad por cultivo:** ingresos − costos, margen %
- **Comparación interanual:** misma temporada, distintos años
- **Costos breakdown:** pie chart por tipo (insumos, MO, otros)
- Filtros globales: rango fechas, lote, cultivo
- Export PDF (DomPDF) y Excel (Maatwebsite/Laravel Excel)

### Notificaciones (`/notificaciones`)
- Bandeja in-app: lista con indicador leído/no leído
- Configuración por usuario: activar/desactivar email, umbral días (default 7)
- Email digest diario si hay alertas activas

### Usuarios (solo Admin)
- `/usuarios` — CRUD
- Asignar rol (admin/supervisor/operador)
- Asignar lotes al usuario (modal con checkboxes)

### Settings
- Perfil (nombre, email, teléfono, avatar)
- Cambio de contraseña
- Preferencias de notificación
- Configuración de la finca: nombre, logo, moneda (NIO), unidad área (Mz)

---

## Plan de desarrollo por fases

| Fase | Alcance | Duración |
|---|---|---|
| **0. Setup** | Laravel 12 + Inertia + Vue 3 + Vite + Tailwind + Docker (MySQL, MinIO, Mailhog) + Herd config + verificación | 1-2 días |
| **1. Auth + Roles + Layout** | Breeze-Inertia, spatie/laravel-permission, 3 roles, sidebar responsive, topbar, dark mode, seeders (admin, roles, 5 cultivos demo, 3 lotes demo) | 3-5 días |
| **2. Catálogos + Lotes** | CRUD cultivos (búsqueda+paginación), CRUD insumos, CRUD lotes con fotos a MinIO + Leaflet mapa | 4-6 días |
| **3. Ciclos + Cosechas** | CRUD ciclos con cálculo auto de cosecha estimada, estados, transiciones, cosecha parcial múltiple, galería con compresión client-side | 5-7 días |
| **4. Costos + Rentabilidad** | Tabs Insumos/MO/Otros en show ciclo, `RentabilidadService` con cache, cálculo rentabilidad en tiempo real | 3-4 días |
| **5. Dashboard + Calendario + Reportes** | KPIs dashboard, Gantt (vue-cal o custom), calendario mensual, Chart.js, export PDF/Excel | 5-7 días |
| **6. Alertas + Notificaciones** | Job `CheckUpcomingHarvests` (cron diario), `HarvestUpcomingNotification` (mail + DB), bandeja, config usuario | 2-3 días |
| **7. Multiusuario + Permisos finos** | Asignación lote↔usuario via `lote_user`, Policies Laravel, auditoría básica (created_by visible) | 2-3 días |
| **8. PWA + Offline sync** | vite-plugin-pwa + Workbox, manifest, IndexedDB queue (`idb`), sync con resolución timestamp+server-wins, UI pendientes | 4-6 días |
| **9. Deploy local + Hardening** | Script instalador, HTTPS local (mkcert), backup cron MySQL+MinIO a disco externo, documentación operador | 2-3 días |
| **10. Pruebas + Iteración** | Pest (modelos+policies+jobs), Vitest (composables críticos), pruebas de campo, iteración UX | continuo |

**Total estimado:** 30-45 días dev full-stack.

---

## Estructura de directorios

```
app/
  Http/
    Controllers/
      Lote/LoteController.php
      Lote/LoteFotoController.php
      Ciclo/CicloController.php
      Ciclo/CosechaController.php
      Ciclo/CostoController.php
      Catalogo/CultivoController.php
      Catalogo/InsumoController.php
      Reporte/ReporteController.php
      Calendario/CalendarioController.php
      Notificacion/NotificacionController.php
      Usuario/UsuarioController.php
      Dashboard/DashboardController.php
    Requests/       (Form Requests por entidad)
    Policies/       (LotePolicy, CicloPolicy, CosechaPolicy, CostoPolicy, etc.)
    Middleware/
      EnsureRole.php
    Resources/      (API Resources para sync offline)
  Models/
    Lote.php
    Cultivo.php
    CicloSiembra.php
    Insumo.php
    CicloInsumo.php
    CicloManoObra.php
    CicloOtroCosto.php
    Cosecha.php
    Foto.php
    LoteUser.php
  Jobs/
    CheckUpcomingHarvests.php
    SendHarvestReminderEmail.php
  Notifications/
    HarvestUpcomingNotification.php
  Services/
    RentabilidadService.php
    FotoUploadService.php
  Console/
    Kernel.php (cron schedule)
database/
  migrations/
    0001_create_users_table.php
    0002_create_permission_tables.php
    0003_create_lotes_table.php
    0004_create_cultivos_table.php
    0005_create_ciclos_siembra_table.php
    0006_create_insumos_table.php
    0007_create_ciclo_insumos_table.php
    0008_create_ciclo_mano_obra_table.php
    0009_create_ciclo_otros_costos_table.php
    0010_create_cosechas_table.php
    0011_create_fotos_table.php
    0012_create_lote_user_table.php
  seeders/
    DatabaseSeeder.php
    RolesSeeder.php
    CultivoCatalogoSeeder.php
    DemoDataSeeder.php
resources/
  js/
    Pages/
      Auth/
        Login.vue
        ForgotPassword.vue
        ResetPassword.vue
      Dashboard.vue
      Lotes/
        Index.vue
        Create.vue
        Edit.vue
        Show.vue
      Cultivos/
        Index.vue
        Create.vue
        Edit.vue
      Insumos/
        Index.vue
        Create.vue
        Edit.vue
      Ciclos/
        Index.vue
        Create.vue
        Show.vue
      Calendario/
        Index.vue
      Reportes/
        Index.vue
      Notificaciones/
        Index.vue
      Usuarios/
        Index.vue
        Create.vue
        Edit.vue
    Components/
      Ui/
        Button.vue
        Card.vue
        Modal.vue
        Table.vue
        Pagination.vue
        Badge.vue
        Breadcrumb.vue
      Fotografia/
        FotoUploader.vue
        FotoGallery.vue
      Mapa/
        LoteMapPicker.vue
        MiniMap.vue
      Calendario/
        CalendarGrid.vue
        GanttLite.vue
      Costos/
        InsumoForm.vue
        ManoObraForm.vue
        OtroCostoForm.vue
    Composables/
      useOfflineQueue.js
      usePhotoUpload.js
      useNotifications.js
      useRentabilidad.js
    Layouts/
      AppLayout.vue
      AuthLayout.vue
  css/
    app.css
routes/
  web.php    (rutas Inertia)
  api.php    (endpoints sync offline)
```

---

## Decisiones técnicas

### Backend

- **Permisos:** `spatie/laravel-permission` (maduro, comunidad, combina con Policies Laravel)
- **Cálculo fecha cosecha estimada:** en mutador/accessor del modelo `CicloSiembra`, no en DB
- **RentabilidadService:** servicio con cache que suma costos e ingresos de un ciclo/lote. Se invalida al crear/editar costo o cosecha
- **Cola de notificaciones:** `CheckUpcomingHarvests` job corre en `Schedule::dailyAt('06:00')`. Usa DB queue (sin Redis en servidor local)
- **Subida fotos:** upload a endpoint Laravel → Intervention/Image v3 para thumbnail (200x200) + original (1920px max) → ambos a MinIO
- **Compresión client-side:** opcional con `browser-image-compression` antes de upload (ahorra ancho de banda en campo)

### Frontend

- **Inertia `useForm`** + Form Requests Laravel para validación server-side. Errores via props de Inertia
- **Tablas:** componente `<Table>` con Tailwind 4 (utility classes). Sin TanStack (bundle más chico). Paginación server-side via Inertia
- **Mapas:** Leaflet + OSM tiles. Sin geocoding (input manual lat/lng o click en mapa). `@vue-leaflet/vue-leaflet` package
- **Gantt:** `vue-cal` (liviano, sin dependencias) o custom CSS grid con position absoluta. `dhtmlx-gantt` descartado por licencia
- **Charts:** Chart.js via `vue-chartjs`
- **PWA:** `vite-plugin-pwa` + Workbox. IndexedDB con `idb` (mínimo peso sobre Dexie)
- **i18n:** paraglide o `@intlify/unplugin-vue-i18n` con `es-NI` base. Preparar multi-idioma desde inicio
- **Dark mode:** clase `dark` en `<html>` con `@custom-variant dark` en CSS (Tailwind 4). Toggle guardado en localStorage

### Infraestructura

- **MinIO:** mismo que servidor de prod. En dev via Docker Desktop o binario Windows
- **Backup:** script PowerShell que ejecuta `mysqldump` + `mc cp` (MinIO Client) a disco externo. Cron via Windows Task Scheduler
- **HTTPS local:** `mkcert` para certificado local. Permite PWA en móviles conectados a WiFi de la finca
- **Server prod:** Ubuntu Server 24.04 LTS (cuando se decida). PHP 8.3 + Nginx + MySQL 8 + MinIO

---

## Convenciones de código

- **PHP:** PSR-12, `declare(strict_types=1)` en archivos nuevos, type hints en toda función
- **Vue:** Composition API + `<script setup>`, PascalCase para componentes, camelCase para composables
- **Tailwind:** utility-first. CSS custom solo para keyframes y animaciones complejas
- **Rutas:** nombres con prefijo según módulo: `lotes.index`, `lotes.show`, `ciclos.create`, etc.
- **Migraciones:** nombres con prefijo numérico (`0001_...`, `0002_...`) para orden explícito
- **Seeders:** cada módulo su propio seeder, `DatabaseSeeder` orquesta el orden
- **Commits:** Conventional Commits (`feat:`, `fix:`, `chore:`, `docs:`, `refactor:`)
- **Branching:** `main` protegido, features en `feat/<nombre>`, fixes en `fix/<nombre>`
- **Idioma:** código en inglés (variables, funciones, comentarios técnicos). UI en español Nicaragua (es-NI)

---

## Comandos útiles

```bash
# Dev
php artisan serve                   # Servidor dev en :8000
npm run dev                         # Vite HMR
php artisan queue:work              # Procesar cola notificaciones

# Migraciones
php artisan migrate                 # Migrar
php artisan migrate:fresh --seed    # Reset + seed

# Tests
php artisan test                    # Pest (backend)
npm run test                        # Vitest (frontend)

# Lint
./vendor/bin/pint                   # Laravel Pint (PSR-12)
npm run lint                        # ESLint + Prettier

# Tailwind 4 + Vite 8
npm install tailwindcss@latest @tailwindcss/vite@latest

# Crear archivos Laravel
php artisan make:model Lote -a      # Modelo + migración + factory + seeder + controller + policy + request

# MinIO CLI
mc alias set local http://127.0.0.1:9000 minioadmin minioadmin
mc ls local/fotos
mc cp backup.sql local/fotos/backups/

# Backup manual
mysqldump -u root finca > backup_$(Get-Date -Format yyyyMMdd).sql
```

---

## Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| **Red inestable en finca** — operadores pierden conexión | PWA offline-first + sync queue con reintentos (exponential backoff). Formularios se guardan en IndexedDB |
| **Pérdida de datos** — fallo disco, corruptión MySQL | Backup diario MySQL dump + MinIO files a disco externo. Windows Task Scheduler ejecuta script PowerShell. Retención 30 días |
| **Operadores no técnicos** — dificultad con UI compleja | Diseño icon-first, formularios de 1-2 taps para acciones frecuentes. Fotos como input principal sobre texto. Capacitación presencial de 30 min |
| **Capacidad del servidor** — fotos consumen espacio | 50.000 fotos ≈ 50 GB (comprimidas 1920px). HDD externo 1 TB cubre años. MinIO compresión opcional |
| **HTTPS para PWA en móvil** — PWA requiere HTTPS | `mkcert` genera certificado local. Servir con IP fija del equipo finca + puerto 443 via Nginx de Herd |
| **PHP 8.2 vs 8.3** — XAMPP arrastra versión antigua | Migrar a Herd (PHP 8.3). XAMPP desinstalar o mantener como backup sin servicios activos |
| **Conflictos de sync offline** — mismo registro editado offline y online | Timestamp `updated_at` en cada modelo. Estrategia server-wins: servidor rechaza update si `updated_at` local es anterior al server. UI muestra conflicto y pide revisión |
| **Costo de almacenamiento futuro** — MinIO en servidor local puede migrar a cloud | Laravel Flysystem abstrae storage. Cambiar driver S3 de MinIO a DigitalOcean Spaces o Cloudflare R2 sin tocar código |

---

## Costos estimados

| Concepto | Costo |
|---|---|
| Licencias (Laravel, Vue, MySQL, MinIO, Leaflet, Chart.js) | $0 (todo open source / free tier) |
| Servidor local (mini PC / PC existente) | $0 (si ya tenés) o ~$300-500 una vez |
| Docker Desktop (dev) | $0 (Personal license) |
| Internet | $0 (no crítico, solo actualizaciones) |
| Mantenimiento anual (backups, actualizaciones) | ~$0-100 (disco externo + tiempo) |

---

## Próximos pasos inmediatos

1. **Abrir Laravel Herd** desde Inicio → Start → minimizar
2. **Reiniciar terminal** (PowerShell / Windsurf)
3. **Verificar** `php -v` → 8.3.x de Herd
4. **Crear proyecto:** `laravel new finca --stack=vue --ssr --pest --git` + migrar Tailwind 4 y Vite 8 (ver sección Creación del proyecto)
5. **Iniciar MinIO** (Docker `docker run` o binario)
6. **Configurar .env** con DB y MinIO
7. **Migrar base:** `php artisan migrate`
8. **Verificar** `php artisan serve` → http://127.0.0.1:8000

Cuando esté listo, arrancamos Fase 1: Auth + Roles + Layout.
