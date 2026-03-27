# Folkeep

[Leia em pt-BR](https://github.com/TacioDaito/folkeep/blob/main/README-pt-BR.md)

Folkeep is a plataform where companies manage their employees and extract strategic HR insights — without the complexity of a full HRIS. The goal is to turn structured people data into actionable reports in a plataform that uses a fully decoupled architecture, designed with enterprise-grade patterns.

## Tech Stack

**Frontend**

* Next.js — Frontend / SPA

**Backend**

* Laravel — Resource Server (RESTful API)
* Keycloak — Auth Server (OIDC/OAuth 2.0)
* PostgreSQL — Relational + Historical Database (SCD Type 2)
* MongoDB — Event Logging & Audit Trail

**Containerization**

* Docker — Service Decoupling

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

## Requirements

- Docker and Docker Compose

## Quick Start

```bash
# Clone and start all services
git clone <repository-url>
cd folkeep
docker compose up -d

# Access the application
# Frontend: http://spa.localhost
# API: http://api.localhost  
# Keycloak: http://keycloak.localhost
```

## Features (MVP)

* OIDC/OAuth 2.0 authentication (Keycloak)
* Hybrid multitenant database architecture (shared database and database-per-tenant)
* Historical tracking (SCD type 2)
* Headcount reports
* Turnover reports
* Salary distribution analytics
