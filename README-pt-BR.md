# Folkeep

[Read in English](https://github.com/TacioDaito/folkeep/blob/main/README.md)

> Plataforma de gestão de RH para empresas gerenciarem funcionários e extrair insights estratégicos sem a complexidade de um HRIS completo.

---

## Conteúdo

- [Stack](#stack)
- [Estrutura](#estrutura)
- [Pré-requisitos](#pré-requisitos)
- [Inicialização](#inicialização)
- [Testes](#testes)
- [Arquitetura](#arquitetura)
- [Recursos](#recursos-mvp)

---

### Stack

| Camada | Tecnologia | Versão |
|---|---|---|
| Linguagem | PHP | 8.2+ |
| Runtime | Node.js | 20 |
| Framework Backend | Laravel | 13 |
| Framework Frontend | Next.js | 16.1.6 |
| Banco de Dados | PostgreSQL | 18.2 |
| Document Store | MongoDB | 8.2 |
| Servidor de Autenticação | Keycloak | 26.1.2 |
| Testes | Pest | 4.0 |
| Conteinerização | Docker | - |

---

### Estrutura

```
folkeep/
├── api/                    # API REST Laravel
│   ├── app/               # Código da aplicação (Controllers, Services, Models)
│   ├── config/            # Arquivos de configuração
│   ├── database/          # Migrations, factories, seeders
│   ├── routes/            # Rotas da API
│   └── tests/             # Testes Pest/PHPUnit
├── spa/                    # Frontend SPA Next.js
│   ├── src/               # Código fonte (componentes, páginas, tipos)
│   └── public/            # Assets estáticos
├── keycloak/               # Configuração do servidor de autenticação Keycloak
├── postgres/               # Scripts de inicialização do PostgreSQL
├── proxy/                  # Configuração do proxy reverso NGINX
└── docker-compose.yml      # Orquestração Docker
```

---

### Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/) e [Docker Compose](https://docs.docker.com/compose/install/)

---

### Inicialização

1. Clone o repositório:

```bash
git clone <repository-url>
cd folkeep
```

2. Copie os arquivos de ambiente e preencha os valores necessários:

```bash
cp api/.env.example api/.env
cp spa/.env.local.example spa/.env.local
cp postgres/.env.example postgres/.env
cp keycloak/.env.example keycloak/.env
```

3. Inicie todos os serviços:

```bash
docker compose up -d
```

4. Acesse a aplicação:

- Frontend: http://spa.localhost
- API: http://api.localhost
- Keycloak: http://keycloak.localhost

---

### Testes

```bash
# Execute os testes da API
cd api && php artisan test

# Ou usando Pest diretamente
cd api && ./vendor/bin/pest
```

---

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

---

### Recursos (MVP)

* Autenticação OIDC/OAuth 2.0 (Keycloak)
* Arquitetura híbrida de banco de dados multilocatário (banco de dados compartilhado e banco de dados por locatário)
* Acompanhamento histórico (SCD tipo 2)
* Relatórios de número de funcionários
* Relatórios de rotatividade
* Análise de distribuição salarial