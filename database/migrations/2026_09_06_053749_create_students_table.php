<?php

use App\Enums\StudentStatus;
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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('student_number')->unique();
            $table->string('name');
            $table->string('email');
            $table->foreignId('program_id')->constrained('academic_programs')->cascadeOnDelete();
            $table->year('enrollment_year');
            $table->enum('status', array_column(StudentStatus::cases(), 'value'))->default(StudentStatus::NOTENROLLED);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
