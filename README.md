# Folkeep

[Leia em pt-BR](https://github.com/TacioDaito/folkeep/blob/main/README-pt-BR.md)

> HR management platform for companies to manage employees and extract strategic insights without the complexity of a full HRIS.

---

## Contents

- [Stack](#stack)
- [Structure](#structure)
- [Prerequisites](#prerequisites)
- [Initialization](#initialization)
- [Testing](#testing)
- [Architecture](#architecture)
- [Features](#features-mvp)

---

### Stack

| Layer | Technology | Version |
|---|---|---|
| Language | PHP | 8.2+ |
| Runtime | Node.js | 20 |
| Backend Framework | Laravel | 13 |
| Frontend Framework | Next.js | 16.1.6 |
| Database | PostgreSQL | 18.2 |
| Document Store | MongoDB | 8.2 |
| Auth Server | Keycloak | 26.1.2 |
| Testing | Pest | 4.0 |
| Containerisation | Docker | - |

---

### Structure

```
folkeep/
├── api/                    # Laravel REST API
│   ├── app/               # Application code (Controllers, Services, Models)
│   ├── config/            # Configuration files
│   ├── database/          # Migrations, factories, seeders
│   ├── routes/            # API routes
│   └── tests/             # Pest/PHPUnit tests
├── spa/                    # Next.js SPA frontend
│   ├── src/               # Source code (components, pages, types)
│   └── public/            # Static assets
├── keycloak/               # Keycloak auth server configuration
├── postgres/               # PostgreSQL initialization scripts
├── proxy/                  # NGINX reverse proxy configuration
└── docker-compose.yml      # Docker orchestration
```

---

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/)

---

### Initialization

1. Clone the repository:

```bash
git clone <repository-url>
cd folkeep
```

2. Copy environment files and fill required values:

```bash
cp api/.env.example api/.env
cp spa/.env.local.example spa/.env.local
cp postgres/.env.example postgres/.env
cp keycloak/.env.example keycloak/.env
```

3. Start all services:

```bash
docker compose up -d
```

4. Access the application:

- Frontend: http://spa.localhost
- API: http://api.localhost
- Keycloak: http://keycloak.localhost

---

### Testing

```bash
# Run API tests
cd api && php artisan test

# Or using Pest directly
cd api && ./vendor/bin/pest
```

---

### Architecture

```
                           ┌───────┐
                           │ User  │
                           └───────┘
                               ▲ 
-------------------------------│---------------------------------
                               ▼                    
                 ┌─────────────────────────┐   Docker Service Network
                 │  Container A            │
                 │  NGINX ─ Reverse Proxy  │
                 └─────────────────────────┘
                  ▲           ▲           ▲
                  │           │           │
                  ▼           ▼           ▼
      ┌─────────────┐ ┌───────────────┐ ┌─────────────┐
      │ Container B │ | Container C   │ │ Container D │
      │ NEXT.JS SPA │ │ KEYCLOAK Auth │ │ LARAVEL API │
      └─────────────┘ └───────────────┘ └─────────────┘
                              ▲                ▲
              ┌───────────────┘                |
              │      ┌─────────────────────────┤
              ▼      ▼                         ▼
      ┌──────────────────────┐  ┌──────────────────────┐
      │ Container E          │  │ Container F          │
      │ PostgreSQL ─ Main DB │  │ MongoDB ─ Logging DB │
      └──────────────────────┘  └──────────────────────┘
```

---

### Features (MVP)

* OIDC/OAuth 2.0 authentication (Keycloak)
* Hybrid multitenant database architecture (shared database and database-per-tenant)
* Historical tracking (SCD type 2)
* Headcount reports
* Turnover reports
* Salary distribution analytics