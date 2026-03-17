<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const PIVOT_PERMISSION = 'permission_id';
    private const PIVOT_ROLE = 'role_id';
    private const MODEL_MORPH_KEY = 'model_id';

    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger(self::PIVOT_PERMISSION);
            $table->string('model_type');
            $table->unsignedBigInteger(self::MODEL_MORPH_KEY);
            $table->index([self::MODEL_MORPH_KEY, 'model_type']);
            $table->foreign(self::PIVOT_PERMISSION)
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
            $table->primary([self::PIVOT_PERMISSION, self::MODEL_MORPH_KEY, 'model_type']);
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger(self::PIVOT_ROLE);
            $table->string('model_type');
            $table->unsignedBigInteger(self::MODEL_MORPH_KEY);
            $table->index([self::MODEL_MORPH_KEY, 'model_type']);
            $table->foreign(self::PIVOT_ROLE)
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
            $table->primary([self::PIVOT_ROLE, self::MODEL_MORPH_KEY, 'model_type']);
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger(self::PIVOT_PERMISSION);
            $table->unsignedBigInteger(self::PIVOT_ROLE);
            $table->foreign(self::PIVOT_PERMISSION)
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');
            $table->foreign(self::PIVOT_ROLE)
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
            $table->primary([self::PIVOT_PERMISSION, self::PIVOT_ROLE]);
        });
    }

    public function down(): void
    {
        Schema::drop('role_has_permissions');
        Schema::drop('model_has_roles');
        Schema::drop('model_has_permissions');
        Schema::drop('roles');
        Schema::drop('permissions');
    }
};
