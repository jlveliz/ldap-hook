<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLdapHookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if(!Schema::hasColumn('users','user_type')) {
                $table->string('user_type');
            }

            if (!Schema::hasColumn('users','username')) {
                $table->string('username');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if(Schema::hasColumn('users','username') ) {
            Schema::table('users',function(Blueprint $table) {
                if (Schema::hasColumn('users','user_type')) {
                    $table->dropColumn('username');
                }

                if (Schema::hasColumn('users','username')) {
                    $table->dropColumn('user_type');
                }
               
            });
        }
    }
}
