## Folkeep

Folkeep é uma plataforma onde as empresas gerenciam seus funcionários e extraem insights estratégicos de RH — sem a complexidade de um HRIS completo. O objetivo é transformar dados estruturados de pessoas em relatórios acionáveis ​​em uma plataforma que usa uma arquitetura totalmente desacoplada, projetada com padrões de nível empresarial.

### Stack de Tecnologia

**Front-end**

* Next.js — Frontend/SPA

**Back-end**

* Laravel — Servidor de recursos (API RESTful) / Servidor de autenticação (Laravel Passport, PKCE, OIDC)
* PostgreSQL — Banco de Dados Relacional + Histórico (SCD Tipo 2)
* MongoDB – registro de eventos e trilha de auditoria

**Conteinerização**

* Docker — Desacoplamento de serviços

### Arquitetura

``` 
                        ┌──────┐
                        │ User │
                        └──────┘
                           ▲ 
                           │
                           ▼ 
               ┌──────────────────────┐
               │ Container A          │
               │ REACT SPA ─ Frontend |
               └──────────────────────┘
                           ▲ 
                           │
                           ▼ 
           ┌────────────────────────────────┐
           │ Container B                    │
           │ LARAVEL ─ Auth/Resource Server │
           └────────────────────────────────┘
                           ▲
              ┌────────────┴────────────┐
              ▼                         ▼
  ┌──────────────────────┐  ┌──────────────────────┐
  │ Container C          │  │ Container D          │
  │ PostgreSQL ─ Main DB │  │ MongoDB ─ Logging DB │
  └──────────────────────┘  └──────────────────────┘
```

## Requisitos

- Docker e Docker Compose

### Recursos (MVP)

* Autenticação OAuth 2.0
* Arquitetura híbrida de banco de dados multilocatário (banco de dados compartilhado e banco de dados por locatário)
* Acompanhamento histórico (SCD tipo 2)
* Relatórios de número de funcionários
* Relatórios de rotatividade
* Análise de distribuição salarial
