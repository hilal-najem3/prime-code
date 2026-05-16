# LLM-IMPLEMENT.md

# AI Implementation Prompt — Modular Multi-Tenant SaaS Platform

Using the architecture specification provided in:

- Architecture.md
- ORACLE.md
- LLM-START.md

AND using the approved planning structure from:

- LLM-PLAN.md

Implement the requested feature following the existing architecture exactly.

This project is a scalable modular multi-tenant SaaS platform.

You MUST preserve:

- modularity
- scalability
- maintainability
- tenant isolation
- reusable abstractions
- enterprise-oriented architecture

---

# Core Stack

Backend:

- Laravel 12
- Sanctum authentication
- Modular Laravel architecture
- Event-driven architecture
- API-first backend

Frontend:

- Vue 3
- TypeScript
- TailwindCSS
- Monorepo frontend architecture

---

# Existing Core Systems

The project already contains:

- centralized ApiResponse system
- modular migration scanning
- tenant database switching
- reusable DataTable system
- centralized HTTP client
- permission generation system
- media upload system
- signed private media routes
- translation-aware frontend components
- event-driven backend architecture

Always reuse existing systems before creating new ones.

---

# Critical Architecture Rules

You MUST follow these rules.

---

# Backend Rules

## Controllers

Controllers must remain thin.

Controllers should:

- receive requests
- delegate logic to services
- return Resources / ApiResponse

Controllers should NOT:

- contain business logic
- contain validation logic
- contain formatting logic

---

## Services

Business logic belongs inside Services.

Examples:

```txt
PatientService
InvoiceService
MediaService
AppointmentService
```

Services should handle:

- workflows
- transactions
- orchestration
- domain operations

---

## Requests

Validation belongs in Form Requests.

Examples:

```txt
StorePatientRequest
UpdateInvoiceRequest
UploadMediaRequest
```

---

## Resources

API formatting belongs in Resources.

Examples:

```txt
PatientResource
InvoiceResource
MediaResource
```

All API responses must remain consistent.

---

# API Response Standard

All API responses must use the centralized API response structure.

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

Pagination metadata should be included automatically.

---

# Multi-Tenant Rules

The project uses database-per-tenant architecture.

## Platform Database

Contains:

- tenants
- subscriptions
- plans
- modules
- permissions
- platform users

Platform migrations exist inside:

```txt
backend/database/migrations
```

---

## Tenant Databases

Contain:

- users
- roles
- patients
- invoices
- appointments
- products
- tenant business data

Tenant migrations exist inside:

```txt
Modules/*/Database/Migrations
```

---

# Important Tenant Rules

Platform migrations must NEVER contain tenant business tables.

Tenant migrations must NEVER contain platform tables.

Respect tenant isolation at all times.

Avoid cross-tenant data leakage.

---

# Frontend Rules

Frontend uses:

- Vue 3
- TypeScript
- TailwindCSS

---

# Components

Components should remain:

- reusable
- modular
- presentation-focused

Avoid heavy business logic inside components.

---

# Composables

Reusable state logic belongs inside composables.

Examples:

```txt
useAuth()
usePermissions()
useTenant()
useDataTable()
```

---

# API Services

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

# DataTable Rules

The DataTable system already supports:

- local mode
- remote mode
- pagination
- search
- sorting
- bulk actions
- expandable rows

Reuse the existing DataTable architecture when possible.

Do NOT build feature-specific table systems unnecessarily.

---

# Media System Rules

Media uploads use centralized media handling.

Supports:

- public media
- private media
- signed URLs
- image conversions
- variants

Frontend should consume MediaResource responses.

---

# Localization Rules

Frontend and websites support:

- English (LTR)
- Arabic (RTL)

Translations exist inside:

```txt
i18n/en
i18n/ar
```

Content translations may use JSON multilingual fields.

---

# Event-Driven Architecture Rules

The backend favors event-driven design.

Services should dispatch domain events.

Listeners should handle side effects.

Examples:

```txt
InvoiceCreated
InvoiceItemUpdated
MediaUploaded
PatientRegistered
```

Listeners may handle:

- notifications
- analytics
- inventory
- accounting
- logs
- reporting

---

# Queue Rules

Heavy operations should use queues.

Examples:

- image processing
- exports
- imports
- notifications
- analytics
- reports

---

# Code Quality Rules

You MUST:

- preserve modularity
- preserve scalability
- preserve maintainability
- preserve reusable abstractions
- keep modules isolated
- use meaningful naming
- keep architecture clean

You MUST NOT:

- tightly couple modules
- introduce conflicting patterns
- bypass existing systems
- duplicate abstractions unnecessarily
- place business logic inside components
- create fat controllers

---

# Required Output

Generate the required implementation for the feature.

You MAY generate:

- migrations
- models
- services
- requests
- resources
- policies
- events
- listeners
- routes
- controllers
- Vue pages
- components
- composables
- API services
- DTOs
- enums
- helpers

when appropriate.

---

# Required Implementation Style

Please structure the implementation output clearly.

Recommended sections:

---

# 1. Feature Overview

Short explanation of the implementation approach.

---

# 2. Backend Structure

Explain:

- module placement
- services
- requests
- resources
- policies
- events
- listeners

---

# 3. Database Layer

Generate:

- migrations
- relationships
- indexes
- constraints

while respecting:

- tenant architecture
- platform architecture

---

# 4. API Layer

Generate:

- routes
- controllers
- requests
- resources

while keeping:

- controllers thin
- validation inside Requests
- formatting inside Resources

---

# 5. Service Layer

Generate:

- services
- workflows
- transactions
- orchestration logic

---

# 6. Events & Listeners

Generate:

- domain events
- listeners
- queueable operations

when appropriate.

---

# 7. Frontend Structure

Generate:

- Vue pages
- reusable components
- composables
- API services
- TypeScript types

while preserving:

- reusable architecture
- modular frontend design

---

# 8. Permissions & Authorization

Generate:

- permissions
- policies
- middleware usage
- UI visibility considerations

---

# 9. DataTable Integration

If applicable:

- reuse existing DataTable system
- support remote mode
- support pagination/search/sort
- follow existing API meta structure

---

# 10. Media Integration

If applicable:

- use existing media system
- support MediaResource
- support variants
- support signed URLs

---

# 11. Localization Integration

If applicable:

- support multilingual fields
- support RTL/LTR
- support frontend translations

---

# 12. Important Architectural Notes

Explain:

- major architectural decisions
- reusable abstractions introduced
- scalability decisions
- tradeoffs

---

# Final Rules

You MUST:

- follow existing architecture exactly
- preserve tenant separation
- reuse existing systems
- keep implementation scalable
- keep implementation modular
- keep frontend reusable
- keep backend clean

You MUST think like a senior SaaS architect and enterprise developer.

---

# Feature To Implement

[FEATURE DESCRIPTION]
