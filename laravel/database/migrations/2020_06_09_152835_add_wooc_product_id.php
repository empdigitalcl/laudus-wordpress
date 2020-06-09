<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWoocProductId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sync', function (Blueprint $table) {
            $table->string('woocProductId',15)->nullable()->comment('Id de producto en woocommerce')->after('status'); 
            $table->string('laudusProductId',15)->nullable()->comment('Id de producto en laudus')->after('woocProductId'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('sync', function (Blueprint $table) {
            $table->dropColumn('woocProductId');   
            $table->dropColumn('laudusProductId');   
        });
    }
}
