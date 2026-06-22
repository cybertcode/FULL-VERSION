<?php

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
        Schema::table('perfiles', function (Blueprint $table) {
            $table->dropIndex(['regimen_laboral']);
            $table->dropColumn('regimen_laboral');
        });
    }

    public function down(): void
    {
        Schema::table('perfiles', function (Blueprint $table) {
            $table->string('regimen_laboral', 60)->nullable()->after('area');
            $table->index('regimen_laboral');
        });
    }
};
