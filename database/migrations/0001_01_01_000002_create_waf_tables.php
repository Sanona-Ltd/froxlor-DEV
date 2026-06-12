<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waf_rules', function (Blueprint $table) {
            $table->id();
            $table->string('type');           // 'ip', 'cidr', 'useragent'
            $table->string('value');          // IP, CIDR block, or UA pattern
            $table->string('action');         // 'challenge', 'block'
            $table->boolean('active')->default(true);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index(['type', 'active']);
        });

        Schema::create('waf_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip', 45);
            $table->text('user_agent')->nullable();
            $table->string('action');         // 'passed', 'challenged', 'blocked'
            $table->string('reason')->nullable();
            $table->string('url')->nullable();
            $table->timestamp('created_at');

            $table->index(['ip', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waf_logs');
        Schema::dropIfExists('waf_rules');
    }
};
