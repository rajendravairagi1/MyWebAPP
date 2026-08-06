# Development Plan
## Job Work Manufacturing ERP — Paint & Coating Industry

**Version:** 1.0
**Companion Documents:** `BRD.md` (requirements), `erp-flowchart.html` (process flow)
**Status:** Planning Complete — Ready to Start Development

---

## 1. Tech Stack (Final)

| Layer | Choice | Reason |
|---|---|---|
| Backend | **Laravel 11 (PHP)** | Aapke server pe chalega, solo-friendly, bahut packages ready-made milte hain |
| Frontend | **Blade + Tailwind CSS** (Laravel ke andar hi) | Alag React app manage nahi karna padega — solo build ke liye simple |
| Database | **MySQL** | Shared hosting/cPanel/VPS sab jagah standard support |
| PDF | **DomPDF (Laravel package)** | Invoice, Salary Slip, Reports — sab PDF isi se |
| Excel Export | **Laravel Excel (maatwebsite/excel)** | Reports ke liye |
| Auth + Roles | **Laravel Breeze + Spatie Permission** | Login + checkbox-based dynamic role/permission system |
| QR Code | **simple-qrcode (Laravel package)** | Invoice/Challan verification |
| **External APIs** | **KOI NAHI** | Confirmed — WhatsApp/SMS/Email-API/Payment Gateway/Biometric-sync — sab manual/internal rahega |
| Hosting | **VPS recommended** (shared hosting bhi chalega shuru me, chhote scale pe) | Payroll + multi-role + reports ka load |

> **Solo + AI-Assisted Build:** Aap khud banayenge, main (Claude) har module ki coding, database, aur logic me madad karunga.

---

## 2. Build Order — Phases (Sequence Matter Karta Hai)

Modules ek dusre pe dependent hain — isliye ye order follow karna zaroori hai (baad wala module pehle wale ka data use karta hai):

### Phase 1 — Foundation (2–3 weeks)
1. Laravel setup + MySQL database
2. Login system + **Role & Permission (checkbox-based module access)** — ye sabse pehle banega kyunki har module isi pe depend karega
3. Basic Dashboard shell (empty widgets, baad me data aayega)

### Phase 2 — Master Data (2 weeks)
4. Customer Master (details, documents upload, GST)
5. Product Master
6. Employee Master (basic — HR module se link hoga)
7. Raw Material Master (Thinner, Primer, Paint, Sand Paper, etc.)

### Phase 3 — Core Job Flow (5–6 weeks)
8. Job Receive Entry (Vehicle, Photo, Challan, Pieces, Due Date) + 1-day-before reminder
9. Store — Material Issue against Job ID
10. Production Stage Tracking (Sanding → Putty → Primer → Paint → Oven → Cool) — start/end time, employees
11. Material Settlement (Return / Used / Shortfall → re-issue loop)
12. Quality Check (QC approve/reject → rework loop)
13. Packing + Ready Stock

### Phase 4 — Dispatch & Money (4–5 weeks)
14. Dispatch + Security Gate verification
15. Invoice + Challan generation + QR Code
16. Payment Term tracking + follow-up reminder
17. Payment Received → Customer Ledger → History

### Phase 5 — HR & Payroll (4–5 weeks)
18. HR — Employee hire/fire, role/posting, document upload
19. Attendance (monthly CSV upload from biometric device)
20. Salary Engine — per-day rate, OT hourly calc, late-deduction, bonus, advance
21. Salary Slip PDF

### Phase 6 — Purchase & Accounts (3–4 weeks)
22. Purchase Entry → Store verify → auto Stock update
23. Purchase Ledger (manager-wise)
24. Account Module — full company ledger, daily/petty expenses, BD reimbursement

### Phase 7 — Analytics & CRM (3–4 weeks)
25. Owner Dashboard — full widgets (invoice totals, profit, customer chart, expense) with filters
26. Job Completion Time Analytics (labor-hours formula)
27. Business Development — restricted view, meeting/follow-up + reminders
28. Owner — BD performance comparison

### Phase 8 — Reports, Notifications, Polish (3–4 weeks)
29. All filterable Reports (Section 8 of BRD)
30. Notification Engine (in-app only — job due, payment due, low stock, meeting)
31. Testing with real data + bug fixing
32. Deployment to production hosting + training

**Total: ~26–33 weeks (6–8 months), solo full-time with AI assistance.**

---

## 3. Database — High-Level Table Groups

(Detailed ER Diagram banega Phase 1 shuru karte waqt — abhi sirf groups)

| Group | Key Tables |
|---|---|
| Access Control | `users`, `roles`, `modules`, `role_module_permissions` |
| Masters | `customers`, `customer_documents`, `products`, `raw_materials`, `employees` |
| Job Flow | `jobs`, `job_pieces`, `job_stage_logs`, `material_issues`, `material_settlements` |
| Quality/Dispatch | `quality_checks`, `dispatches`, `security_gate_logs` |
| Finance | `invoices`, `invoice_items`, `payments`, `customer_ledger` |
| HR/Payroll | `attendance`, `salary_records`, `advances`, `bonuses` |
| Purchase/Accounts | `purchases`, `purchase_ledger`, `expenses`, `company_ledger` |
| CRM/BD | `leads`, `meetings`, `followups`, `bd_customer_map` |

---

## 4. What's Confirmed (Decisions Locked)

- ✅ AI features — **excluded** (formula-based logic only, revisit after 3–6 months of real data)
- ✅ External APIs — **excluded** (no WhatsApp/SMS/Email-API/Payment Gateway/Biometric-sync)
- ✅ Attendance — manual monthly CSV upload from biometric device
- ✅ Invoice/Salary-slip sharing — PDF download/print, manual send
- ✅ Stack — Laravel + MySQL + Blade + Tailwind, no separate frontend framework
- ✅ Solo build with AI (Claude) assistance
- ✅ Role/permission system — dynamic, checkbox-based, Owner-controlled

---

## 5. Open Items (Answer Before/During Phase 1)

From `BRD.md` Section 10 — needs your input as we build:

1. Payment terms — customer-wise fixed ya invoice-wise alag?
2. Partial/advance payment allowed hai kya?
3. Ek job me multiple pieces alag-alag stages me simultaneously ho sakte hain?
4. Overtime — daily max cap hai kya?
5. Security Guard ke paas login/device hoga ya paper receipt?

---

## 6. Next Immediate Step

**Phase 1 shuru karna hai:**
1. Laravel project fresh setup
2. Database connection + migrations start
3. Role & Permission system (checkbox UI) — pehla real screen

Iske baad seedha coding shuru hogi — module by module, jaisa upar phase list me hai.

---

*Reference: `BRD.md` (full requirement detail), `erp-flowchart.html` (visual process flow). Ye teen documents mil kar complete pre-development package hain.*
