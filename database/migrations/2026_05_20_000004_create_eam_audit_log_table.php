<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Obelaw\Ium\Eam\Base\MigrationBase;

return new class extends MigrationBase
{
    public function up(): void
    {
        Schema::create($this->prefix . 'audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained($this->prefix . 'assets')->cascadeOnDelete();
            $table->string('action');
            $table->string('performed_by');
            $table->json('payload')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix . 'audit_log');
    }
};
