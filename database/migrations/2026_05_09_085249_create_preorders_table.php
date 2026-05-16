<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('preorders', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('project_note')->nullable();
            $table->integer('total_kg');
            $table->integer('fruit_kg');
            $table->integer('puree_kg');
            $table->json('deliveries');
            $table->decimal('optional_prepayment', 10, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('submitted_at');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('preorders');
    }
};
