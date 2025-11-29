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
        Schema::table('donations', function (Blueprint $table) {
            $table->string('transfer_receipt')->after('amount');
            $table->enum('method', ['bank', 'cash'])->after('transfer_receipt');
            $table->text('cash_description')->nullable()->after('method');
            $table->boolean('anonymous')->default(false)->after('cash_description');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('anonymous');
            $table->foreignId('bank_account_id')->nullable()->after('status')->constrained()->nullOnDelete();
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });

        if (Schema::hasColumn('donations', 'is_anonymous')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn('is_anonymous');
            });
        }

        if (Schema::hasColumn('donations', 'payment_method')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn('payment_method');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            if (! Schema::hasColumn('donations', 'is_anonymous')) {
                $table->boolean('is_anonymous')->default(false)->after('amount');
            }

            $table->dropForeign(['bank_account_id']);
            $table->dropColumn([
                'transfer_receipt',
                'method',
                'cash_description',
                'anonymous',
                'status',
                'bank_account_id',
                'updated_at',
            ]);

            if (! Schema::hasColumn('donations', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('donor_name');
            }
        });
    }
};
