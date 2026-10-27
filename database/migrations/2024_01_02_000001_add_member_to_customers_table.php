<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('is_member')->default(false)->after('notes');
            $table->decimal('discount_percent', 5, 2)->default(0)->after('is_member');
            $table->date('member_since')->nullable()->after('discount_percent');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['is_member', 'discount_percent', 'member_since']);
        });
    }
};