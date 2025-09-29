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
        Schema::table('orders', function (Blueprint $table) {
            // Dispute and Complaint Management
            $table->boolean('has_complaint')->default(false)->after('notes');
            $table->text('complaint_details')->nullable()->after('has_complaint');
            $table->text('resolution_notes')->nullable()->after('complaint_details');
            $table->timestamp('dispute_resolved_at')->nullable()->after('resolution_notes');
            
            // Refund Management
            $table->enum('refund_status', [
                'none', 
                'requested', 
                'approved', 
                'processed', 
                'denied'
            ])->default('none')->after('dispute_resolved_at');
            $table->text('refund_reason')->nullable()->after('refund_status');
            $table->timestamp('refunded_at')->nullable()->after('refund_reason');
            
            // Admin Override Tracking
            $table->text('admin_notes')->nullable()->after('refunded_at');
            $table->timestamp('admin_override_at')->nullable()->after('admin_notes');
            $table->unsignedBigInteger('admin_override_by')->nullable()->after('admin_override_at');
            
            // Review and Flag System
            $table->timestamp('flagged_at')->nullable()->after('admin_override_by');
            $table->text('flag_reason')->nullable()->after('flagged_at');
            
            // Add foreign key for admin override tracking
            $table->foreign('admin_override_by')->references('id')->on('users')->onDelete('set null');
            
            // Add indexes for better query performance
            $table->index(['has_complaint', 'created_at']);
            $table->index(['refund_status', 'created_at']);
            $table->index(['flagged_at', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['admin_override_by']);
            
            // Drop indexes
            $table->dropIndex(['has_complaint', 'created_at']);
            $table->dropIndex(['refund_status', 'created_at']);
            $table->dropIndex(['flagged_at', 'created_at']);
            
            // Drop columns
            $table->dropColumn([
                'has_complaint',
                'complaint_details',
                'resolution_notes',
                'dispute_resolved_at',
                'refund_status',
                'refund_reason',
                'refunded_at',
                'admin_notes',
                'admin_override_at',
                'admin_override_by',
                'flagged_at',
                'flag_reason',
            ]);
        });
    }
};