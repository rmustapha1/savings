<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMemberLimitAndTargetAmountToGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->integer('member_limit')->nullable()->after('group_name')->comment('Maximum number of members allowed in the group');
            $table->decimal('target_amount', 15, 2)->nullable()->after('member_limit')->comment('Target amount for group savings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['member_limit', 'target_amount']);
        });
    }
}
