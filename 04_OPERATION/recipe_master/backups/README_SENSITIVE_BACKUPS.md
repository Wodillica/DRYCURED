# Sensitive database backups

This directory is not used for committed SQL backups.

Database backups may contain secrets, API keys, tokens, salts, emails, private URLs, user data or other sensitive material. Therefore:

- `*.sql`, `*.sql.gz` and `*.dump` files must never be committed.
- Real database backups must be stored outside the Git repository.
- Current external backup location used by this workflow: `/root/DRYCURED_SENSITIVE_BACKUPS/recipe_master/`
- Git should contain only reports that a backup was created, not the backup file itself.

If GitHub push protection blocks a push because of a secret in a backup, do not unblock the secret. Remove the backup from the commit, rotate the exposed key, and clean local Git history/reflog.
