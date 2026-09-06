<?php

use App\Enums\AssignmentStatus;
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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_offering_id')->constrained('course_offerings')->cascadeOnDelete();
            $table->string('title');
            $table->longText('description');
            $table->date('due_date');
            $table->float('max_marks');
            $table->string('file_path');
            $table->string('original_name');
            $table->enum('mime_type', ['file/pdf', 'file/docx']);
            $table->enum('status', array_column(AssignmentStatus::cases(), 'value'))->default(AssignmentStatus::HIDDEN);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
