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
        Schema::create('w9_forms', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->unsignedBigInteger('affiliate_id')->nullable();
            $table->unsignedBigInteger('entertainer_id')->nullable();
            $table->enum('type', ['affiliate', 'entertainer']); // To track which type

            // W-9 Form Fields (Official IRS)
            $table->string('full_name'); // Name (as shown on your income tax return)
            $table->string('business_name')->nullable(); // Business name/DBA
            $table->enum('tax_classification', [
                'individual',
                'c_corporation',
                's_corporation',
                'partnership',
                'trust_estate',
                'limited_liability_company_c',
                'limited_liability_company_s',
                'limited_liability_company_individual',
                'sole_proprietor',
                'other'
            ])->default('individual');
            $table->string('tax_classification_other')->nullable(); // If "other" is selected

            // Tax ID
            $table->string('tax_id_type'); // 'ssn' or 'ein'
            $table->string('tax_id_number'); // SSN or EIN (encrypted)

            // Address
            $table->string('street_address');
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip_code', 10);

            // Optional Fields
            $table->string('account_numbers')->nullable(); // Account number(s) for records (optional)
            $table->text('requester_name')->nullable(); // Name of person/organization requesting
            $table->string('requester_phone')->nullable();
            $table->string('requester_email')->nullable();

            // Certification
            $table->boolean('certification_signed') // Checkbox: I declare under penalty of perjury
                ->default(false);
            $table->timestamp('certification_date')->nullable();
            $table->string('certification_ip')->nullable(); // IP address of certification

            // Exempt Payee & FATCA (optional)
            $table->string('exempt_payee_code')->nullable(); // Exempt payee code (optional)
            $table->string('fatca_exemption_code')->nullable(); // Exemption from FATCA (optional)

            // ID Document Uploads
            $table->string('id_front_image')->nullable(); // ID front image path
            $table->string('id_back_image')->nullable(); // ID back image path
            $table->string('id_document_type')->nullable(); // Type: 'driver_license', 'passport', etc.

            // Status & Review
            $table->enum('status', ['pending', 'approved', 'rejected', 'submitted'])->default('pending');
            $table->text('admin_notes')->nullable(); // Admin notes for rejection or comments
            $table->unsignedBigInteger('reviewed_by')->nullable(); // Admin user ID who reviewed
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->foreign('affiliate_id')->references('id')->on('affiliates')->onDelete('cascade');
            $table->foreign('entertainer_id')->references('id')->on('entertainers')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('w9_forms');
    }
};
