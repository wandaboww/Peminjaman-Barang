# SIM-Inventaris Codebase Instructions

## Overview
**SIM-Inventaris** is a school asset/equipment inventory management system with two parallel implementations:
- **Prototype**: Single-file PHP (`prototype.php`) with JSON database — portable, zero dependencies
- **Laravel**: Full MVC structure with MySQL migrations — production-ready, incomplete

The system manages borrowing/returning workflows for school assets (laptops, projectors) with role-based loan duration and blacklist enforcement.

## Architecture & Data Flow

### Core Business Logic (Universal across both implementations)
1. **Loan Eligibility Check** (`LoanService::canUserBorrow`)
   - Users can borrow only if they have NO active or overdue loans (1-item-per-user strict policy)
   - Teachers get 3-day loan duration; students get 1 day
   - Blacklisted users (overdue items) are blocked from new borrowing

2. **Borrowing Workflow** (Checkout)
   - Scan/input user identity (NIP for teachers, NIS for students)
   - Scan/input asset serial number or QR code hash
   - System validates user eligibility → asset availability
   - Calculate due_date based on role → create Loan record
   - Update asset status to 'borrowed'

3. **Return Workflow** (Checkin)
   - Scan asset serial number or QR code
   - Confirm condition (good/minor_damage/major_damage)
   - Update Loan status to 'returned', set return_date
   - Update asset status back to 'available'

### Key Data Models
- **Users**: id, identity_number (unique), role (admin/teacher/student), name, email, phone, is_active
- **Assets**: id, serial_number (unique), brand, model, status (available/borrowed/maintenance/lost), qr_code_hash (unique)
- **Loans**: id, user_id, asset_id, loan_date, due_date, return_date, status (active/returned/overdue/lost), digital_signature_path (optional)

## Implementation Patterns

### Prototype (`prototype.php`)
- **Database**: `JsonDB` class manages CRUD on `database.json` with in-memory sync
- **Authentication**: Simple `AuthManager` class; password in code (change in production)
- **Entry Points**: HTML form submissions trigger PHP functions that mutate JSON and return responses
- **Activity Logging**: `ActivityLog` class tracks all mutations to `activity_logs.json`
- **Validation**: `checkBlacklist()`, asset availability check inline in request handlers

### Laravel (`app/`)
- **Controllers**: `BorrowingController` handles `store()` (checkout) and `update()` (return)
- **Services**: `LoanService` encapsulates business logic (`createLoan()`, `canUserBorrow()`, `calculateDueDate()`)
- **Models**: Eloquent relationships (User → Loans, Asset → Loans)
- **Validation**: Form Request validation + service-layer business logic checks
- **Transactions**: DB transactions wrap checkout/return to ensure atomicity

## Extending the Codebase

### Adding New Features
1. **Prototype First**: Test new logic in `prototype.php` (lowest friction)
2. **Then Laravel**: Replicate logic in `LoanService` and Controllers
3. **Example**: To add fine/penalty logic:
   - Add `fine_amount` column to Loans schema
   - Implement calculation in `LoanService::returnLoan()`
   - Update both prototype return handler and Laravel controller

### Common Tasks
- **New asset status**: Update enum in Models + prototype `$validStatuses` check
- **New role/duration**: Extend `calculateDueDate()` in both implementations
- **Database schema changes**: Update Laravel migration, then update prototype seeding

## Critical Files & Their Roles
- [ARCHITECTURE.md](ARCHITECTURE.md) — ERD, folder structure, component responsibilities
- [DESIGN.md](DESIGN.md) — Detailed schema, business logic spec, role-based rules
- [prototype.php](prototype.php) — Full working implementation; reference for logic patterns
- [BorrowingController.php](app/Http/Controllers/BorrowingController.php) — Laravel checkout/return entry points
- [LoanService.php](app/Services/LoanService.php) — Shared business logic library
- [database.json](database.json) — Prototype's live data store (development only)
- [database/migrations](database/migrations/) — Schema definitions for Laravel/MySQL

## Testing & Debugging
- **Prototype**: Run `run-local-server.bat` (Windows) or `php -S 127.0.0.1:8000` (all platforms)
  - Opens on `http://127.0.0.1:8000` with admin password `admin123`
  - Inspect `database.json` and `activity_logs.json` for live state
  - See [TESTING.md](TESTING.md) for comprehensive test workflows
- **Laravel**: Run migrations, then use Postman/curl to test endpoints in `BorrowingController`
- **Blacklist validation**: Manually create active loan record, attempt new checkout → should reject
- **Due date calculation**: Check `loans.due_date` against `loans.loan_date` + 1 or 3 days

## Active Development Notes
- Laravel Controllers incomplete — `update()` (return flow) is partially implemented
- No frontend UI in Laravel yet (prototype only)
- Authentication in prototype is hardcoded; use Laravel's built-in auth in production
- QR code scanning: prototype uses JsBarcode client-side; Laravel migration includes `qr_code_hash` field but no generation logic yet
