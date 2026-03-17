<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /** @var array<string, string> */
    private array $tableNames = [
        'permissions' => 'permissions',
        'roles' => 'roles',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ];

    private string $modelMorphKey = 'model_id';

    public function up(): void
    {
        Schema::create($this->tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create($this->tableNames['roles'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create($this->tableNames['model_has_permissions'], function (Blueprint $table) {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);
            $table->string('model_type');
            $table->unsignedBigInteger($this->modelMorphKey);
            $table->index([$this->modelMorphKey, 'model_type']);
            $table->foreign(PermissionRegistrar::$pivotPermission)
                ->references('id')
                ->on($this->tableNames['permissions'])
                ->onDelete('cascade');
            $table->primary([PermissionRegistrar::$pivotPermission, $this->modelMorphKey, 'model_type']);
        });

        Schema::create($this->tableNames['model_has_roles'], function (Blueprint $table) {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);
            $table->string('model_type');
            $table->unsignedBigInteger($this->modelMorphKey);
            $table->index([$this->modelMorphKey, 'model_type']);
            $table->foreign(PermissionRegistrar::$pivotRole)
                ->references('id')
                ->on($this->tableNames['roles'])
                ->onDelete('cascade');
            $table->primary([PermissionRegistrar::$pivotRole, $this->modelMorphKey, 'model_type']);
        });

        Schema::create($this->tableNames['role_has_permissions'], function (Blueprint $table) {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);
            $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);
            $table->foreign(PermissionRegistrar::$pivotPermission)
                ->references('id')
                ->on($this->tableNames['permissions'])
                ->onDelete('cascade');
            $table->foreign(PermissionRegistrar::$pivotRole)
                ->references('id')
                ->on($this->tableNames['roles'])
                ->onDelete('cascade');
            $table->primary([PermissionRegistrar::$pivotPermission, PermissionRegistrar::$pivotRole]);
        });
    }

    public function down(): void
    {
        Schema::drop($this->tableNames['role_has_permissions']);
        Schema::drop($this->tableNames['model_has_roles']);
        Schema::drop($this->tableNames['model_has_permissions']);
        Schema::drop($this->tableNames['roles']);
        Schema::drop($this->tableNames['permissions']);
    }
};
