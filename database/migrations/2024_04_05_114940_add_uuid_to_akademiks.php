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
            $table->uuid('uuid')->after('id')->nullable();

            // Generate UUID for existing records
            $akademik = Akademik::all();
            foreach ($akademik as $akademik) {
                $akademik->uuid = Uuid::uuid4()->toString();
                $akademik->save();
            }
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
