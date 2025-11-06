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
        Schema::table('approval_flows', function (Blueprint $table) {
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete()
                ->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_flows', function (Blueprint $table) {
            // $table->dropForeign(['department_id']);
            // $table->dropColumn('department_id');
            $table->dropConstrainedForeignId('department_id');

        });
    }
};
