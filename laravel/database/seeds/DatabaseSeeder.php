<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Person;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Schema::enableForeignKeyConstraints();
    }
}
