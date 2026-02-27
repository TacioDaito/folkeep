# Folkeep Resource API

>[Leia em português pt-BR](https://github.com/TacioDaito/folkeep-api/blob/main/README.pt-BR.md)

Laravel-based REST API acting as an OAuth 2.0 Resource Server. Responsible for JWT validation, multitenant data isolation, historical employee modeling (SCD Type 2), and analytics-ready reporting. Implements stateless authentication, schema-based tenancy, event logging, and versioned endpoints.

## System Overview

Folkeep is a multitenant system where companies manage their employees and extract strategic HR insights — without the complexity of a full HRIS. The goal is to turn structured people data into actionable reports in a system that uses a fully decoupled architecture, designed with enterprise-grade patterns.

### Tech Stack

**Frontend**

* React + TypeScript — Frontend / SPA

**Backend**

* Laravel 12+ — Resource Server / REST API
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

### Features (MVP)

* OAuth 2.0 authentication
* Multitenant employee management
* Historical tracking (SCD)
* Headcount reports
* Turnover reports
* Salary distribution analytics

---

## Structure

```
api/   # Laravel REST API (Resource Server)
├── 
└── 
```

## Setup
---

