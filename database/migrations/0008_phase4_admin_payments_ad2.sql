-- Phase 4: Admin payments enhancement and Ad-2 confirmations
-- Non-destructive, additive changes only.

-- Membership applications: admission and Ad-2 fields
ALTER TABLE membership_applications
    ADD COLUMN IF NOT EXISTS admission_id CHAR(6) NULL UNIQUE,
    ADD COLUMN IF NOT EXISTS ad2_confirmed_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS ad2_confirmed_by_user_id BIGINT UNSIGNED NULL,
    ADD COLUMN IF NOT EXISTS ad2_notes TEXT NULL,
    ADD INDEX IF NOT EXISTS idx_membership_admission_id (admission_id),
    ADD INDEX IF NOT EXISTS idx_membership_ad2_confirmed_at (ad2_confirmed_at);

-- Trainee applications: ensure admission_id exists and add Ad-2 fields
ALTER TABLE trainee_applications
    ADD COLUMN IF NOT EXISTS admission_id CHAR(6) NULL,
    ADD COLUMN IF NOT EXISTS ad2_confirmed_at DATETIME NULL,
    ADD COLUMN IF NOT EXISTS ad2_confirmed_by_user_id BIGINT UNSIGNED NULL,
    ADD COLUMN IF NOT EXISTS ad2_notes TEXT NULL,
    ADD INDEX IF NOT EXISTS idx_trainee_admission_id (admission_id),
    ADD INDEX IF NOT EXISTS idx_trainee_ad2_confirmed_at (ad2_confirmed_at);

-- Payments: add amount/currency/notes and extra indexes
ALTER TABLE payments
    ADD COLUMN IF NOT EXISTS amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER mode,
    ADD COLUMN IF NOT EXISTS currency CHAR(3) NOT NULL DEFAULT 'BDT' AFTER amount,
    ADD COLUMN IF NOT EXISTS notes TEXT NULL AFTER proof_upload_id,
    ADD INDEX IF NOT EXISTS idx_owner_payment_date (owner_type, owner_id, payment_date),
    ADD INDEX IF NOT EXISTS idx_created_at (created_at);
