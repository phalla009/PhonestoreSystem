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
            Schema::table('customers', function (Blueprint $table) {
                if (!Schema::hasColumn('customers', 'email')) {
                    $table->string('email')->unique()->after('phone');
                }
                if (!Schema::hasColumn('customers', 'password')) {
                    $table->string('password')->after('email');
                }
            });
        }

        public function down(): void
        {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn(['email', 'password']);
            });
        }
};
