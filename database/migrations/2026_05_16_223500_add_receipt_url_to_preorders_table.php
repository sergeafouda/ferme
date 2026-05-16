<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('preorders', function (Blueprint $table) {
            $table->string('receipt_url', 1000)->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('preorders', function (Blueprint $table) {
            $table->dropColumn('receipt_url');
        });
    }
};
