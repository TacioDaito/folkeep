# Folkeep Resource API

API REST baseada em Laravel atuando como um Servidor de Recursos OAuth 2.0. Responsável pela validação de JWT, isolamento de dados multi-inquilino, modelagem histórica de funcionários (SCD Tipo 2) e relatórios prontos para análise. Implementa autenticação stateless, tenancy por esquema, registro de eventos e endpoints versionados.

## Visão Geral do Sistema

Folkeep é um sistema multi-inquilino onde empresas gerenciam seus funcionários e extraem insights estratégicos de RH — sem a complexidade de um HRIS completo. O objetivo é transformar dados estruturados de pessoas em relatórios acionáveis em um sistema que utiliza uma arquitetura totalmente desacoplada, projetada com padrões de nível empresarial.

### Stack Tecnológica

**Frontend**

* React + TypeScript — Frontend / SPA

**Backend**

* Laravel 12+ — Servidor de Recursos / API REST
* Keycloak — Servidor de Autenticação / OAuth 2.0 / OIDC
* PostgreSQL — Banco Relacional + Histórico (SCD Tipo 2)
* MongoDB — Registro de Eventos e Auditoria

**Containerização**

* Docker — Desacoplamento de Serviços

### Arquitetura

```
                        ┌──────┐
                        │ Usuário │
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
  │ PostgreSQL ─ Banco   │  │ MongoDB ─ Banco de   │
  │          Principal   │  │          Logs        │
  └──────────────────────┘  └──────────────────────┘
```

### Funcionalidades (MVP)

* Autenticação OAuth 2.0
* Gerenciamento multi-inquilino de funcionários
* Rastreamento histórico (SCD)
* Relatórios de headcount
* Relatórios de rotatividade (turnover)
* Análise de distribuição salarial

---

## Estrutura

```
api/   # API REST Laravel (Servidor de Recursos)
├── 
└── 
```

## Configuração
---
