# Implementation status

## Updated in this source package
- Laravel/Blade architecture; no React/Vue/Angular/Node backend.
- XAMPP MySQL configuration.
- Product SKU is server-generated and immutable after creation; Product `$fillable` now includes `sku` and the model has a fallback server-side SKU generator.
- Multiple globally unique product barcodes.
- Product archive/restore workflow using soft deletes.
- Product image upload validation and public storage reference.
- Responsive Glassmorphism shell and light/dark theme.
- Authentication and flexible Admin/Manager/Cashier/Sales Staff roles.
- Fine-grained backend permission middleware plus permission-aware navigation.
- Custom role create/edit/delete with protected system roles.
- Last active Admin cannot be deleted, deactivated, or reassigned away from Admin.
- Product, category, brand, customer, supplier, expense CRUD.
- Expense Add/Edit/Delete UI.
- Supplier and customer due ledgers with real payment records.
- POS barcode/name/SKU lookup, live product suggestions, cart, payment and transactional stock decrease.
- Purchase workflow with transactional stock increase and supplier due calculation.
- Stock movement records.
- Sales return stock restoration.
- Dashboard and profit calculations.
- Reports page.
- Settings and barcode management.
- CSRF, hashed passwords, server-side validation, database constraints, row locking for inventory changes.
- User deletion is soft-delete based so transaction history remains intact.

## Still partial / production expansion areas
- Held bills/resume/delete.
- Full return ledger with return_items table.
- 58/80mm/A4 invoice print templates and print CSS.
- Full chart/report filtering and export.
- Admin database backup/restore UI.
- Complete activity-log middleware coverage.
- Optimized image thumbnails.
- Automated tests and deployment hardening.
- SMS low-stock alerts are intentionally postponed.
