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
        Schema::create('approval_flows', function (Blueprint $table) {
            $table->id();
            $table->string('module', 50); // 'leave', 'expense', etc.
            $table->string('role', 50);   // vai trò người nộp đơn (staff, manager...)
            $table->unsignedInteger('level'); // cấp duyệt (1,2,3,...)
            $table->unsignedBigInteger('approver_role_id'); // role_id của người duyệt
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approva_flows');
    }
};
