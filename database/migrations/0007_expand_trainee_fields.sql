ALTER TABLE trainee_applications
ADD COLUMN name VARCHAR(150) NULL,
ADD COLUMN dob DATE NULL,
ADD COLUMN phone VARCHAR(40) NULL,
ADD COLUMN email VARCHAR(150) NULL,
ADD COLUMN last_or_current_education VARCHAR(150) NULL,
ADD COLUMN institution VARCHAR(200) NULL,
ADD COLUMN club_name VARCHAR(150) NULL,
ADD COLUMN membership_no VARCHAR(100) NULL,
ADD COLUMN father_name VARCHAR(150) NULL,
ADD COLUMN father_profession VARCHAR(150) NULL,
ADD COLUMN mother_name VARCHAR(150) NULL,
ADD COLUMN mother_profession VARCHAR(150) NULL,
ADD COLUMN address_present TEXT NULL,
ADD COLUMN gender VARCHAR(30) NULL,
ADD COLUMN religion VARCHAR(50) NULL,
ADD COLUMN blood_group VARCHAR(10) NULL,
ADD COLUMN hobby TEXT NULL,
ADD COLUMN specialty TEXT NULL,
ADD COLUMN marital_status VARCHAR(30) NULL,
ADD COLUMN occupation VARCHAR(150) NULL,
ADD COLUMN admission_id CHAR(6) NULL;

CREATE INDEX idx_trainee_name ON trainee_applications (name);
CREATE INDEX idx_trainee_email ON trainee_applications (email);

-- Ensure required base columns exist (no-op if already created in 0003)
-- ALTER TABLE trainee_applications ADD COLUMN training_for ENUM('self','other') NULL;
-- ALTER TABLE trainee_applications ADD COLUMN trainee_type ENUM('junior','senior') NULL;
-- ALTER TABLE trainee_applications ADD COLUMN bgf_id VARCHAR(50) NULL;
