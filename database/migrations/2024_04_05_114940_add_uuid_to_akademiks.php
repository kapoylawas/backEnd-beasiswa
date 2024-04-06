<?php

use App\Models\Akademik;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('akademiks', function (Blueprint $table) {
            $table->uuid('uuid')->index()->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('akademiks', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
