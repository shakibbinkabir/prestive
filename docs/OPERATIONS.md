````
# Operations Guide

This document explains routine backups and restore steps, plus where files are saved.

## Backup outputs

- Database dumps (gzipped): `storage/backups/db_YYYYmmdd_HHMMss.sql.gz`
- Storage archive (uploads/logs): `storage/backups/storage_YYYYmmdd_HHMMss.zip`

Ensure `BACKUP_DIR` is set in `.env` if you need a different path.

## Run backups

From the project root:

```powershell
php scripts/backup_db.php
php scripts/backup_storage.php
```

Requirements:
- `mysqldump` available in PATH.
- DB env vars set: `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`.

## Restore database

1. Extract SQL: `gunzip db_YYYYmmdd_HHMMss.sql.gz`
2. Import:

```powershell
mysql -h <host> -P <port> -u <user> -p <db> < db_YYYYmmdd_HHMMss.sql
```

## Restore storage files

Unzip `storage_YYYYmmdd_HHMMss.zip` into the `storage/` folder, preserving structure.

## Scheduling

### Linux (cron)

Example daily at 02:00:

```
0 2 * * * cd /var/www/prestiveform && /usr/bin/php scripts/backup_db.php >> storage/logs/ops.log 2>&1
10 2 * * * cd /var/www/prestiveform && /usr/bin/php scripts/backup_storage.php >> storage/logs/ops.log 2>&1
```

### Windows (Task Scheduler)

- Action: Start a program
- Program/script: `php`
- Arguments: `D:\xaamp\htdocs\prestiveform\scripts\backup_db.php`
- Start in: `D:\xaamp\htdocs\prestiveform`

Create another task for `scripts\backup_storage.php`.

## Cleanup

- Rotate or prune old backups periodically.
- Ensure sufficient disk space; monitor `storage/logs` growth.

````
