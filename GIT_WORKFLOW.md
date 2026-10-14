# Retail POS Git workflow

This project keeps source code readable. Do not minify PHP/Blade during development.

## First-time setup

```bash
git init
git add .
git commit -m "Initial Retail POS project"
```

If you have a GitHub/GitLab repository, add the remote you own and push the branch normally.

## Small changes

For a small bug/UI change, commit only the changed files:

```bash
git status
git add path/to/changed-file.blade.php path/to/changed-controller.php
git commit -m "Fix dashboard UI"
```

Pull before starting work when a remote is configured:

```bash
git pull --rebase
```

## Laravel runtime folders

`.gitkeep` files are intentionally present in `storage/framework/sessions`, `storage/framework/views`, `storage/framework/cache`, and `storage/logs` so fresh Git checkouts keep the required directories.

Do not commit real session files, logs, uploaded customer data, or `.env` secrets.

## Database safety

Use:

```bash
php artisan migrate
```

Do not use `php artisan migrate:fresh` on a database containing real POS data unless a full reset is intentionally required.
