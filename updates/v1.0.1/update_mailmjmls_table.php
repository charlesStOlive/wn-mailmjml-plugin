<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Winter\Storm\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('waka_mailmjml_mail_mjmls', function (Blueprint $table) {
            $table->text('prod_asks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('waka_mailmjml_mail_mjmls', function (Blueprint $table) {
            $table->dropColumn('prod_asks');
        });
    }
};
