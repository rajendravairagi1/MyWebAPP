# Business Requirement Document (BRD)
## Job Work Manufacturing ERP — Paint & Coating Industry

**Version:** 1.0 (Draft)
**Prepared for:** Internal Planning — Pre-Development
**Status:** Requirement Capture Complete — Flow/Database/UI Design Pending

---

## 1. Project Summary

Ye ek **Job Work based Manufacturing ERP** hai — paint/coating factory ke liye jahan customer apna material bhejta hai processing (sanding, priming, painting, coating) ke liye, aur factory use process karke wapas dispatch karti hai invoice ke saath. System multi-role hai — Owner se lekar Security Guard tak, har role ka apna dashboard aur access hoga, jo **Owner dynamically control** kar sakta hai (checkbox-based module access).

---

## 2. User Roles

| # | Role | One-line Responsibility |
|---|---|---|
| 1 | **Owner** | Poori company ka control — sab kuch dekh sakta hai, sabko permission de sakta hai |
| 2 | **Plant Head** | Owner jaisa hi, sirf Owner-only special features (jaise BD/meetings) chhodkar — production/store/purchase sab approve karta hai |
| 3 | **HR Manager** | Employee hire/fire, records, role-wise salary setup |
| 4 | **Purchase Manager** | Company ke liye material/goods purchase karta hai, ledger maintain |
| 5 | **Account Manager** | Poori company ka payment/ledger — income, expense, salary approval, purchase ledger |
| 6 | **Store Person** | Material in/out, job-wise consumption entry, stock ki visibility |
| 7 | **Quality (QC) Person** | Dispatch se pehle product verify/approve karta hai |
| 8 | **Security Guard** | Gate pe dispatch ho raha maal double-check karta hai (2nd verification layer) |
| 9 | **Business Development (BD)** | Naye customer laata hai, apne hi customers ka data dekhta hai, meeting/follow-up manage karta hai |
| 10 | **Employee (Production Floor)** | Attendance, apni salary slip — production stages me kaam karta hai |

> **Note:** Filhal Owner aur BD ek hi vyakti hai — lekin system me role alag-alag design honge, taki baad me jab alag BD hire ho to seedha wahi role assign ho jaye, koi rebuild na karna pade.

---

## 3. Dynamic Role-Based Permission System (Important — Core Requirement)

Ye ek **cross-cutting system feature** hai, koi ek module nahi — poore ERP ka access-control isi pe based hoga.

**Kaise kaam karega:**
1. Owner "Manage Roles" screen par jayega
2. Har role ke liye — saare modules ki **checkbox list** dikhegi (Dashboard, CRM, Job Work, Store, Purchase, Accounts, HR, Reports, etc.)
3. Owner jis module ka checkbox ON karega, wo role us module ko dekh/use kar payega
4. **Runtime flexible** — Owner kabhi bhi kisi role ko extra access de sakta hai (jaise "Account Manager ko Store bhi dikhana hai") — bina code change ke, seedha checkbox se
5. Har module ke andar bhi **Read / Write / Approve** level ka access ho sakta hai (future refinement)

**Technical approach:** Database me `roles`, `modules`, aur `role_module_permissions` (mapping table) — Laravel me Spatie Permission package isi ke liye best-fit hai.

---

## 4. Owner Dashboard (Command Center)

Owner ko login karte hi ek nazar me sab dikhna chahiye:

### Financial Overview
- Total Invoice Value (kitni invoice bani, total amount)
- Total Payment Received
- Total Payment Pending (Outstanding)
- Daily / Monthly / Custom Date Range filter
- Job-wise Profit (Invoice Amount − Material Cost − Labor Cost − Other Cost, job complete hone ke baad auto-calculate)

### Daily Operations Snapshot
- Aaj kaunsa raw material stock se nikla (kitna, kis job ke liye)
- Aaj kitna dispatch hua
- Aaj kitna material aaya (naya job customer se)
- Aaj kitne employee present

### Customer Analytics
- Customer List with payment status (paid/pending)
- **Top Customer Chart** — kaun sabse zyada business de raha hai (revenue-wise)
- **Most Profitable Customer** — kis customer se sabse zyada margin mil raha hai
- Customer-wise pending/history

### Expense Overview
- Daily expense total (petrol, chai, transport, BD visit expense, etc.)

**Design requirement:** Har widget **filterable** ho (date range, customer, employee, product) — jitna clear utna acha, kyunki Owner ko "sab kuch haath me chahiye."

---

## 5. Core Business Flow (End-to-End)

```
Customer (naya ya existing)
      │
      ▼
Customer Registration — Details + Documents Upload
      │
      ▼
Material Aata Hai (Paint/Coating ke liye)
      │
      ▼
Vehicle Entry — Gadi Number + Photo + Challan Details
      │
      ▼
Job Entry — Kitne pieces aaye (5, 10, jitne bhi)
      │
      ▼
Due Date Set — kab tak complete karna hai
      │   (1 din pehle reminder automatic aayega)
      ▼
PENDING JOBS list me chala jata hai
      │
      ▼
Store se Material Issue (Job ID select karke)
      │  Thinner, Primer, Sand Paper, Color — kitna nikala, kis job ke liye
      ▼
PRODUCTION SHURU (WORKING JOBS)
      │
      ▼
Sanding → Industrial Putty → Fine Sanding → Primer →
Drying → Paint → Heat/Oven Drying → Cooling →
Quality Check → Plastic Wrapping → Box Packing
      │
      ▼
READY TO DISPATCH (QC Verified)
      │
      ▼
Job Complete → Material Balance Check:
   • Kuch bacha → Store me wapas jama
   • Sab use ho gaya → koi entry nahi
   • Kam pada → New Material Issue entry (dobara)
      │
      ▼
Vehicle Load → Dispatch → Invoice + Challan Generate
      │
      ▼
Payment Term ke hisaab se Reminder (jaisa customer se tय हुआ — 25/45 din)
      │
      ▼
Payment Follow-up (agar due date nikal gayi, follow-up date generate hoga)
      │
      ▼
Payment Received → Invoice "Paid" → History me move
      │
      ▼
Customer Profile me: Pending Jobs / Working Jobs / History — sab updated
```

---

## 6. Module-Wise Requirement Detail

### 6.1 CRM — Customer Management
- New customer create — full details, GST, documents upload
- Har customer ka: Invoice History, Payment History (paid + pending), Profit generated from this customer
- Paid invoices automatically **History** section me move ho jaye
- Payment aate hi customer ke due balance se minus ho

### 6.2 Job Work Module
- Job Receive Entry: Vehicle Number, Photo, Challan
- Product/Piece count entry (variable — 5, 10, jitne bhi)
- Due Date set + **1-din-pehle automatic reminder**
- Status tracking: Pending → Working → Ready → Dispatched

### 6.3 Material Consumption (Store-Linked)
- Job ID select karke material issue (Thinner, Primer, Sand Paper, Paint — quantity)
- Job complete hone ke baad 3 scenarios handle ho:
  1. Material bacha → Store me return entry
  2. Exact use hua → kuch nahi
  3. Kam pada → naya issue entry (same job ke against)
- Har job ka **total material cost** track ho (costing engine ke liye zaroori)

### 6.4 Production Flow (Stage-wise Tracking)
Stages: Sanding → Industrial Putty → Fine Sanding → Primer → Drying → Paint → Heat/Oven → Cooling → Quality Check → Wrapping → Packing → Ready

- Har stage: Start Time, End Time, Employees involved
- **Job Completion Time Analytics:** Attendance data se calculate — "agar 40 employees ne 1 din kaam kiya to X pieces complete hue; ab agar 80 employees available hain to same job aadhe time me hoga" — labor-hours based estimation formula
- Sab jobs ke completion time ka history record — future estimation ke liye base data

### 6.5 HRMS
- HR Manager employee hire/fire karega, verified record banayega
- Role/posting ke hisaab se salary set — Account Manager approve karega
- Document upload (Aadhaar, PAN, photo, etc.)

### 6.6 Attendance & Payroll (Detailed Logic)
- **Per-day salary model:** e.g. ₹800/day for 8 hours
- **Overtime:** Per-hour rate auto-calculate hoga salary se (₹800/8hrs = ₹100/hr) — extra hours verified karke add
- **Late deduction:** Agar employee 10 min late aaya, uska proportional deduction (per-minute/per-hour rate se calculate) — kyunki 40 employees x 10 min roz ka company-wide loss significant hai
- **Bonus:** Manual add option — festival bonus, role-wise alag amount (e.g. worker ₹500, management ₹1000)
- **Salary Slip PDF:** Total hours worked, PF deduction, Allowances, Deductions, Net Salary — sab breakdown ke saath

### 6.7 Purchase Module
- Purchase entry — kya kharida, kitne ka
- Store me automatically add hota jaye (received status)
- Ledger: Purchase Manager ke naam se track (Account ko pata rahe kis manager ne kya purchase kiya)

### 6.8 Store Module
- Har incoming material verify karke accept
- Job-wise consumption visibility — click karke list dikhe "is job me kitna material laga"
- **Low stock alert:** Owner/Plant Head jo limit set kare, us se neeche jaate hi notification
- Working jobs ka material dashboard pe live dikhna chahiye

### 6.9 Purchase → Store → Account Chain
- Purchase Manager entry karta hai → Store verify karta hai → Account automatically ledger update karta hai (balance minus purchase amount, plus jo aaya uska record)

### 6.10 Account Module
- Poori company ka payment ledger — income (customer payments) − expense (salary, purchase, daily expenses)
- Daily/petty expenses: BD visit ka petrol/nasta/stay — BD submit kare, Account approve/reimburse kare
- Real-time balance visibility — "hum abhi kis financial position par hain"

### 6.11 Plant Head Role
- Owner jaisa hi visibility (Owner-specific BD/meeting features chhodkar)
- Store me aaya material verify/approve kar sake
- Stock level, remaining days ka estimate, kaunsa challan aaya/jaana hai — sab dikhe
- Approval-based workflow — kai actions Plant Head ke approval se hi aage badhein

### 6.12 Quality (QC) Module
- Dispatch se pehle final check
- QC verify karke "Ready for Dispatch" status set kare — tabhi aage invoice/dispatch process chalu ho

### 6.13 Dispatch + Invoice
- QC approved hone ke baad Account Manager invoice + challan generate karega
- Customer ko Email ya Print — dono option
- **QR Code on Invoice/Challan** — verification ke liye ki ye document genuine hai
- Vehicle number + photo bill/challan ke saath print hoga

### 6.14 Security Gate (2nd Verification Layer)
- Gate pe security guard dispatch hone wala maal check karega
- Receipt lega — double verification (Account + Security) ensure karega ki sahi maal, sahi invoice ke saath ja raha hai

### 6.15 Business Development (BD) — Restricted View
- Apne khud ke laaye customers ka hi data dekh sakta hai (baaki customers nahi)
- Apne customers ki invoice/payment status dekh sakta hai (sirf apna commission/business track karne ke liye)
- Meeting & Follow-up system:
  - Naya lead/meeting schedule
  - **1-din-pehle notification** — "kal is time meeting hai"
  - Meeting ke baad outcome record → agar dobara follow-up chahiye to naya reminder auto-generate

### 6.16 Owner — Special Features
- Sab kuch jo BD ke paas hai (kyunki abhi Owner khud BD hai) — Visit planning, Meeting calendar, Follow-up tracking
- **BD Performance View (jab alag BD hire ho):** Har BD kitne customer laaya, kitna business diya, kitna profit hua, kitna commission banta hai — sab compare (BD-wise + overall)

---

## 7. Notifications (Cross-Module)

| Trigger | Kisko | Kab |
|---|---|---|
| Job due date | Plant Head, Store | 1 din pehle |
| Payment due (per customer terms) | Account, Owner | Due date pe + follow-up cycle |
| Low stock | Store, Plant Head, Owner | Set limit cross hote hi |
| Meeting scheduled | Owner/BD | 1 din pehle |
| Salary due/processed | Account, HR | Monthly cycle |
| Purchase approval pending | Plant Head/Owner | Real-time |

---

## 8. Reports Needed (Filterable — Date/Customer/Employee/Product)

- Daily/Monthly Revenue & Profit
- Customer-wise Business & Profit
- BD-wise Performance (leads, conversion, revenue, commission)
- Job-wise Costing (material + labor + profit)
- Employee-wise Attendance, Overtime, Productivity
- Stock Consumption & Wastage
- Purchase Ledger (manager-wise)
- Outstanding Payment Report
- Salary Register

---

## 9. Kya Extra Add Ho Sakta Hai (Suggestions)

Jo aapne nahi bola, lekin is scale ke ERP me useful hoga:

1. **Barcode/QR per Job** — job ID scan karke turant status pata chale (production floor pe useful)
2. **Digital Signature on Gate Pass** — security guard tablet pe signature le sake
3. **Customer Self-Service Portal** — customer khud login karke apni job status dekh sake (support calls kam honge)
4. **WhatsApp Auto-Notification** — invoice, reminder, job-ready — sab WhatsApp pe bhi jaye
5. **Multi-Branch Support** — agar kabhi doosri factory/branch khule, ek hi system se manage ho
6. **Audit Log** — kisne kab kya change kiya, poora trail (enterprise systems me zaroori hota hai disputes ke liye)

---

## 10. Open Questions (Design Phase Se Pehle Clarify Karna Hai)

1. Payment terms customer-wise fixed hain ya invoice-wise alag ho sakte hain?
2. Advance payment / partial payment allowed hai kya customer se?
3. Ek job me multiple products/pieces alag-alag stages me ho sakte hain (kuch ready, kuch abhi paint me)?
4. Employee overtime — kya daily cap hai (max kitne extra hours allowed)?
5. Security Guard ke paas login/device hoga ya sirf paper receipt?

---

## 12. Next Steps

1. ✅ **Requirement Capture** — is document ke saath complete
2. ⏭ **Flow Diagrams** — har role ka alag user-flow diagram (visual)
3. ⏭ **Database Design (ER Diagram)** — tables aur relationships
4. ⏭ **Screen Wireframes** — har role ke dashboard/screens
5. ⏭ **Development (Laravel, Phased)** — MVP se shuru

---

*Ye document living hai — jaise-jaise clarity aayegi, update hota rahega development shuru hone se pehle.*
