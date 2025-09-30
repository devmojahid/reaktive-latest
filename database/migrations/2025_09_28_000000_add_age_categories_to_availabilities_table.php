<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            // JSON dacă ai MySQL 5.7+; dacă nu e sigur, folosește longText (comentariul de mai jos)
            $table->json('age_categories')->nullable()->after('per_children_price');
            // alternativ: $table->longText('age_categories')->nullable()->after('per_children_price');
        });
    }

    public function down(): void
    {
        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropColumn('age_categories');
        });
    }
};
