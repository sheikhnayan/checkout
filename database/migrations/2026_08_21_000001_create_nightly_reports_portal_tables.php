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
        // 1. Locations Directory
        Schema::create('nr_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id')->nullable()->index();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('type')->default('Adult with Liquor');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('timezone')->nullable()->default('America/Los_Angeles');
            $table->string('phone')->nullable();
            $table->string('dispatcher_phone')->nullable();
            $table->string('gm_name')->nullable();
            $table->string('gm_email')->nullable();
            $table->decimal('nightly_goal', 12, 2)->nullable();
            $table->decimal('break_even', 12, 2)->nullable();
            $table->decimal('historical_best', 12, 2)->nullable();
            $table->boolean('active')->default(true);
            $table->json('operating_days')->nullable();
            $table->timestamps();
        });

        // 2. User Location Allocation Pivot (Multi-Club Assignment)
        Schema::create('nr_user_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('location_id')->index();
            $table->timestamps();
            $table->unique(['user_id', 'location_id']);
        });

        // 3. Nightly Reports
        Schema::create('nr_nightly_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id')->index();
            $table->date('business_date')->index();
            $table->string('submitter_name');
            $table->string('submitter_email');
            $table->string('additional_contributor')->nullable();
            $table->decimal('net_sales', 12, 2)->default(0);
            $table->decimal('nightly_goal', 12, 2)->nullable();
            $table->decimal('last_year_net_sales', 12, 2)->nullable();
            $table->decimal('weekly_running_net_sales', 12, 2)->nullable();
            $table->decimal('day_shift_net_sales', 12, 2)->nullable();
            $table->decimal('voids', 12, 2)->nullable();
            $table->decimal('comps', 12, 2)->nullable();
            $table->decimal('dance_dollars_sold', 12, 2)->nullable();
            $table->decimal('dance_dollars_redeemed', 12, 2)->nullable();
            $table->integer('vip_rooms_sold')->nullable();
            $table->integer('total_guests')->default(0);
            $table->integer('paid_guests')->nullable();
            $table->integer('free_discount_guests')->nullable();
            $table->integer('passes_redeemed')->nullable();
            $table->decimal('guest_average', 10, 2)->nullable();
            $table->decimal('dance_average', 10, 2)->nullable();
            $table->integer('ipes')->nullable();
            $table->decimal('taxi_payout', 12, 2)->nullable();
            $table->decimal('atm_payout', 12, 2)->nullable();
            $table->decimal('other_payouts', 12, 2)->nullable();
            $table->decimal('total_payouts', 12, 2)->nullable();
            $table->decimal('deposit', 12, 2)->nullable();
            $table->decimal('safe_balance', 12, 2)->nullable();
            $table->string('weather')->nullable();
            $table->boolean('incident_flag')->default(false);
            $table->text('team_member_notes')->nullable();
            $table->text('ipe_notes')->nullable();
            $table->text('social_media_content')->nullable();
            $table->text('ordering_notes')->nullable();
            $table->text('pass_distribution_locations')->nullable();
            $table->text('night_summary')->nullable();
            $table->string('super_star_nomination')->nullable();
            $table->text('shift_comments')->nullable();
            $table->boolean('is_viewed')->default(false);
            $table->string('source')->default('web_form');
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->index(['location_id', 'business_date']);
        });

        // 4. Boutique Retail Daily Reports
        Schema::create('nr_boutique_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id')->index();
            $table->date('business_date')->index();
            $table->string('submitter_name');
            $table->string('submitter_email');
            $table->decimal('gross_daily_sales', 12, 2)->default(0);
            $table->decimal('daily_sales_goal', 12, 2)->nullable();
            $table->integer('total_guest_count')->default(0);
            $table->decimal('guest_average_ticket', 10, 2)->nullable();
            $table->integer('arcade_theater_guest_count')->nullable();
            $table->decimal('current_week_total_sales', 12, 2)->nullable();
            $table->decimal('last_year_daily_sales', 12, 2)->nullable();
            $table->integer('last_year_guest_count')->nullable();
            $table->decimal('last_year_guest_average_ticket', 10, 2)->nullable();
            $table->decimal('total_returns', 12, 2)->nullable();
            $table->decimal('total_discount', 12, 2)->nullable();
            $table->decimal('total_payouts', 12, 2)->nullable();
            $table->decimal('atm_payouts', 12, 2)->nullable();
            $table->decimal('gift_cards_sold', 12, 2)->nullable();
            $table->decimal('beginning_safe_balance', 12, 2)->nullable();
            $table->decimal('ending_safe_balance', 12, 2)->nullable();
            $table->decimal('said_deposit', 12, 2)->nullable();
            $table->decimal('actual_deposit', 12, 2)->nullable();
            $table->string('sales_direction', 10)->nullable();
            $table->text('sales_direction_reason')->nullable();
            $table->boolean('incident_flag')->default(false);
            $table->string('super_star_nomination')->nullable();
            $table->text('daytime_shift_notes')->nullable();
            $table->text('nighttime_shift_notes')->nullable();
            $table->string('weather')->nullable();
            $table->text('social_media_content')->nullable();
            $table->text('ordering_notes')->nullable();
            $table->text('passes_distributed')->nullable();
            $table->boolean('is_viewed')->default(false);
            $table->string('source')->default('web_form');
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->index(['location_id', 'business_date']);
        });

        // 5. Cash On Hand (COH) Reports
        Schema::create('nr_coh_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id')->index();
            $table->date('business_date')->index();
            $table->string('submitter_name');
            $table->string('submitter_email');
            $table->decimal('drop_safe', 12, 2)->default(0);
            $table->decimal('main_safe', 12, 2)->default(0);
            $table->decimal('register_1', 12, 2)->default(0);
            $table->decimal('register_2', 12, 2)->default(0);
            $table->decimal('register_3', 12, 2)->default(0);
            $table->decimal('register_4', 12, 2)->default(0);
            $table->decimal('atm_1', 12, 2)->default(0);
            $table->decimal('atm_2', 12, 2)->default(0);
            $table->decimal('atm_3', 12, 2)->default(0);
            $table->decimal('atm_4', 12, 2)->default(0);
            $table->decimal('other', 12, 2)->default(0);
            $table->decimal('paid_outs_total', 12, 2)->default(0);
            $table->text('paid_outs_explanation')->nullable();
            $table->decimal('vu_cash_on_hand', 12, 2)->default(0);
            $table->text('e_signature')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->index(['location_id', 'business_date']);
        });

        // 6. Security Incident Reports
        Schema::create('nr_incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id')->index();
            $table->date('incident_date')->index();
            $table->string('time_of_incident')->nullable();
            $table->string('report_type_field')->default('Other');
            $table->string('submitter_name');
            $table->string('gm_email')->nullable();
            $table->string('managers_on_duty')->nullable();
            $table->string('manager_phone')->nullable();
            $table->text('cast_members_on_duty')->nullable();
            $table->text('involved_persons')->nullable();
            $table->text('incident_description');
            $table->text('witnesses')->nullable();
            $table->string('police_report_number')->nullable();
            $table->string('police_officers_badges')->nullable();
            $table->string('police_report_file')->nullable();
            $table->text('camera_angles')->nullable();
            $table->string('camera_timestamp')->nullable();
            $table->text('additional_footage_info')->nullable();
            $table->string('additional_footage_file')->nullable();
            $table->boolean('restricted')->default(false);
            $table->string('status')->default('pending'); // pending, under_review, legal_hold, resolved
            $table->text('e_signature')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
        });

        // 7. Witness Statements
        Schema::create('nr_witness_statements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incident_id')->nullable()->index();
            $table->unsignedBigInteger('location_id')->index();
            $table->date('incident_date')->index();
            $table->string('time_of_incident')->nullable();
            $table->string('type_of_incident')->nullable();
            $table->string('witness_name');
            $table->string('witness_address')->nullable();
            $table->string('witness_phone')->nullable();
            $table->string('witness_email')->nullable();
            $table->string('witness_type')->nullable(); // Customer, Performer, Security, Staff, Bystander
            $table->text('statement_text');
            $table->string('media_attachment')->nullable();
            $table->string('submitter_email')->nullable();
            $table->text('e_signature')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
        });

        // 8. High Transactions ($10k+) AML Register
        Schema::create('nr_high_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id')->index();
            $table->date('transaction_date')->index();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('card_last4', 10);
            $table->string('card_brand')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('authorizing_manager_name');
            $table->string('id_image')->nullable();
            $table->string('card_image')->nullable();
            $table->string('receipt_image')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
        });

        // 9. Model Release Vault
        Schema::create('nr_model_releases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id')->nullable()->index();
            $table->string('performer_legal_name');
            $table->string('stage_name')->nullable();
            $table->date('date_of_birth');
            $table->string('ssn_last4', 10)->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->date('shoot_date');
            $table->string('photographer_name')->nullable();
            $table->string('id_attachment')->nullable();
            $table->string('release_pdf_attachment')->nullable();
            $table->text('digital_signature')->nullable();
            $table->boolean('age_verified')->default(true);
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
        });

        // 10. Benchmarks & Target Records
        Schema::create('nr_benchmarks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id')->unique();
            $table->decimal('historical_best', 12, 2)->nullable();
            $table->decimal('break_even', 12, 2)->nullable();
            $table->unsignedBigInteger('updated_by_user_id')->nullable();
            $table->timestamps();
        });

        // 11. Document Requests Clearance Queue
        Schema::create('nr_document_requests', function (Blueprint $table) {
            $table->id();
            $table->string('report_type'); // incident, witness
            $table->unsignedBigInteger('report_id')->index();
            $table->unsignedBigInteger('requester_id')->index();
            $table->string('requester_name');
            $table->string('requester_email');
            $table->string('requester_role')->default('ambassador');
            $table->string('requested_for');
            $table->text('requester_note')->nullable();
            $table->string('status')->default('pending'); // pending, approved, denied
            $table->timestamp('reviewed_at')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->text('reviewer_note')->nullable();
            $table->timestamps();
        });

        // 12. Legal Access Tokens
        Schema::create('nr_legal_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 80)->unique();
            $table->string('attorney_name');
            $table->string('firm_name')->nullable();
            $table->string('case_reference')->nullable();
            $table->json('location_ids')->nullable();
            $table->json('incident_ids')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('revoked')->default(false);
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
        });

        // 13. Quotes Carousel
        Schema::create('nr_quotes', function (Blueprint $table) {
            $table->id();
            $table->text('quote_text');
            $table->string('author');
            $table->string('category')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // 14. Dynamic Form Builder Configurations
        Schema::create('nr_form_configs', function (Blueprint $table) {
            $table->id();
            $table->string('report_type'); // nightly, boutique, coh, incident, witness
            $table->string('field_key');
            $table->string('label');
            $table->boolean('visible')->default(true);
            $table->boolean('required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('hint')->nullable();
            $table->timestamps();

            $table->unique(['report_type', 'field_key']);
        });

        // 15. 4-Week Rolling Revenue Reports
        Schema::create('nr_fourweek_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id')->index();
            $table->date('week_ending_date')->index();
            $table->decimal('week_1_sales', 12, 2)->default(0);
            $table->decimal('week_2_sales', 12, 2)->default(0);
            $table->decimal('week_3_sales', 12, 2)->default(0);
            $table->decimal('week_4_sales', 12, 2)->default(0);
            $table->decimal('four_week_average', 12, 2)->default(0);
            $table->decimal('trend_pct', 8, 2)->nullable();
            $table->text('variance_notes')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
        });

        // 16. Quarterly Strategic Reports
        Schema::create('nr_quarterly_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id')->index();
            $table->integer('year');
            $table->string('quarter', 10); // Q1, Q2, Q3, Q4
            $table->decimal('net_sales', 14, 2)->default(0);
            $table->integer('total_guests')->default(0);
            $table->decimal('guest_average', 10, 2)->nullable();
            $table->decimal('prior_year_sales', 14, 2)->nullable();
            $table->decimal('variance_pct', 8, 2)->nullable();
            $table->text('strategic_notes')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->unique(['location_id', 'year', 'quarter']);
        });

        // 17. Encrypted System Backups
        Schema::create('nr_backups', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('checksum')->nullable();
            $table->string('encryption_type')->default('AES-256');
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nr_backups');
        Schema::dropIfExists('nr_quarterly_reports');
        Schema::dropIfExists('nr_fourweek_reports');
        Schema::dropIfExists('nr_form_configs');
        Schema::dropIfExists('nr_quotes');
        Schema::dropIfExists('nr_legal_tokens');
        Schema::dropIfExists('nr_document_requests');
        Schema::dropIfExists('nr_benchmarks');
        Schema::dropIfExists('nr_model_releases');
        Schema::dropIfExists('nr_high_transactions');
        Schema::dropIfExists('nr_witness_statements');
        Schema::dropIfExists('nr_incidents');
        Schema::dropIfExists('nr_coh_reports');
        Schema::dropIfExists('nr_boutique_reports');
        Schema::dropIfExists('nr_nightly_reports');
        Schema::dropIfExists('nr_user_locations');
        Schema::dropIfExists('nr_locations');
    }
};
