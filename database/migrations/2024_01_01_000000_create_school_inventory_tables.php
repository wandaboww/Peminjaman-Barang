<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Users Implementation (Teachers, Students, Staff)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identity_number')->unique(); // NIP or NIS
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Assets / Inventory Items
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('brand'); // e.g. Acer
            $table->string('model'); // e.g. Aspire 5
            $table->string('serial_number')->unique();
            $table->enum('category', ['laptop', 'infocus', 'peripheral', 'other']);
            
            // Condition tracking
            $table->enum('condition', ['good', 'minor_damage', 'major_damage', 'under_repair'])->default('good');
            
            // Availability logic
            $table->enum('status', ['available', 'borrowed', 'maintenance', 'lost'])->default('available');
            
            $table->string('qr_code_hash')->unique(); // String for QR Generation
            $table->json('specifications')->nullable(); // Flexible specs
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Transactions (Loans)
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users'); // Who approved/scanned it
            
            // Time tracking
            $table->dateTime('loan_date');
            $table->dateTime('due_date'); // Calculated based on user role
            $table->dateTime('return_date')->nullable();
            
            // Status
            $table->enum('status', ['active', 'returned', 'overdue', 'lost'])->default('active');
            
            // Evidence & Validation
            $table->string('pickup_photo_path')->nullable(); // Proof condition
            $table->string('digital_signature_path')->nullable(); // User signature
            
            // Return process
            $table->json('return_checklist')->nullable(); // e.g. {mouse: true, bag: false}
            $table->text('return_notes')->nullable(); // Fines or damage report
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('users');
    }
};
