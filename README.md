# Folkeep

[Leia em pt-BR](https://github.com/TacioDaito/folkeep/blob/main/README-pt-BR.md)

Folkeep is a plataform where companies manage their employees and extract strategic HR insights — without the complexity of a full HRIS. The goal is to turn structured people data into actionable reports in a plataform that uses a fully decoupled architecture, designed with enterprise-grade patterns.

## Tech Stack

**Frontend**

* React + TypeScript — Frontend / SPA

**Backend**

* Laravel — Resource Server / REST API
* Keycloak — Auth Server / OAuth 2.0 / OIDC
* PostgreSQL — Relational + Historical Database (SCD Type 2)
* MongoDB — Event Logging & Audit Trail

**Containerization**

* Docker — Service Decoupling

### Architecture

```
                        ┌──────┐
                        │ User │
                        └──────┘
                           ▲ 
                           │
                           ▼ 
┌─────────────────────────────────────────────────────┐
│                 Container A                         │
│                 REACT SPA ─ Frontend                |
└─────────────────────────────────────────────────────┘
       ▲                                     ▲ 
       │                                     │
       ▼                                     ▼ 
┌────────────────────────┐  ┌───────────────────────────────┐
│ Container B            │  │ Container C                   │
│ KEYCLOAK ─ Auth Server |  │ LARAVEL API ─ Resource Server │
└────────────────────────┘  └───────────────────────────────┘
          ▲                               ▲
          |       ┌───────────────────────┤
          ▼       ▼                       ▼
  ┌──────────────────────┐  ┌──────────────────────┐
  │ Container D          │  │ Container E          │
  │ PostgreSQL ─ Main DB │  │ MongoDB ─ Logging DB │
  └──────────────────────┘  └──────────────────────┘
```

## Requirements

- Docker and Docker Compose

## Features (MVP)

* OAuth 2.0 authentication
* Hybrid multitenant database architecture (shared database and database-per-tenant)
* Historical tracking (SCD type 2)
* Headcount reports
* Turnover reports
* Salary distribution analytics
