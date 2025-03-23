<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSavingsAccountIdToGroupMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_members', function (Blueprint $table) {
            if (!Schema::hasColumn('group_members', 'savings_account_id')) {
            $table->unsignedBigInteger('savings_account_id')->nullable()->after('member_id');
            $table->foreign('savings_account_id')
                ->references('id')->on('savings_accounts')
                ->onDelete('cascade');
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
        Schema::table('group_members', function (Blueprint $table) {
            if (Schema::hasColumn('group_members', 'savings_account_id')) {
                Schema::dropForeign('savings_account_id');
                $table->dropColumn('savings_account_id');
            }
        });
    }
}
