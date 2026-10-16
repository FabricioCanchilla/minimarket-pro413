# Minimarket PRO-413 - Sistema de microservicios para gestión de productos

Sistema compuesto por tres componentes independientes:

| Componente | Tecnología | Puerto |
|---|---|---|
| Frontend | PHP (servidor integrado) | 8000 |
| Backend (API REST) | Java + Quarkus | 8080 |
| Base de datos | MySQL 8.4 en Docker | 3307 (host) -> 3306 (contenedor) |

## Arquitectura
```
Navegador --> Frontend PHP (:8000) --HTTP/JSON--> Backend Quarkus (:8080) --JDBC--> MySQL en Docker (:3307)
```

El frontend nunca se conecta a MySQL: toda operación pasa por la API REST.

## Estructura del repositorio

```
minimarket-pro413/
├── docker-compose.yml     # Contenedor MySQL (credenciales, volumen, script inicial)
├── .env.example           # Variables de entorno de ejemplo
├── database/init.sql      # Crea la tabla Producto y 2 registros de prueba
├── backend/               # API REST con Quarkus (Maven Wrapper incluido)
└── frontend/              # Aplicación web en PHP
```

## Requisitos

- Docker Desktop (con Docker Compose)
- Java 17 o superior (no hace falta instalar Maven, el proyecto incluye `mvnw`)
- PHP 8.0 o superior
- Puertos libres: 3307, 8080 y 8000

## Variables de entorno

| Variable | Usada por | Valor por defecto |
|---|---|---|
| `MYSQL_ROOT_PASSWORD` | docker-compose | definida en `.env` |
| `MYSQL_USER` | docker-compose | definida en `.env` |
| `MYSQL_PASSWORD` | docker-compose | definida en `.env` |
| `DB_URL` | backend | `jdbc:mysql://localhost:3307/db_minimarket_pro413` |
| `DB_USER` | backend | `minimarket` |
| `DB_PASSWORD` | backend | `minimarket123` |
| `API_URL` | frontend | `http://localhost:8080` |

Los valores por defecto del backend coinciden con los de `.env.example`. Si se cambian en `.env`, definir también `DB_USER` y `DB_PASSWORD` con los mismos valores.

## Puesta en marcha

Ejecutar cada paso en una terminal distinta, en este orden.

### 1. Base de datos (desde la raíz del repositorio)

```
copy .env.example .env
docker compose up -d
```

(En Linux/macOS: `cp .env.example .env`). La primera vez se crea la base `db_minimarket_pro413` con la tabla `Producto`. Los datos persisten en el volumen `mysql_data`.

### 2. Backend

```
cd backend
.\mvnw quarkus:dev
```

(En Linux/macOS: `./mvnw quarkus:dev`). La API queda en http://localhost:8080. Para compilar el proyecto: `.\mvnw package`. Para detenerlo en modo dev, presionar `q`.

### 3. Frontend

```
cd frontend
php -S localhost:8000
```

Abrir http://localhost:8000 en el navegador.

## Endpoints de la API

| Método | Ruta | Descripción | Respuestas |
|---|---|---|---|
| GET | `/productos` | Lista los productos activos (`eliminado = 0`) | 200 |
| GET | `/productos/{id}` | Obtiene un producto activo | 200, 404 |
| POST | `/productos` | Registra un producto | 201, 400 |
| PUT | `/productos/{id}` | Edita un producto | 200, 400, 404 |
| DELETE | `/productos/{id}` | Eliminación lógica (`eliminado = 1`) | 204, 404 |

Los errores de base de datos devuelven 500 con `{"error": "..."}`.

Ejemplo de cuerpo JSON para POST y PUT:

```json
{
  "nombre": "Pan de batalla",
  "proveedor": "Panadería Central",
  "fecha_vencimiento": "2026-11-20",
  "precio": 1.50,
  "stock": 100
}
```

## Eliminación lógica

`DELETE /productos/{id}` no borra la fila: cambia `eliminado` de 0 a 1. El listado solo muestra los registros con `eliminado = 0`. Para verificarlo directamente en MySQL:

```
docker exec -it minimarket-mysql mysql -u root -p db_minimarket_pro413 -e "SELECT * FROM Producto;"
```

## Detener los servicios

docker compose down

Los datos se conservan en el volumen `mysql_data`. El script `database/init.sql` solo se ejecuta la primera vez, cuando el volumen está vacío. Para borrar los datos y reconstruir la base desde cero: `docker compose down -v` y luego `docker compose up -d`.