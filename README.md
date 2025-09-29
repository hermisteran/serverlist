
# Gestión de servidores – Fullstack Challenge (PHP + Vue)

## Proyecto desplegado

| Parte | URL |
|------|------|
| **Backend (Render)** | [https://serverlist-26nh.onrender.com/](https://serverlist-26nh.onrender.com/) |
| **Frontend (Render)** | [https://serverlist-1.onrender.com/](https://serverlist-1.onrender.com/) |

> En producción se usa PostgreSQL (Render), la opción para MySql no disponible. El docker esta configurado para levnatar el servicio de BD en MySql, se puede ajustar.

---
## Instalación local con Docker

1. Clonar el repositorio:
```bash
git clone https://github.com/tu-usuario/serverlist.git
cd serverlist
```

2. Levantar contenedores:
```bash
docker compose up --build -d
```

3. Ejecutar migraciones y seeders:
```bash
docker exec -it laravel-backend php artisan migrate --seed
```

4. Acceder:
- **Backend:** [http://localhost:8000/api/servers](http://localhost:8000/api/servers)
- **Documentación de API (Scribe):** [http://localhost:8080/docs](http://localhost:8080/docs)
- **Frontend:** [http://localhost:5173](http://localhost:5173)

---

## Estructura del Proyecto

```
.
├── backend/           # Proyecto Laravel 12 (API REST)
│   ├── Dockerfile
│   └── ...
├── frontend/          # Proyecto Vue 3 (SPA)
│   ├── Dockerfile
│   └── ...
├── docker-compose.yml # Orquesta backend, frontend y MySQL
└── README.md
```

---

## Tecnologías utilizadas

- **Backend:** Laravel 12, PHP 8.2, MySQL (local), PostgreSQL (Render), Scribe para documentación.
- **Frontend:** Vue 3 + Vite, Bootstrap 5, Pinia (state management), Axios para consumo de API, vuedraggable para drag & drop.
- **Infraestructura:** Docker + Docker Compose para entorno de desarrollo.
- **CI/CD:** GitHub Actions para:
  - Ejecutar tests de backend (PHPUnit).
  - Ejecutar `laravel pint` para formatear código en cada push sobre `main`.

---



## Documentación de API

Se usa **Scribe** para documentar los endpoints automáticamente.  
Una vez levantado el proyecto, visita:

 **[http://localhost:8080/docs](http://localhost:8080/docs)**

### Endpoints disponibles

| Método | Endpoint | Descripción |
|-------|-----------|-------------|
| GET   | `/api/servers` | Listar servidores |
| POST  | `/api/servers` | Crear un nuevo servidor |
| GET   | `/api/servers/{id}` | Mostrar un servidor |
| PUT   | `/api/servers/{id}` | Actualizar un servidor |
| DELETE| `/api/servers/{id}` | Eliminar un servidor (soft delete) |
| PATCH | `/api/update-order-server` | Actualizar orden en lote (drag & drop) |

Todas las respuestas devuelven **JSON** con el código HTTP correspondiente.

---

## Frontend

SPA construida con **Vue 3** + **Vite**:

- **Bootstrap 5** para UI responsiva.
- **Pinia** para manejo de estado global.
- **Axios** para consumo de endpoints.
- **vuedraggable** para drag & drop con persistencia en la BD.

```bash
npm install
npm run dev
```

Disponible en: [http://localhost:5173](http://localhost:5173)

---

## Testing

Laravel ya incluye PHPUnit. Para correr los tests en backend:

```bash
docker exec -it laravel-backend php artisan test
```

Además, está configurado **GitHub Actions** para correr estos tests automáticamente en cada push.

---

## CI/CD (GitHub Actions)

- **Test Workflow:** ejecuta `php artisan test` en cada push/PR sobre `main`.
- **Laravel Pint Workflow:** ejecuta formateo automático de código antes de mergear.

---

## Docker Compose

El proyecto incluye `docker-compose.yml` que levanta:

- **Backend (Laravel 12)** → en `http://localhost:8000`
- **Frontend (Vue 3)** → en `http://localhost:5173`
- **Base de datos MySQL 8** → puerto `3306`

### Variables de entorno importantes:

```yaml
APP_ENV: local
DB_CONNECTION: mysql
DB_HOST: db
DB_PORT: 3306
DB_DATABASE: serverlist
DB_USERNAME: root
DB_PASSWORD: root
```

---

## Conclusión

Este proyecto cumple con:

✅ CRUD completo de servidores con eliminación lógica.  
✅ Persistencia en base de datos (Opción B).  
✅ Drag & drop para reordenar servidores y guardar orden en la BD.  
✅ Documentación de API con Scribe.  
✅ Deploy en Render (Backend + Frontend).  
✅ Docker para entorno local.  
✅ GitHub Actions para testing y formateo de código.

---



