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
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_read_by_receiver')->default(false);
            $table->foreignId('parent_id')->nullable()->constrained('messages')->onDelete('cascade');
            $table->string('message_type')->default('text');
            // Drop the old is_read column as we're replacing it with is_read_by_receiver
            $table->dropColumn('is_read');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_read')->default(false);
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['is_read_by_receiver', 'parent_id', 'message_type']);
        });
    }
};
