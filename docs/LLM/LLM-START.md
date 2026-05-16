# LLM-START.md

# AI Architecture Context — Modular Multi-Tenant SaaS Platform

You are working on a modular enterprise-oriented multi-tenant SaaS platform.

Before generating any architecture, planning, or implementation:

- understand the architecture
- respect the project structure
- reuse existing systems
- avoid conflicting patterns
- prioritize scalability and maintainability

This is NOT a simple CRUD project.

This is a long-term scalable SaaS architecture.

---

# Core Stack

Backend:

- Laravel 12
- Modular Laravel architecture
- Sanctum authentication
- API-first backend
- Event-driven architecture
- Database-per-tenant multi-tenancy

Frontend:

- Vue 3
- TypeScript
- TailwindCSS
- Monorepo frontend architecture
- Shared package-based frontend system

---

# High-Level Architecture

The platform contains five major layers:

```txt
Laravel Backend API
Platform Admin Frontend
Tenant Admin Frontend
CMS Modules
Public Website Themes
```

---

# Repository Structure

```txt
project-root/

backend/
frontend/
docs/
scripts/
```

---

# Backend Structure

Laravel backend exists inside:

```txt
backend/
```

Structure:

```txt
backend/

app/
bootstrap/
config/
database/
routes/
Modules/
resources/themes/
public/
storage/
artisan
composer.json
```

---

# Frontend Structure

Frontend uses a monorepo architecture.

```txt
frontend/

apps/
packages/
```

---

# Frontend Applications

```txt
frontend/apps/

platform-admin/
tenant-admin/
```

---

# Shared Frontend Packages

```txt
frontend/packages/

core/
ui/
auth/
media/
tenant/
config/
```

---

# Multi-Tenant Architecture

The platform uses a database-per-tenant architecture.

Each tenant has:

- its own database
- its own users
- its own permissions
- its own business data
- its own modules
- its own theme
- its own domain

---

# Database Layers

## Platform Database

Stores:

- tenants
- domains
- subscriptions
- plans
- platform users
- modules
- permissions
- tenant metadata

Platform migrations exist inside:

```txt
backend/database/migrations
```

---

## Tenant Databases

Store:

- users
- roles
- permissions
- patients
- invoices
- products
- appointments
- media
- articles
- pages
- comments
- tenant business data

Tenant migrations exist inside:

```txt
Modules/*/Database/Migrations
```

---

# Important Migration Rules

Platform migrations must NEVER contain tenant business tables.

Tenant migrations must NEVER contain platform tables.

---

# Modular Backend Architecture

All backend business logic must be modular.

Modules exist inside:

```txt
backend/Modules/
```

Examples:

```txt
Modules/

Auth/
Permissions/
Tenants/
Blog/
Media/
Patients/
Appointments/
Products/
Orders/
Services/
Customers/
Projects/
```

---

# Module Structure

Each module should ideally contain:

```txt
Controllers/
Services/
Requests/
Resources/
Models/
Policies/
Routes/
Database/
Events/
Listeners/
DTOs/
Seeders/
Providers/
```

---

# Backend Architecture Rules

## Controllers

Controllers must remain thin.

Controllers should:

- receive requests
- delegate logic to services
- return API responses

Controllers should NOT:

- contain business logic
- contain heavy validation
- contain formatting logic

---

## Services

Business logic belongs inside Services.

Examples:

```txt
PatientService
InvoiceService
MediaService
ProductService
```

Services handle:

- transactions
- workflows
- orchestration
- domain operations

---

## Requests

Validation belongs in Form Requests.

Examples:

```txt
CreatePatientRequest
StoreMediaRequest
UpdateInvoiceRequest
```

---

## Resources

API formatting belongs in Resources.

Examples:

```txt
PatientResource
MediaResource
InvoiceResource
```

Resources provide:

- consistent API formatting
- frontend stability
- reusable output contracts

---

# API Response Standard

All responses must use centralized API formatting.

Example:

```json
{
  "success": true,
  "message": "Success",
  "data": [],
  "meta": {},
  "errors": null
}
```

Pagination metadata should be automatically included.

---

# Authentication System

Authentication uses Laravel Sanctum.

Two authentication systems exist.

---

## Admin Authentication

Used for:

- platform admin
- tenant admin

Uses:

- access tokens
- refresh tokens

---

## Website Authentication

Used for:

- comments
- ratings
- subscriptions
- customer accounts

Uses:

- Laravel sessions

---

# Authorization System

Authorization uses:

- Policies
- Permissions
- Middleware

---

# Permission Architecture

Permissions are platform-level.

Roles are tenant-level.

No cross-database foreign keys are used.

Relationships are enforced at the application level.

---

# Middleware Examples

```php
middleware('access:auto')
middleware('access:permission,articles.create')
middleware('access:role,admin')
```

---

# Frontend Architecture

Frontend uses:

- Vue 3
- TypeScript
- TailwindCSS

---

# Frontend Rules

## Components

Components should remain:

- reusable
- presentation-focused
- modular

Avoid heavy business logic inside components.

---

## Composables

Reusable frontend state logic belongs in composables.

Examples:

```txt
useAuth()
usePermissions()
useTenant()
useDataTable()
```

---

## API Services

Frontend communicates ONLY through centralized API services.

Do NOT call Axios directly inside components.

Examples:

```txt
packages/core/api/
```

---

# Shared UI System

Reusable UI belongs inside:

```txt
packages/ui
```

Examples:

```txt
Button
Modal
DataTable
Pagination
Badge
Loader
ConfirmDialog
TextInput
SelectInput
```

---

# DataTable System

The DataTable component supports:

- local mode
- remote mode
- pagination
- searching
- sorting
- bulk actions
- expandable rows
- reusable slots

It is designed as a reusable SaaS UI engine component.

---

# Media System

Media system is centralized.

Supports:

- public media
- private media
- signed URLs
- media variants
- image conversions

Frontend consumes MediaResource responses.

---

# Localization System

The system supports:

- English (LTR)
- Arabic (RTL)

Translations exist inside:

```txt
i18n/en
i18n/ar
```

Content translations use JSON-based multilingual fields.

---

# Event-Driven Architecture

The backend favors event-driven design.

Services dispatch domain events.

Listeners handle side effects.

Examples:

```txt
InvoiceCreated
InvoiceItemUpdated
PatientRegistered
MediaUploaded
```

Listeners may handle:

- inventory updates
- notifications
- logs
- analytics
- accounting
- reporting

---

# Queues

Heavy operations should use queues.

Examples:

- notifications
- exports
- imports
- reporting
- analytics
- image processing

---

# Theme System

Public websites use Blade themes.

Themes exist inside:

```txt
resources/themes/
```

Examples:

```txt
default/
corporate/
magazine/
minimal/
```

---

# SEO System

The platform supports:

- meta titles
- meta descriptions
- canonical URLs
- OpenGraph
- schema.org JSON
- sitemap.xml
- robots.txt
- hreflang tags

---

# Existing Core Systems

The project already contains:

- centralized ApiResponse system
- modular migration scanning
- tenant database switching
- centralized HTTP client
- reusable DataTable
- permission generation system
- media upload system
- signed private media routes
- translation-aware frontend components
- event-driven backend architecture

Always reuse existing systems before creating new ones.

---

# Development Philosophy

This project prioritizes:

- scalability
- maintainability
- modularity
- reusable abstractions
- tenant isolation
- enterprise-oriented architecture

---

# Important Rules

- Respect existing architecture
- Reuse existing systems before creating new ones
- Avoid tightly coupling modules
- Keep modules isolated
- Keep controllers thin
- Use Services for business logic
- Use Requests for validation
- Use Resources for API formatting
- Use TypeScript everywhere possible
- Keep frontend reusable
- Prefer composition over inheritance
- Think long-term scalability first

---

# Critical Implementation Rules

When implementing features:

- follow existing architecture exactly
- avoid introducing conflicting patterns
- explain major architectural decisions
- avoid generating duplicate abstractions
- prefer reusable systems
- avoid hardcoding business logic inside UI
- keep modules independently maintainable

---

# AI Workflow

Recommended workflow:

## Step 1 — Architecture Context

Read:

- Architecture.md
- ORACLE.md

---

## Step 2 — Planning Phase

Use:

- LLM-PLAN.md

Purpose:

- architecture planning
- database design
- API planning
- scalability analysis
- feature decomposition

---

## Step 3 — Implementation Phase

Use:

- LLM-IMPLEMENT.md

Purpose:

- backend generation
- frontend generation
- services
- requests
- resources
- composables
- API services
- reusable UI components

---

# Final Goal

The final platform should support:

- SaaS websites
- CMS platforms
- blogs
- CRM modules
- ERP-style systems
- multilingual content
- scalable tenant-based architectures

while maintaining:

- clean architecture
- reusable systems
- modular design
- long-term maintainability
