<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign('messages_to_id_foreign');
            $table->dropForeign('messages_from_id_foreign');

            $table->string('to_type')->nullable();
            $table->string('from_type')->nullable();
        });

        foreach (\App\Models\Messaging\Message::all() as $message) {
            if ($message->to_id) {
                $message->to_type = 'user';
            }
            if ($message->from_id) {
                $message->from_type = 'user';
            }

            if ($message->isDirty()) {
                $message->save();
            }
        }

        Schema::create('push_notification_keys', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('owner_id');
            $table->string('owner_type');

            $table->string('push_notification_key');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('push_notification_keys');
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('to_type');
            $table->dropColumn('from_type');
        });
    }
};
