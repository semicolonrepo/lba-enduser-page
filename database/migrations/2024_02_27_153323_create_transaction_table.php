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
        Schema::create('voucher_transaction', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 25);
            $table->unsignedBigInteger('campaign_id');
            $table->unsignedBigInteger('product_id');
            $table->string('partner_id', 100);
            $table->string('product_name', 100);
            $table->integer('normal_price');
            $table->integer('subsidy_price');
            $table->integer('transaction_amount');
            $table->string('customer_name', 100)->nullable();
            $table->string('customer_phone', 25)->nullable();
            $table->string('customer_email', 25)->nullable();
            $table->boolean('is_auth_wa')->nullable();
            $table->boolean('is_auth_gmail')->nullable();
            $table->string('midtrans_snap_token', 255)->nullable();
            $table->string('status', 100)->default('waiting');
            $table->string('notes', 255)->nullable();
            $table->timestamp('expired_time')->nullable();
            $table->string('midtrans_status_code', 100)->nullable();
            $table->string('midtrans_status_message', 255)->nullable();
            $table->string('midtrans_payment_type', 100)->nullable();
            $table->string('midtrans_status', 100)->nullable();
            $table->timestamp('midtrans_payment_time')->nullable();
            $table->text('midtrans_response')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_transaction');
    }
};
