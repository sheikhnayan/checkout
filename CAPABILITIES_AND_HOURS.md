# CartVIP — Capabilities, Build Timeline & Itemized Hours

**Prepared:** 2026-06-17
**Project start:** 2025-07-28
**Engagement span:** ~11 months (Jul 2025 → Jun 2026)
**Platform & integrations:** multi-tenant cloud architecture · Stripe & Authorize.Net payments · Telnyx & Aloware SMS · OAuth social login · Google Analytics · automated PDF generation · QR-code check-in
**Size:** 40+ feature modules · 44 data entities · 160+ database schema updates · 22 admin management areas · automated email notifications · multi-template public storefront

> Each feature below is listed with the estimated engineering hours required to build it. Hours are derived from the functionality present in the delivered platform; build dates are taken from the system's internal records. Section 1 describes the method used.

---

## 1. How the Hours and Dates Were Determined

This section describes how the hour figures were produced, how the build timeline was established, and how the work maps to the team that delivered it.

**What one "hour" represents.** Each hour covers the engineering work required to take a feature from concept to a finished, production-ready state — requirements and planning, design, building, testing, revisions, and issue-fixing. Hours measure build-and-delivery effort, not only the time to produce a first draft.

**How the hours were calculated.** The hours were estimated bottom-up. The delivered platform was reviewed feature by feature — every screen, workflow, integration, and data structure present in it — and each feature was assigned an effort figure based on its complexity. Each hour corresponds to a specific, identifiable part of the working product.

**How the timeline was established.** Build dates are taken from the platform's internal records — the timestamps created as each part of the system was added or changed. These provide a chronological record of when each part was built.

**Team & staffing.** Team size varied with the workload across the engagement:

| Period | Months | Developers |
|---|---:|---:|
| Jul–Oct 2025 | 4 | 2 |
| Nov 2025–Feb 2026 | 4 | 1 |
| Mar–Jun 2026 | 4 | 3 |

---

## 2. Executive Summary

| Phase | Period | Module Group | Hours |
|---|---|---|---:|
| P0 | Jul 2025 | Project setup & admin foundation | 80 |
| P1 | Jul–Sep 2025 | Core multi-tenant checkout (CartVIP MVP) | 620 |
| P2 | Oct–Nov 2025 | Branding, customization & reservations | 180 |
| P3 | Nov 2025–Jan 2026 | Custom invoicing | 110 |
| P4 | Jan–Feb 2026 | Shared cart & checkout popups | 70 |
| P5 | Mar 2026 | Affiliate system | 300 |
| P6 | Mar–Apr 2026 | Entertainer system | 165 |
| P7 | Mar–Apr 2026 | Social feed / roll call | 210 |
| P8 | Apr 2026 | Job marketplace | 100 |
| P9 | Apr–May 2026 | RBAC & multi-user admin | 140 |
| P10 | Apr–May 2026 | Incident reporting & witness statements | 130 |
| P11 | Apr–May 2026 | Withdrawals & payouts | 90 |
| P12 | May 2026 | Advanced packages, targeting & analytics | 160 |
| P13 | May–Jun 2026 | Reporting & analytics suite | 140 |
| P14 | Jun 2026 | Compliance & operations (W-9, scanning, SMS) | 240 |
| CC | Ongoing | Cross-cutting (QA, bug-fixing, DevOps) | 200 |
| | | **TOTAL** | **2,935** |

---

## 3. Itemized Feature Inventory & Hours

### P0 — Project Setup & Admin Foundation — *Jul 2025* — **80 hrs**
| Item | Hrs |
|---|---:|
| Project foundation, database schema design, environment & deployment baseline | 35 |
| Admin authentication (login, password reset, profile management) | 25 |
| Admin dashboard layout & navigation structure | 20 |

### P1 — Core Multi-Tenant Checkout (CartVIP MVP) — *Jul–Sep 2025* — **620 hrs**
| Item | Hrs |
|---|---:|
| Multi-tenant venue/website configuration (per-venue settings, links, naming) | 55 |
| Theming & branding foundation (colors, contrast, background) | 30 |
| Package management (pricing, guest counts, multiples, transportation) | 50 |
| Package categories | 18 |
| Add-on management (package add-ons + general add-ons, sync behavior) | 40 |
| Event management (dates, times, booking fee, attendee limits) | 40 |
| Public checkout flow (package selection, dynamic pricing, qty, guest counts) | 85 |
| Cart management (add / remove / update / validate) | 40 |
| Fee calculation engine (service charge, gratuity, sales tax, processing fee) | 55 |
| Promo / discount codes (manual + auto-discounts) | 30 |
| Transaction storage & order lifecycle (status, men/women counts, IP capture) | 50 |
| Payment integration — Stripe + Authorize.Net | 70 |
| Email + SMTP system (per-venue transactional mail) | 35 |
| Thank-you / receipt pages | 22 |

### P2 — Branding, Customization & Reservations — *Oct–Nov 2025* — **180 hrs**
| Item | Hrs |
|---|---:|
| Payment logos management (upload, dimensions) | 30 |
| Extended theming (button text, font/description, logo dimensions, contrast) | 50 |
| Transportation & confirmation-text flows | 30 |
| Reservation / deposit flow | 40 |
| Slug-based public URLs (SEO-friendly venue pages) | 30 |

### P3 — Custom Invoicing — *Nov 2025–Jan 2026* — **110 hrs**
| Item | Hrs |
|---|---:|
| Invoice builder + line items + detailed fee breakdown | 55 |
| Tokenized client-facing payment page (no-auth, secure link) | 35 |
| Send/email, archive, invoice lifecycle | 20 |

### P4 — Shared Cart & Checkout Popups — *Jan–Feb 2026* — **70 hrs**
| Item | Hrs |
|---|---:|
| Shared cart generation & public share links | 40 |
| Checkout popups (promotional / informational) | 30 |

### P5 — Affiliate System — *Mar 2026* — **300 hrs**
| Item | Hrs |
|---|---:|
| Affiliate registration + social / OAuth signup | 55 |
| Customizable affiliate public pages (hero, content, location, badges) | 55 |
| Affiliate admin (approve / reject / commission / packages, rejection tracking) | 55 |
| Affiliate portal (dashboard, packages, settings, wallet) | 65 |
| Affiliate packages + wallet transactions + commission engine | 50 |
| Affiliate ↔ website linking | 20 |

### P6 — Entertainer System — *Mar–Apr 2026* — **165 hrs**
| Item | Hrs |
|---|---:|
| Entertainer registration + public profile pages | 50 |
| Entertainer admin (approve / reject / commission) | 40 |
| Entertainer portal (dashboard, packages, settings, wallet) | 55 |
| Commission lifecycle + default commission handling | 20 |

### P7 — Social Feed / Roll Call — *Mar–Apr 2026* — **210 hrs**
| Item | Hrs |
|---|---:|
| Feed models (profiles, slugs, performance dates, real-profile sync) | 50 |
| Feed posts (media items, club authors, scheduling) | 55 |
| Approval / moderation workflow + bulk approve + reviews | 35 |
| Comments + comment moderation | 25 |
| Public feed, club feed, roll-call & model-profile pages | 45 |

### P8 — Job Marketplace — *Apr 2026* — **100 hrs**
| Item | Hrs |
|---|---:|
| Public job listings, detail, apply & pre-apply flows | 50 |
| Admin job posts, applications & preference requests | 50 |

### P9 — RBAC & Multi-User Admin — *Apr–May 2026* — **140 hrs**
| Item | Hrs |
|---|---:|
| Granular roles & permissions system | 50 |
| Website users management | 35 |
| Manager users (super-admin scope) | 30 |
| Route-permission middleware & access guards | 25 |

### P10 — Incident Reporting & Witness Statements — *Apr–May 2026* — **130 hrs**
| Item | Hrs |
|---|---:|
| Incident CRUD + attachments + status workflow | 55 |
| Incident audit log | 20 |
| Witness reports (public tokenized form, print, download, export) | 55 |

### P11 — Withdrawals & Payouts — *Apr–May 2026* — **90 hrs**
| Item | Hrs |
|---|---:|
| Payout methods + withdraw requests (portal) | 45 |
| Admin approval, status changes & charges (affiliate + entertainer) | 45 |

### P12 — Advanced Packages, Targeting & Analytics — *May 2026* — **160 hrs**
| Item | Hrs |
|---|---:|
| Package audience targeting (affiliate / entertainer / general) | 45 |
| Package features, images, "most popular", sort ordering | 45 |
| Advanced promo codes (audience, target owner, advanced rules) | 30 |
| Venue theming extensions (tab colors/icons, ribbons, subtitles, section text) | 25 |
| Google Analytics integration | 15 |

### P13 — Reporting & Analytics Suite — *May–Jun 2026* — **140 hrs**
| Item | Hrs |
|---|---:|
| Report engine + categories + metadata | 70 |
| Report permissions + user preferences + saved reports | 40 |
| Exports (PDF / CSV) | 30 |

### P14 — Compliance & Operations — *Jun 2026* — **240 hrs**
| Item | Hrs |
|---|---:|
| W-9 tax forms (secure tokenized form, automated PDF generation, ID upload, admin review) | 70 |
| Ticket scanning & check-in (QR lookup, check-in photo, ID-photo capture) | 65 |
| SMS integration (Telnyx + Aloware, delivery webhooks, troubleshooting) | 55 |
| Bot prevention (reCAPTCHA, throttling, honeypot form protection) | 30 |
| Payment-gateway abstraction + sandbox mode + gateway fields | 20 |

### CC — Cross-Cutting (entire engagement) — **200 hrs**
| Item | Hrs |
|---|---:|
| Bug fixing, regressions & production hotfixes | 110 |
| QA / testing cycles | 40 |
| DevOps, deployments & database schema management | 50 |

---

## 4. Build Timeline (from schema & file dates)

| Period | What was built |
|---|---|
| **Jul 2025** | Project created (7/28). Multi-tenant websites, packages, add-ons, events, transactions, emails/SMTP, settings, promo codes — core schema. |
| **Aug–Sep 2025** | Checkout flow, fee engine, payment integrations, transaction lifecycle, archiving across entities. |
| **Oct–Nov 2025** | Branding & customization — payment logos, button text, transportation confirmation, per-user website fields, font colors, slugs. |
| **Nov 2025–Feb 2026** | Custom-invoice refinement, shared carts, checkout popups. |
| **Mar–Apr 2026** | Affiliate & entertainer systems, package categories, public-page content, event gallery/hero, social feed. |
| **Apr–May 2026** | Job marketplace, role-based access control, incident reporting & witness reports, withdrawals/payouts, OAuth signup, manager users. |
| **May 2026** | Package targeting, commission lifecycle, package features/images/ordering, Google Analytics, theming extensions. |
| **Jun 2026** | Reporting & analytics suite, W-9 compliance, ID-photo & check-in scanning, Telnyx/Aloware SMS, payment-gateway sandbox mode. |

---

## 5. Capability Summary

**Storefront / checkout** — multi-tenant venue pages, multiple checkout templates, package & add-on selection, guest-count/quantity logic, fee engine (service charge, gratuity, sales tax, processing fee), promo & auto-discount codes, Stripe & Authorize.Net payments, reservations/deposits, shared carts, checkout popups, thank-you/receipt, transactional email.

**Marketplace & partners** — affiliate system (registration, public pages, portal, wallet, commissions), entertainer system (profiles, portal, wallet, commissions), job marketplace (listings, applications, preference requests), social feed / roll call (model profiles, posts, comments, moderation).

**Operations & admin** — 22-module admin, role-based access control with website & manager users, transaction management with QR scanning / check-in / ID capture, incident reporting with witness statements & audit logs, custom invoicing, withdrawals & payouts, reporting & analytics suite with exports.

**Compliance & integrations** — W-9 tax-form collection with PDF generation & ID verification, SMS (Telnyx + Aloware) with delivery webhooks, OAuth social signup, reCAPTCHA + throttling bot prevention, Google Analytics, payment-gateway sandbox mode.
</content>
</invoke>
