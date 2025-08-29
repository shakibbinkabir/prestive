<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

use App\Core\DB;
use App\Models\User;

function seedEnums(): void
{
    echo "Seeding enum tables...\n";
    
    // Membership Types
    $membershipTypes = [
        ['slug' => 'permanent', 'label' => 'Permanent Membership', 'sort_order' => 1],
        ['slug' => 'life', 'label' => 'Life Membership', 'sort_order' => 2],
        ['slug' => 'corporate', 'label' => 'Corporate Membership', 'sort_order' => 3],
        ['slug' => 'honorary', 'label' => 'Honorary Membership', 'sort_order' => 4],
        ['slug' => 'general', 'label' => 'General Membership', 'sort_order' => 5],
        ['slug' => 'foreign', 'label' => 'Foreign Membership', 'sort_order' => 6],
    ];
    
    foreach ($membershipTypes as $type) {
        DB::query("INSERT IGNORE INTO membership_types (slug, label, sort_order, active) VALUES (?, ?, ?, 1)", 
            [$type['slug'], $type['label'], $type['sort_order']]);
    }
    
    // Genders
    $genders = [
        ['slug' => 'male', 'label' => 'Male', 'sort_order' => 1],
        ['slug' => 'female', 'label' => 'Female', 'sort_order' => 2],
        ['slug' => 'other', 'label' => 'Other/Prefer not to say', 'sort_order' => 3],
    ];
    
    foreach ($genders as $gender) {
        DB::query("INSERT IGNORE INTO genders (slug, label, sort_order, active) VALUES (?, ?, ?, 1)", 
            [$gender['slug'], $gender['label'], $gender['sort_order']]);
    }
    
    // Religions
    $religions = [
        ['slug' => 'islam', 'label' => 'Islam', 'sort_order' => 1],
        ['slug' => 'hindu', 'label' => 'Hindu', 'sort_order' => 2],
        ['slug' => 'christian', 'label' => 'Christian', 'sort_order' => 3],
        ['slug' => 'buddhist', 'label' => 'Buddhist', 'sort_order' => 4],
        ['slug' => 'others', 'label' => 'Others', 'sort_order' => 5],
    ];
    
    foreach ($religions as $religion) {
        DB::query("INSERT IGNORE INTO religions (slug, label, sort_order, active) VALUES (?, ?, ?, 1)", 
            [$religion['slug'], $religion['label'], $religion['sort_order']]);
    }
    
    // Marital Statuses
    $maritalStatuses = [
        ['slug' => 'single', 'label' => 'Single', 'sort_order' => 1],
        ['slug' => 'married', 'label' => 'Married', 'sort_order' => 2],
        ['slug' => 'separated', 'label' => 'Separated', 'sort_order' => 3],
        ['slug' => 'divorced', 'label' => 'Divorced', 'sort_order' => 4],
        ['slug' => 'widowed', 'label' => 'Widowed', 'sort_order' => 5],
    ];
    
    foreach ($maritalStatuses as $status) {
        DB::query("INSERT IGNORE INTO marital_statuses (slug, label, sort_order, active) VALUES (?, ?, ?, 1)", 
            [$status['slug'], $status['label'], $status['sort_order']]);
    }
    
    // Blood Groups
    $bloodGroups = [
        ['slug' => 'a_positive', 'label' => 'A+', 'sort_order' => 1],
        ['slug' => 'a_negative', 'label' => 'A-', 'sort_order' => 2],
        ['slug' => 'b_positive', 'label' => 'B+', 'sort_order' => 3],
        ['slug' => 'b_negative', 'label' => 'B-', 'sort_order' => 4],
        ['slug' => 'o_positive', 'label' => 'O+', 'sort_order' => 5],
        ['slug' => 'o_negative', 'label' => 'O-', 'sort_order' => 6],
        ['slug' => 'ab_positive', 'label' => 'AB+', 'sort_order' => 7],
        ['slug' => 'ab_negative', 'label' => 'AB-', 'sort_order' => 8],
    ];
    
    foreach ($bloodGroups as $group) {
        DB::query("INSERT IGNORE INTO blood_groups (slug, label, sort_order, active) VALUES (?, ?, ?, 1)", 
            [$group['slug'], $group['label'], $group['sort_order']]);
    }
    
    echo "✓ Enum tables seeded successfully\n";
}

function seedAdminUser(): void
{
    echo "Creating admin user...\n";
    
    // Check if admin user already exists
    $existing = User::findByEmail(ADMIN_EMAIL);
    if ($existing) {
        echo "✓ Admin user already exists: " . ADMIN_EMAIL . "\n";
        return;
    }
    
    // Create admin user
    $userData = [
        'name' => 'Administrator',
        'email' => ADMIN_EMAIL,
        'password_hash' => password_hash(ADMIN_PASSWORD, PASSWORD_BCRYPT),
        'is_admin' => 1,
    ];
    
    $userId = User::create($userData);
    echo "✓ Admin user created successfully: " . ADMIN_EMAIL . " (ID: $userId)\n";
    echo "  Default password: " . ADMIN_PASSWORD . "\n";
    echo "  Please change the password after first login!\n";
}

// Run seeding
try {
    seedEnums();
    seedAdminUser();
    echo "\nSeeding completed successfully!\n";
} catch (Exception $e) {
    echo "Seeding error: " . $e->getMessage() . "\n";
    exit(1);
}