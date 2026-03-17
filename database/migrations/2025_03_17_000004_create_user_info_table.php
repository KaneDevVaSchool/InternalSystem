<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->unsignedTinyInteger('gender')->default(1)->comment('1: Nam, 0: Nữ');
            $table->dateTime('birthdate')->nullable();
            $table->string('birth_place')->nullable();
            $table->string('national')->nullable();
            $table->string('religion')->nullable();
            $table->string('hometown')->nullable();
            $table->string('identity')->nullable()->comment('CCCD/CMND');
            $table->timestamp('identity_date')->nullable();
            $table->string('identity_place')->nullable();
            $table->string('tax_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('household')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank')->nullable();
            $table->timestamp('start_working_date')->nullable();
            $table->string('working_place')->nullable();
            $table->string('note')->nullable();
            $table->string('company_name')->nullable();
            $table->string('department_name')->nullable();
            $table->string('unit_name')->nullable();
            $table->string('headquarter_name')->nullable();
            $table->string('position_name')->nullable();
            $table->string('concurrent_position_name')->nullable();
            $table->unsignedBigInteger('department_id')->nullable()->index();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_info');
    }
};
