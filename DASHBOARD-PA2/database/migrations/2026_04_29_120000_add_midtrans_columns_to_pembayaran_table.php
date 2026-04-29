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
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('midtrans_order_id', 100)->nullable()->unique()->after('status_bayar');
            $table->string('midtrans_transaction_id', 100)->nullable()->after('midtrans_order_id');
            $table->string('midtrans_transaction_status', 50)->nullable()->after('midtrans_transaction_id');
            $table->string('midtrans_payment_type', 50)->nullable()->after('midtrans_transaction_status');
            $table->string('midtrans_fraud_status', 50)->nullable()->after('midtrans_payment_type');
            $table->string('snap_token', 100)->nullable()->after('midtrans_fraud_status');
            $table->text('snap_redirect_url')->nullable()->after('snap_token');
            $table->json('midtrans_raw_response')->nullable()->after('snap_redirect_url');
            $table->timestamp('paid_at')->nullable()->after('midtrans_raw_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn([
                'midtrans_order_id',
                'midtrans_transaction_id',
                'midtrans_transaction_status',
                'midtrans_payment_type',
                'midtrans_fraud_status',
                'snap_token',
                'snap_redirect_url',
                'midtrans_raw_response',
                'paid_at',
            ]);
        });
    }
};
