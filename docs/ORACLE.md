# ORACLE — Project AI Context

This repository contains a modular multi-tenant SaaS platform built using:

- Laravel 12
- Vue 3
- TypeScript
- TailwindCSS
- Sanctum
- Modular backend architecture
- Monorepo frontend architecture

## Important Rules

Read the following documentation before planning or implementing features:

### Core Architecture

/docs/PROJECT-ARCHITECTURE.md

### AI Workflow

/docs/LLM-WORKFLOW.md

### Backend Rules

/docs/architecture/backend.md

### Frontend Rules

/docs/architecture/frontend.md

### Multi-Tenancy Rules

/docs/architecture/multitenancy.md

### Permissions Architecture

/docs/architecture/permissions.md

### Media System

/docs/architecture/media-system.md

---

# AI Workflow

## Step 1 — Architecture Context

Read:

- PROJECT-ARCHITECTURE.md

## Step 2 — Planning Phase

Use:

- /docs/llm/LLM-PLAN.md

## Step 3 — Implementation Phase

Use:

- /docs/llm/LLM-IMPLEMENT.md

---

# Critical Rules

- Controllers must remain thin
- Business logic belongs in Services
- Validation belongs in Requests
- API formatting belongs in Resources
- Frontend logic belongs in composables/services
- Components should remain reusable
- Respect tenant separation
- Reuse existing abstractions
- Avoid tightly coupling modules
- Follow modular architecture

---

# Repository Structure

backend/
frontend/
docs/

frontend/apps
frontend/packages
backend/Modules

---

# Important Notes

This is NOT a CRUD project.

This is an enterprise-oriented SaaS architecture.

All implementations must prioritize:

- scalability
- modularity
- maintainability
- reusable abstractions
- tenant isolation
- clean API contracts
