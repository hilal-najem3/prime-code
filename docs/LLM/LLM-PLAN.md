# LLM-PLAN.md

# AI Planning Prompt — Modular Multi-Tenant SaaS Platform

Using the architecture specification provided in:

- Architecture.md
- ORACLE.md
- LLM-START.md

I want to plan a new feature for this project.

IMPORTANT:

Do NOT generate implementation code.

Do NOT generate:

- controllers
- migrations
- services
- components
- Vue pages
- composables
- routes
- requests
- resources
- actual files

This phase is ONLY for:

- architecture planning
- feature analysis
- system design
- scalability analysis
- API structure planning
- frontend structure planning
- security considerations
- implementation strategy

You must think like a senior SaaS architect.

---

# Project Rules

This project uses:

- Laravel 12
- Vue 3
- TypeScript
- TailwindCSS
- Modular Laravel architecture
- Monorepo frontend architecture
- Database-per-tenant multi-tenancy
- Sanctum authentication
- Event-driven backend architecture

The project already contains:

- centralized ApiResponse system
- modular migration scanning
- tenant database switching
- reusable DataTable system
- centralized HTTP client
- permission generation system
- media upload system
- translation-aware frontend components

Always reuse existing systems before introducing new ones.

---

# Architecture Constraints

You MUST respect:

- modular architecture
- tenant isolation
- service-layer architecture
- reusable frontend systems
- package-based frontend architecture
- centralized API formatting
- TypeScript usage
- composable-based frontend logic
- event-driven backend patterns

Avoid:

- tightly coupled modules
- duplicate abstractions
- business logic inside UI
- direct Axios usage inside components
- fat controllers
- conflicting architectural patterns

---

# Required Output Structure

Please structure the planning response using the following sections.

---

# 1. Feature Overview

Explain:

- what the feature is
- its business purpose
- how it fits into the platform
- whether it is platform-level or tenant-level

---

# 2. Architectural Placement

Explain:

- which module(s) should contain the feature
- whether a new module is needed
- how it integrates with existing systems
- which existing abstractions should be reused

Examples:

- Media system
- DataTable
- Permission system
- HTTP client
- Translation system
- Event system

---

# 3. Database Design Analysis

Explain:

- required tables
- table responsibilities
- tenant vs platform database placement
- important relationships
- indexing considerations
- scalability considerations
- multilingual considerations
- soft delete considerations

DO NOT generate migrations.

---

# 4. API Architecture Planning

Explain:

- API endpoints
- endpoint responsibilities
- API resource structure
- pagination expectations
- filtering/sorting/search expectations
- upload flows if applicable
- validation considerations
- authorization considerations

DO NOT generate controllers or requests.

---

# 5. Frontend Architecture Planning

Explain:

- frontend application placement
- reusable components needed
- composables needed
- shared package usage
- page structure
- modal structure
- table/form architecture
- state management approach
- API service structure

DO NOT generate Vue code.

---

# 6. Permissions & Authorization

Explain:

- required permissions
- policy considerations
- role considerations
- UI visibility rules
- middleware expectations

Examples:

- articles.create
- media.upload
- appointments.update

---

# 7. Event-Driven Considerations

Explain:

- important domain events
- listeners
- side effects
- queue opportunities
- async processing opportunities

Examples:

- MediaUploaded
- InvoiceCreated
- AppointmentScheduled

---

# 8. Media & File Handling Considerations

If the feature involves media:

Explain:

- upload architecture
- public/private access
- media variants
- signed URL considerations
- optimization opportunities

---

# 9. Scalability Considerations

Explain:

- future scaling concerns
- caching opportunities
- indexing strategy
- queue opportunities
- API performance concerns
- frontend performance concerns

---

# 10. Security Considerations

Explain:

- authorization concerns
- tenant isolation risks
- validation concerns
- upload security concerns
- sensitive data concerns
- signed URL concerns
- permission escalation risks

---

# 11. UX / UI Considerations

Explain:

- usability considerations
- responsive behavior
- accessibility concerns
- admin workflow optimization
- multilingual UX considerations
- RTL/LTR concerns

---

# 12. Suggested Folder/File Structure

Suggest:

- module placement
- frontend placement
- composables location
- package usage
- services structure
- DTO placement if needed

DO NOT generate actual files.

---

# 13. Recommended Implementation Phases

Break implementation into phases.

Examples:

```txt
Phase 1 — Database foundation
Phase 2 — Backend services
Phase 3 — API layer
Phase 4 — Frontend UI
Phase 5 — Permissions
Phase 6 — Testing
```

Prioritize:

- safe incremental implementation
- minimal architectural disruption
- reusable abstractions

---

# 14. Risks & Edge Cases

Explain:

- architectural risks
- scaling risks
- UX edge cases
- tenant-related risks
- permission-related risks
- race conditions
- synchronization issues
- media handling edge cases

---

# 15. Final Architectural Recommendation

Provide:

- best recommended architecture
- tradeoffs
- alternatives if applicable
- why this approach best fits the platform

---

# Important Final Rules

You MUST:

- prioritize scalability
- prioritize maintainability
- prioritize modularity
- prioritize reusable abstractions
- think long-term
- preserve architectural consistency

You MUST NOT:

- simplify architecture incorrectly
- introduce conflicting patterns
- bypass existing systems
- tightly couple modules
- generate implementation code during planning

---

# Feature To Plan

[FEATURE DESCRIPTION]
