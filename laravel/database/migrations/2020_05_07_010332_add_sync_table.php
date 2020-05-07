<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('status')->default(1);
            $table->string('sku', 255)->nullable();
            $table->string('netPrice', 25)->nullable();
            $table->string('stockAvailable')->nullable();
            $table->string('session')->nullable();
            // auditoría
            $table->timestamp('timeStampAdd')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('Fecha creación del registro');
            $table->biginteger('userAdd')->unsigned()->nullable()->comment('Usuario que creo el registro');
            $table->string('ipAdd', 15)->nullable()->comment('IP desde que se creo el registro');
            $table->timestamp('timeStampUpd')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment('Fecha actualización');
            $table->biginteger('userUpd')->unsigned()->nullable()->comment('Usuario que actualizó el registro');
            $table->string('ipUpd', 15)->nullable()->comment('Ip desde que se actualizó el registro');
            $table->timestamp('timeStampDel')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment('Fecha eliminación del registro');
            $table->biginteger('userDel')->unsigned()->nullable()->comment('Usuario que eliminó el registro');
            $table->string('ipDel', 15)->nullable()->comment('Ip desde que se eliminó el registro');
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
    }
}
