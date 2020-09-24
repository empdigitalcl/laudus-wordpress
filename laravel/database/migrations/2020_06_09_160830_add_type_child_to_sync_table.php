<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeChildToSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sync', function (Blueprint $table) {
            $table->string('woocType',50)->nullable()->comment('tipo de producto woocommerce')->after('session'); 
            $table->string('woocParentId',15)->nullable()->comment('id del padre del producto woocommerce')->after('woocType'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sync', function (Blueprint $table) {
            $table->dropColumn('woocType');   
            $table->dropColumn('woocParentId');   
        });
    }
}
