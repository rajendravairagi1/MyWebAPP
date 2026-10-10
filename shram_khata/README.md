# LabourBook

Android app for labour contractor agencies: attendance (P / A / H + overtime),
daily payments and ledger, company contracts and billing, PDF statements and
invoices, with a live dashboard. Works offline; data is stored on the phone in
SQLite.

## Plans
| Plan | Who | What it unlocks |
|---|---|---|
| Solo | One person does everything | All features, single user |
| Owner + Team | Owner with supervisors / accountants | Team members, custom roles with per-permission switches, supervisors limited to their sites |
| Company | Several branches | Branches, branch switcher, per-branch team |

Limits live in `lib/core/plans.dart`.

## Run
```bash
flutter pub get
dart run build_runner build --delete-conflicting-outputs   # only after schema changes
flutter run
flutter test
flutter build apk --release
```

## Layout
- `lib/data/` Drift schema (`tables.dart`) and one service per area (`services/`).
- `lib/domain/` pure money / wage / ledger rules (`calc.dart`) and business profile.
- `lib/pdf/` statement, invoice (with attendance annexure) and register PDFs.
- `lib/features/` screens. `lib/state/providers.dart` holds session and shared data providers.
- `test/` service tests, PDF rendering, and widget tests that walk every main screen.

## Rules worth knowing
- Money is stored as integer paise. Dates are `yyyy-MM-dd` text.
- Labour is deactivated, never deleted (unless nothing refers to them). Payments are voided, never removed.
- A person cannot be marked for more than one full day across sites (H + H is fine).
- Pay rates are dated: changing a rate never rewrites earlier days.
- Invoices copy their lines when created, so later rate changes do not alter them.

## Not built yet
- Cloud sync, email verification and password reset (needs a backend; ids are uuids so rows can be merged later).
- PF / ESI, Hindi UI strings, holidays.

## Play Store
Icon, store graphics, screenshots, privacy policy, listing text and the step-by-step guide are in `store_assets/`
(start with `store_assets/PLAY_STORE_STEPS.md`). Release signing reads `android/key.properties`
(copy `key.properties.example`; the real file is git-ignored).
