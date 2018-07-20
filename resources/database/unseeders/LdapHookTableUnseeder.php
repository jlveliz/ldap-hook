<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LdapHookTableUnseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Skip if table does not exists.
        if (!Schema::hasTable('ldap_hook')) {
            return;
        }

        DB::table('ldap_hook')
            ->whereIn('name', ['foo', 'bar', 'baz'])
            ->delete();
    }
}
