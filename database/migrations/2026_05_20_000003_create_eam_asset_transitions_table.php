<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Obelaw\Ium\Eam\Base\MigrationBase;

return new class extends MigrationBase
{
    public function up(): void
    {
        Schema::create($this->prefix . 'asset_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained($this->prefix . 'assets')->cascadeOnDelete();
            $table->string('from_holder_type')->nullable();
            $table->unsignedBigInteger('from_holder_id')->nullable();
            $table->string('to_holder_type')->nullable();
            $table->unsignedBigInteger('to_holder_id')->nullable();
            $table->string('performed_by');
            $table->enum('condition', ['good', 'damaged', 'lost'])->nullable();
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('transitioned_at');
            $table->timestamps();

            $table->index(['from_holder_type', 'from_holder_id']);
            $table->index(['to_holder_type', 'to_holder_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->prefix . 'asset_transitions');
    }
};
