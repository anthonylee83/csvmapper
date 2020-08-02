<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InitialDbSetup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE contacts (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `team_id` int(10) unsigned NOT NULL,
            `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `sticky_phone_number_id` int(11) DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE = InnoDB AUTO_INCREMENT = 256
          DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci"
        );

        DB::statement(
            "CREATE TABLE custom_attributes (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `contact_id` bigint(20) unsigned NOT NULL,
            `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE = InnoDB AUTO_INCREMENT = 256
          DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('custom_attributes');
    }
}
