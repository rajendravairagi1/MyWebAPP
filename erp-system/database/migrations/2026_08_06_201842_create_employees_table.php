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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('mobile')->nullable();
            $table->string('aadhaar_number')->nullable();
            $table->string('pan_number')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->decimal('per_day_salary', 10, 2)->default(0);
            $table->string('bank_account_number')->nullable();
            $table->string('bank_ifsc')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
