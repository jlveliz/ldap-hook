<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LdapHookTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Don't add data if the data is already there
        if (DB::table('ldap_hook')->count() > 0) {
            return;
        }

        DB::table('ldap_hook')->insert([
            ['name' => 'foo'],
            ['name' => 'bar'],
            ['name' => 'baz'],
        ]);
    }
}
