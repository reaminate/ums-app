<?php

use App\Enums\AcademicStatus;
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
        Schema::create('academic_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->integer('qualification_level')->unsigned();
            $table->integer('duration')->unsigned();
            $table->integer('required_credits')->unsigned();
            $table->enum('status', array_column(AcademicStatus::cases(), 'value'))->default(AcademicStatus::GOOD_STANDING->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_programs');
    }
};
