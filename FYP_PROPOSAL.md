# Final Year Project (FYP) Proposal

## StoreBook — Web-Based Inventory, Sales, and Accounting Management System

| Field | Details |
|--------|---------|
| **Project title** | StoreBook: Integrated Web Application for Retail Inventory, Sales, and Financial Operations |
| **Proposed platform** | Multi-business web application (browser-based) |
| **Primary stack** | PHP 8.2+, Laravel 12, MySQL/SQLite (as configured), Vite, Tailwind CSS, Alpine.js |

*Institution, student name, registration number, supervisor, and academic session should be inserted by the submitting student per departmental format.*

---

## 1. Introduction and Background

Small and medium retailers and trading businesses often manage stock, customer and supplier balances, daily sales and purchases, and basic accounting using spreadsheets or disconnected tools. This leads to inconsistent stock figures, delayed reporting, weak audit trails, and difficulty supporting multiple branches or users with different responsibilities.

**StoreBook** is a full-stack web application that centralizes **inventory**, **sales and purchasing**, **party (customer/supplier) and bank ledgers**, and **core financial statements** in one system. It is designed for **multi-business** use: authorized users can work within an active business context, with access controlled by **roles, permissions, and feature modules**.

This proposal presents the project as a suitable Final Year Project in software engineering, information systems, or computer science—demonstrating requirements analysis, secure web development, database design, and domain modeling for real business processes.

---

## 2. Problem Statement

Organizations need a single, reliable system that can:

- Maintain accurate **item master data**, **stock levels**, and **batch-oriented** inventory history where applicable.
- Record **purchases** and **sales** in a way that updates inventory and supports **posting**, **cancellation**, and **audit logs**.
- Handle **returns** (sale and purchase) and **stock adjustments** without corrupting historical records.
- Track **receivables and payables** through **party** accounts and transfers, and **cash/bank** movements through **bank** accounts and transfers.
- Produce **financial reports** (e.g. trial balance, general ledger, profit and loss, balance sheet) aligned with a **chart of accounts**.
- Support **operational controls**: user management, optional **approval workflows**, **activity logging**, and **document attachments** for vouchers and expenses.

Without such integration, businesses face operational risk, reporting delays, and poor scalability as transaction volume grows.

---

## 3. Aims and Objectives

### 3.1 Overall aim

To design, implement, and evaluate **StoreBook**, a secure, multi-user web system that integrates inventory management with sales, purchasing, and accounting workflows for small to medium trading entities.

### 3.2 Specific objectives

1. **Requirements and domain modeling** — Document functional requirements for items, purchases, sales, returns, stock adjustments, parties, banks, expenses, income, and financial reporting; produce use cases and an entity-relationship view consistent with the implementation.
2. **System architecture** — Apply a layered architecture (presentation, application/controllers, domain/models, persistence) using Laravel conventions; separate concerns for authentication, authorization, and business rules.
3. **Security and access control** — Implement authentication (including email verification patterns as provided by the stack), role-based permissions (Spatie Laravel Permission), and module-aware route protection for sensitive operations.
4. **Core business modules** — Deliver working modules for: master data (items, item types), purchases and sales (including dashboards and profit views where applicable), quotations and conversion to sales, returns, stock ledger and valuation reports, stock adjustments, party and bank management with ledger/balance reports, expenses and other income, general vouchers, and finance summaries/exports (e.g. PDF where implemented).
5. **Data integrity and traceability** — Use transactions, validations, posting/cancel patterns, and activity or audit features so that critical operations remain explainable.
6. **Evaluation** — Test the system with realistic scenarios (functional testing, user acceptance with a small business or simulated dataset), and reflect on performance, usability, and limitations.

---

## 4. Scope of the Project

### 4.1 In scope

- Web UI for the modules listed above, scoped by business and permissions.
- Server-side validation and authorization for CRUD and workflow actions (post, cancel, approve, etc.).
- Reporting and dashboards already present in the codebase (sales, purchases, inventory valuation, party/bank ledgers, finance reports).
- Administrative features: businesses, packages, modules, countries/cities/currencies/timezones as used by the application.
- API-style endpoints used internally (e.g. searchable dropdowns for items and parties).

### 4.2 Out of scope (unless explicitly extended by the student)

- Native mobile applications (iOS/Android).
- Full payroll or manufacturing/MRP.
- Third-party payment gateways or e-commerce storefronts (unless added as an extension).
- Features explicitly disabled or commented as non-goals in the current codebase (e.g. legacy specialized inventory tracks not used in the “items-only” configuration).

The student may define a **narrow extension** (e.g. barcode scanning, SMS alerts, or a simplified mobile-friendly POS view) as an optional enhancement, subject to supervisor approval and time constraints.

---

## 5. Proposed Methodology

1. **Literature and standards review** — Review inventory valuation basics (e.g. moving average/FIFO as implemented), double-entry accounting concepts, and OWASP-aligned web security for Laravel applications.
2. **Requirements gathering** — Interview a target business or use a case study; prioritize must-have workflows (purchase → stock → sale → return → reports).
3. **Design** — Data model refinement, wireframes for key screens, permission matrix (role vs. module vs. action).
4. **Implementation** — Iterative development on the existing Laravel codebase: fix defects, add tests where required by the department, and document configuration (`.env`, migrations, seeders).
5. **Testing** — Unit/feature tests (Pest/Laravel testing tools available in the project), manual test scripts for posting/cancellation edge cases.
6. **Deployment (optional)** — Deploy to a staging server (e.g. shared hosting or VPS) with HTTPS and backup strategy.
7. **Documentation** — User manual, installation guide, and FYP report chapters mapping objectives to deliverables.

---

## 6. System Overview (Functional Modules)

The following reflects the **intended capabilities** of the StoreBook system as implemented in the project:

| Area | Capabilities |
|------|----------------|
| **Platform** | Multi-business context; module and permission checks on routes; activity logs; settings. |
| **Identity & access** | Users, roles, permissions, sub-users; profile; business activation/switching. |
| **Master data** | Countries, cities, currencies, timezones; packages and modules; item types; general items with opening stock. |
| **Inventory** | Item CRUD; stock ledger; inventory valuation summary and per-item detailed valuation; batches and inventory transactions (views/updates as routed); stock adjustments. |
| **Purchasing** | Purchase documents with post/cancel; stock impact visibility; audit log. |
| **Sales** | Sales dashboard; sale invoices with post/cancel, audit log, restore/force-delete where applicable; profit/loss report for sales; quotations (convert to sale, reject, expire). |
| **Returns** | Sale returns and purchase returns with post/cancel and audit patterns. |
| **Approvals** | Approval listing, processing, and report views (module/permission gated). |
| **Parties** | Party CRUD; dashboard; ledger and balances reports; party transfers with attachments. |
| **Banking** | Banks; bank transfers; dashboards and ledger/balance reports; attachment handling. |
| **Cash & journals** | General vouchers with attachments; journal entries index (as exposed by routes). |
| **Expenses & income** | Expense heads, income heads, expenses (dashboard/report), other income with attachments. |
| **Finance** | Finance index; chart of accounts (active/inactive); trial balance; general ledger and detailed general ledger; profit and loss; balance sheet; PDF exports where implemented. |
| **UX / front-end** | Blade templates with Vite; Tailwind CSS; Alpine.js; Tom Select for enhanced selects; Axios for HTTP. |

---

## 7. Technology Stack

| Layer | Technology |
|--------|------------|
| **Language / runtime** | PHP 8.2+ |
| **Framework** | Laravel 12 |
| **Auth scaffolding** | Laravel Breeze (dev dependency in project) |
| **Authorization** | Spatie Laravel Permission |
| **Database** | Configurable (e.g. MySQL in production; SQLite supported for local development per Laravel conventions) |
| **Front-end build** | Vite 6 |
| **CSS** | Tailwind CSS 3.x with `@tailwindcss/vite` and forms plugin |
| **JavaScript** | Alpine.js; Axios; Tom Select |
| **Testing (available)** | Pest PHP |

---

## 8. Expected Outcomes and Deliverables

1. **Working system** — Deployable StoreBook instance with seeded demo data (optional) and documented setup steps.
2. **FYP report** — Problem, literature, analysis, design, implementation, testing, results, conclusion, and future work.
3. **Project documentation** — ERD, major sequence diagrams for post/cancel flows, permission matrix, and user guide.
4. **Evidence of testing** — Test cases, sample outputs (reports, PDFs), and screenshots of critical modules.
5. **Reflection** — Discussion of ethical use (financial data), backup, and data privacy considerations for a real deployment.

---

## 9. Innovation and Learning Value

- Integrates **operational** (inventory/sales) and **financial** (GL, TB, P&L, balance sheet) views—closer to real SME software than a toy CRUD app.
- Exposes the student to **enterprise patterns**: multi-tenancy by business, granular permissions, auditability, and report generation.
- Provides a strong basis for **portfolio** and **industry** discussion in viva voce.

---

## 10. Risks and Mitigations

| Risk | Mitigation |
|------|------------|
| Scope creep | Lock “core” modules early; treat enhancements as optional backlog. |
| Domain complexity | Use supervisor review of accounting assumptions; document design decisions. |
| Data corruption in posting logic | Rely on DB transactions, reproduce bugs with tests, keep audit logs. |
| Deployment issues | Containerize or document PHP extensions and `composer`/`npm` build steps clearly. |

---

## 11. Indicative Timeline (adjust to institutional calendar)

| Phase | Duration (indicative) | Activities |
|--------|------------------------|------------|
| **Phase 1** | Weeks 1–3 | Finalize requirements, study codebase, set up environment, baseline demo run. |
| **Phase 2** | Weeks 4–8 | Deep dive on chosen modules; testing; bug fixes; ERD and design docs. |
| **Phase 3** | Weeks 9–12 | Reports, exports, UAT, performance checks; draft thesis. |
| **Phase 4** | Weeks 13–14 | Final report, presentation, viva preparation. |

---

## 12. Conclusion

StoreBook is a **practical, industry-relevant** Final Year Project that combines **web engineering**, **database design**, and **business information systems**. It addresses real pain points for trading businesses while remaining feasible to document, test, and demonstrate within a standard FYP timeline.

---

## Appendix A — Repository and Running the Project (for the student)

Typical local steps (verify against current `README` and departmental instructions):

1. Clone the repository and copy `.env.example` to `.env`; configure database and `APP_URL`.
2. Run `composer install` and `php artisan key:generate`.
3. Run `php artisan migrate` (and seeders if provided).
4. Run `npm install` and `npm run dev` (or `npm run build` for production assets).
5. Run `php artisan serve` and access the application in the browser.

---

*End of proposal document.*
