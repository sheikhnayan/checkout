# Master Capabilities & Hours — CartVIP + James Website Builder

**Prepared:** 2026-06-17
**Engagement span:** ~14 months (May 2025 → Jun 2026)
**Platforms covered:** two products — **CartVIP** (events & venue checkout) and **James Website Builder** (multi-tenant fundraising & site-building SaaS) — built by the same team and sharing architectural foundations and reusable components.

| Platform | Built To Date | Hours |
|---|---|---:|
| CartVIP | Events & venue checkout platform | **2,935** |
| James Website Builder | Fundraising & no-code website-builder SaaS | **4,860** |
| | **Combined engineering effort** | **7,795** |

> Every feature is listed with the estimated engineering hours required to build it. Hours are derived from the functionality present in the delivered platforms; build dates are taken from each system's internal records. Section 1 describes the method used. Full line-item detail for each platform is in that platform's own document.

---

## 1. How the Hours and Dates Were Determined

This section describes how the hour figures were produced, how the build timeline was established, and how the work maps to the team that delivered it.

**What one "hour" represents.** Each hour covers the engineering work required to take a feature from concept to a finished, production-ready state — requirements and planning, design, building, testing, revisions, and issue-fixing. Hours measure build-and-delivery effort, not only the time to produce a first draft.

**How the hours were calculated.** The hours were estimated bottom-up. Each platform was reviewed feature by feature — every screen, workflow, integration, and data structure present in it — and each feature was assigned an effort figure based on its complexity. Each hour corresponds to a specific, identifiable part of the working products.

**How the timeline was established.** Build dates are taken from each platform's internal records — the timestamps created as each part of a system was added or changed. These provide a chronological record of when each part was built.

**Team & staffing.** Both platforms were built by the same team, with size varying by workload across the engagement. Period-by-period staffing is given in each platform's own document.

| Platform | Developer-months | Peak team |
|---|---:|---:|
| CartVIP | 24 | 3 |
| James Website Builder | 34 | 4 |
| **Combined** | **58** | **—** |

---

## 2. Combined Executive Summary

| | CartVIP | James Website Builder | Combined |
|---|---:|---:|---:|
| Delivered engineering hours | 2,935 | 4,860 | **7,795** |
| Feature modules | 40+ | 55+ | **95+** |
| Distinct data entities | 44 | 70+ | **114+** |
| Database schema updates | 160+ | 180+ | **340+** |

---

## 3. CartVIP — Module Breakdown — **2,935 hrs**

*Events & venue checkout platform. Build window: Jul 2025 → Jun 2026.*

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
| | | **CartVIP Total** | **2,935** |

**Capabilities.** Multi-tenant venue pages with multiple checkout templates · package & add-on selection with a fee engine (service charge, gratuity, sales tax, processing fee) · Stripe & Authorize.Net payments · reservations/deposits, shared carts, checkout popups · affiliate & entertainer marketplaces with portals, wallets and commissions · social feed / roll call · job marketplace · role-based access control with website & manager users · transaction management with QR scanning, check-in and ID capture · incident reporting with witness statements & audit logs · custom invoicing · withdrawals & payouts · reporting suite with exports · W-9 tax compliance · SMS, OAuth login, bot prevention.

---

## 4. James Website Builder — Module Breakdown — **4,860 hrs**

*Multi-tenant fundraising & no-code site-building SaaS. Build window: May 2025 → Jun 2026.*

| Phase | Period | Module Group | Hours |
|---|---|---|---:|
| P0 | May 2025 | Template & builder foundation | 110 |
| P1 | May–Jun 2025 | Core platform & multi-tenant foundation | 360 |
| P2 | Jun–Aug 2025 | Page & site builder | 420 |
| P3 | Jun–Jul 2025 | Donations, tax receipts & multi-rail payments | 380 |
| P4 | Aug–Oct 2025 | Tickets & events | 220 |
| P5 | Jul–Aug 2025 | Auctions & live bidding | 180 |
| P6 | Sep–Nov 2025 | Investments / real-estate offerings | 320 |
| P7 | Aug–Oct 2025 | Sponsors, newsletter & QR donations | 130 |
| P8 | Sep–Oct 2025 | Analytics suite | 260 |
| P9 | Oct 2025 | Payment funnel analytics | 150 |
| P10 | Oct–Nov 2025 | Heatmaps & session recording | 240 |
| P11 | Sep–Nov 2025 | A/B testing | 200 |
| P12 | Nov 2025 | Cohort analysis | 160 |
| P13 | Nov–Dec 2025 | Fraud detection | 190 |
| P14 | Nov–Dec 2025 | Reporting & scheduled exports | 200 |
| P15 | Dec 2025 | Push notifications | 120 |
| P16 | Dec 2025 | Crypto payments | 110 |
| P17 | Dec 2025 | Roles, permissions & user management | 180 |
| P18 | Dec 2025–Jan 2026 | Teachers / students module | 140 |
| P19 | Jan–Mar 2026 | Dynamic email system & preferences | 170 |
| P20 | Feb–Mar 2026 | Menu builder & builder upgrades | 160 |
| P21 | Apr–Jun 2026 | SaaS multi-tenant hardening | 220 |
| CC | Ongoing | Cross-cutting (QA, bug-fixing, DevOps) | 240 |
| | | **James Website Builder Total** | **4,860** |

**Capabilities.** Multi-tenant fundraising sites with a drag-and-drop page, header, footer & menu builder · one-time & recurring donations, tipping, tax receipts · ticketed events and live auctions with real-time bidding · investment / real-estate offerings with investor profiles and live market data · Stripe, Authorize.Net & Coinbase (crypto) payments plus ACH/wire/mailed-check payouts · analytics (visitors, funnels, heatmaps, session recording, A/B testing, cohort/retention) · configurable fraud detection · role-based access control · per-website email system · push notifications · QR-code donations · scheduled Excel/PDF reporting.

---

## 5. Combined Capability View

Across the two platforms, the delivered capabilities include:

- **Multi-tenancy** — both run multiple independent client sites from one system, with per-site branding, payments, roles and data separation.
- **Payments** — combined: Stripe, Authorize.Net, Coinbase (crypto), and offline methods (direct deposit, wire, mailed check), with fee handling, tipping, refunds and tax receipts.
- **Commerce & fundraising** — checkout, packages, add-ons, reservations, tickets, auctions with live bidding, donations (one-time & recurring), sponsorships and investment offerings.
- **Site building** — drag-and-drop page, header, footer and menu builders with templates, custom fonts and SEO controls.
- **Analytics** — funnels, heatmaps, session recording, A/B testing, cohort/retention analysis, UTM/referrer tracking and scheduled reporting.
- **Operations** — fraud detection, role-based access control, incident reporting, identity capture & check-in, compliance (W-9, tax receipts), and email/SMS/push notifications.

---

## 6. Combined Build Timeline (high level)

| Period | Focus |
|---|---|
| **May–Aug 2025** | James Website Builder foundation & core — template/builder engine, multi-tenant sites, page builder, donations, multi-rail payments, tickets, auctions, tax receipts. |
| **Jul–Sep 2025** | CartVIP core — multi-tenant checkout, fee engine, payments, transactions, admin. |
| **Sep–Dec 2025** | James Website Builder — investments, analytics suite, heatmaps, session recording, A/B testing, cohorts, fraud detection, reporting, push notifications, crypto. |
| **Oct–Nov 2025** | CartVIP branding & customization. |
| **Dec 2025–Jan 2026** | James Website Builder access control, teachers/students module. |
| **Jan–Mar 2026** | James Website Builder dynamic email system, menu builder, builder upgrades. |
| **Mar–Jun 2026** | CartVIP expansion (affiliates, entertainers, social feed, jobs, access control, incidents, payouts, targeting, reporting, compliance) and James Website Builder SaaS hardening. |

---

## 7. Upcoming Phase — AI Capabilities *(projected, not included in the totals above)*

AI features are planned for **James Website Builder** as the next phase. The items below are estimates and are not part of the delivered hours above.

### AI Capabilities (projected: 420–560 hrs)
- AI donation & checkout assistant (conversational, guided giving)
- AI page & content generation built into the site builder
- Natural-language analytics — "ask your data" across the analytics suite
- AI-enhanced fraud & anomaly detection
- Smart donor segmentation & personalized recommendations
- Automated AI report summaries & plain-language insights

---

> **Rate note:** apply your blended hourly rate to the **7,795** combined total (and to the AI estimate range) to produce the dollar-denominated figure. Full per-feature line items for each platform are in the CartVIP and James Website Builder documents.
</content>
