## Folkeep

Folkeep é uma plataforma onde as empresas gerenciam seus funcionários e extraem insights estratégicos de RH — sem a complexidade de um HRIS completo. O objetivo é transformar dados estruturados de pessoas em relatórios acionáveis ​​em uma plataforma que usa uma arquitetura totalmente desacoplada, projetada com padrões de nível empresarial.

### Stack de Tecnologia

**Front-end**

* Next.js — Frontend/SPA

**Back-end**

* Laravel — Servidor de recursos (API RESTful)
* Keycloak — Servidor de autenticação (OIDC/OAuth 2.0)
* PostgreSQL — Banco de Dados Relacional + Histórico (SCD Tipo 2)
* MongoDB – registro de eventos e trilha de auditoria

**Conteinerização**

* Docker — Desacoplamento de serviços

### Arquitetura

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

## Requisitos

- Docker e Docker Compose

## Início Rápido

```bash
# Clone e inicie todos os serviços
git clone <repository-url>
cd folkeep
docker compose up -d

# Acesse a aplicação
# Frontend: http://spa.localhost
# API: http://api.localhost  
# Keycloak: http://keycloak.localhost
```

### Recursos (MVP)

* Autenticação OIDC/OAuth 2.0 (Keycloak)
* Arquitetura híbrida de banco de dados multilocatário (banco de dados compartilhado e banco de dados por locatário)
* Acompanhamento histórico (SCD tipo 2)
* Relatórios de número de funcionários
* Relatórios de rotatividade
* Análise de distribuição salarial
