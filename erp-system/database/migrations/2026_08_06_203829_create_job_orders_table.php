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
        Schema::create('job_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('challan_number')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('photo_path')->nullable();
            $table->date('received_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'working', 'ready', 'dispatched'])->default('pending');
            $table->enum('qc_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('qc_remarks')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_orders');
    }
};
