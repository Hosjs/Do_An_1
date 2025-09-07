<?php
// database/migrations/xxxx_add_title_description_to_tests_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tests', function (Blueprint $table) {
            if (!Schema::hasColumn('tests', 'title')) {
                $table->string('title', 255)->nullable()->after('id');
            }
            if (!Schema::hasColumn('tests', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
        });
    }
    public function down(): void {
        Schema::table('tests', function (Blueprint $table) {
            if (Schema::hasColumn('tests', 'description')) $table->dropColumn('description');
            if (Schema::hasColumn('tests', 'title')) $table->dropColumn('title');
        });
    }
};
