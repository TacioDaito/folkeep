🤖 Folkeep System Instructions

🏗️ Core Architectural Philosophy
Decoupled First: Always maintain strict separation between the Next.js SPA (Frontend) and the Laravel Resource Server (API), with Nginx Reverse Proxy to mediate everything.

Polyglot Persistence:

Use PostgreSQL for relational data and historical tracking (SCD Type 2).

Use MongoDB exclusively for event logging and audit trails.

Principles: All code must strictly adhere to SOLID, DRY, and KISS principles. Favor readability and maintainability over clever "one-liners."

Easy setup: Everything must work with just docker compose up -d.

Security: Never hardcode keys, codes, passwords or other sensitive info.

🔧 Technical Constraints
🟢 Backend (Laravel API)
Auth: Validate all requests against the Keycloak OIDC/OAuth 2.0 flow. Do not implement local Laravel authentication.

Database Patterns:

Implement Slowly Changing Dimensions (SCD) Type 2 for employee history to ensure point-in-time reporting accuracy.

Support Hybrid Multitenancy (Shared DB and Database-per-tenant logic).

Structure: Use a Service-Repository pattern to keep Controllers thin and business logic decoupled from Eloquent models.

🔵 Frontend (Next.js)
State Management: Use functional components and hooks. Ensure type safety for all API responses.

API Interaction: All external calls must go through a centralized service layer; no inline fetch or axios calls in components.

🐳 Infrastructure (Docker)
Environment: Always assume a containerized environment.

Network: Respect the decoupled container structure (Containers A through E) as defined in the system architecture.

🚦 Workflow & Definition of Done (DoD)
Plan Before Action: For any new feature (e.g., Turnover Reports or Salary Analytics), provide a technical breakdown of the changes required across the API, DB schema, and UI.

Schema Updates: If modifying PostgreSQL, explicitly define the migration and how it impacts SCD Type 2 versioning.

Logging: Ensure every write operation in PostgreSQL is accompanied by a corresponding audit event in MongoDB.

Refactoring: When refactoring, prioritize simplifying the logic (KISS) and removing duplication (DRY).

🃏 Wildcard Rule: The "Senior Architect" Review
Before finalizing any task, the agent must perform a "self-critique" to identify potential bottlenecks in the Hybrid Multitenant architecture or security flaws in the OAuth 2.0 implementation.