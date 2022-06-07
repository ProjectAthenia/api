<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Cusco extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merged_to_id')->nullable();
            $table->foreign('merged_to_id')->references('id')->on('users');
            $table->string('stripe_customer_key', 120)->nullable();
            $table->string('email', 120)->unique();
            $table->string('name', 120);
            $table->string('password', 255);
            $table->timestamps();
            $table->softDeletes();
        });
        $roles = [
            ['id' => \App\Models\Role::APP_USER, 'name' => 'A Basic App User'],
            ['id' => \App\Models\Role::SUPER_ADMIN, 'name' => 'A Super Admin'],
            ['id' => \App\Models\Role::ARTICLE_VIEWER, 'name' => 'An Article Viewer'],
            ['id' => \App\Models\Role::ARTICLE_EDITOR, 'name' => 'An Article Editor'],
        ];

        DB::table('roles')->insert($roles);

        Schema::create('role_user', function(Blueprint $table)
        {
            $table->integer('role_id')->unsigned()->index('role_user_role_id_foreign');
            $table->integer('user_id')->unsigned()->index('role_user_user_id_foreign');
            $table->timestamps();
        });
        Schema::create('password_tokens', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('token', 40);

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('ballots', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 128)->nullable();
            $table->string('type');

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('ballot_subjects', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('ballot_id');
            $table->foreign('ballot_id')->references('id')->on('ballots');

            $table->unsignedInteger('subject_id');
            $table->string('subject_type');

            $table->integer('votes_cast')->default(0);
            $table->integer('vote_count')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('ballot_completions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('ballot_id');
            $table->foreign('ballot_id')->references('id')->on('ballots');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('votes', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('ballot_subject_id');
            $table->foreign('ballot_subject_id')->references('id')->on('ballot_subjects');

            $table->unsignedInteger('ballot_completion_id');
            $table->foreign('ballot_completion_id')->references('id')->on('ballot_completions');

            $table->integer('result')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 40);

            $table->string('duration', 20);

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('membership_plan_rates', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('membership_plan_id');
            $table->foreign('membership_plan_id')->references('id')->on('membership_plans');

            $table->float('cost');

            $table->boolean('active');

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('owner_id');
            $table->string('owner_type', 20)->default('user');

            $table->string('payment_method_key', 120)->nullable();
            $table->string('payment_method_type', 20);
            $table->string('identifier', 20)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('membership_plan_rate_id');
            $table->foreign('membership_plan_rate_id')->references('id')->on('membership_plan_rates');

            $table->unsignedInteger('payment_method_id');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');

            $table->unsignedInteger('subscriber_id');
            $table->string('subscriber_type', 20)->default('user');

            $table->timestamp('last_renewed_at')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('canceled_at')->nullable();

            $table->boolean('recurring')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('payment_method_id');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');

            $table->unsignedInteger('subscription_id')->nullable();
            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->float('amount');

            $table->string('transaction_key', 120)->nullable();

            $table->dateTime('refunded_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('created_by_id');
            $table->foreign('created_by_id')->references('id')->on('users');

            $table->string('title', 120);

            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('iterations', function (Blueprint $table) {
            $table->increments('id');

            $table->text('content');

            $table->unsignedInteger('created_by_id');
            $table->foreign('created_by_id')->references('id')->on('users');

            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('articles');

            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('article_versions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('article_id');
            $table->foreign('article_id')->references('id')->on('articles');

            $table->unsignedInteger('iteration_id');
            $table->foreign('iteration_id')->references('id')->on('iterations');

            $table->string('name', 20)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        /** @var \App\Models\Wiki\Article $article */
        foreach (\App\Models\Wiki\Article::all() as $article) {
            /** @var \App\Models\Wiki\ArticleIteration $iteration */
            $iteration = $article->iterations()->limit(1)->get();

            $version = new \App\Models\Wiki\ArticleVersion([
                'iteration_id' => $iteration->id,
                'article_id' => $article->id,
                'name' => '1.0.0',
            ]);
            $version->save();
        }

        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');

            $table->text('name')->nullable();
            $table->text('caption')->nullable();
            $table->string('url', 120);

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('threads', function (Blueprint $table) {
            $table->increments('id');

            $table->string('topic', 120)->nullable();
            $table->unsignedInteger('subject_id')->nullable();
            $table->string('subject_type', 20)->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('thread_user', function(Blueprint $table) {
            $table->unsignedInteger('thread_id');
            $table->foreign('thread_id')->references('id')->on('threads');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->primary(['thread_id', 'user_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');

            $table->string('email', 120)->nullable();
            $table->string('subject', 256)->nullable();
            $table->string('template', 32)->nullable();
            $table->json('data');

            $table->unsignedInteger('to_id')->nullable();
            $table->foreign('to_id')->references('id')->on('users');

            $table->unsignedInteger('from_id')->nullable();
            $table->foreign('from_id')->references('id')->on('users');

            $table->unsignedInteger('thread_id')->nullable();
            $table->foreign('thread_id')->references('id')->on('threads');


            $table->json('via')->nullable();
            $table->string('action', 128)->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->dateTime('seen_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('resources', function(Blueprint $table) {
            $table->increments('id');

            $table->text('content');

            $table->unsignedInteger('resource_id');
            $table->string('resource_type', 20);

            $table->softDeletes();
            $table->timestamps();
        });


        Schema::create('contacts', function(Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('initiated_by_id');
            $table->foreign('initiated_by_id')->references('id')->on('users');

            $table->unsignedInteger('requested_id');
            $table->foreign('requested_id')->references('id')->on('users');

            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('denied_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('resources');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('membership_plan_rates');
        Schema::dropIfExists('membership_plans');
        Schema::dropIfExists('votes');
        Schema::dropIfExists('ballot_completions');
        Schema::dropIfExists('ballot_subjects');
        Schema::dropIfExists('ballots');
        Schema::dropIfExists('article_versions');
        Schema::dropIfExists('iterations');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('thread_user');
        Schema::dropIfExists('threads');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('password_tokens');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
}
