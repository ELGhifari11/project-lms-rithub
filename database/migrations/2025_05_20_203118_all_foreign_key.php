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
        Schema::table('socialite_users', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
        });

        Schema::table('class_models', function (Blueprint $table) {
            $table->foreign('mentor_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->restrictOnDelete();
        });

        Schema::table('module_of_courses', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('class_models')->cascadeOnDelete();
        });

        Schema::table('bundle_items', function (Blueprint $table) {
            $table->foreign('bundle_id')->references('id')->on('bundles')->cascadeOnDelete();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('promo_id')->references('id')->on('promos')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('order_id')->references('id')->on('orders')->restrictOnDelete();
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('subscription_id')->references('id')->on('user_subscriptions')->nullOnDelete();
        });

        Schema::table('educational_contents', function (Blueprint $table) {
            $table->foreign('module_of_course_id')->references('id')->on('module_of_courses')->cascadeOnDelete();
        });

        Schema::table('webinar_recordings', function (Blueprint $table) {
            $table->foreign('mentor_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->restrictOnDelete();
        });

        Schema::table('milestones', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('class_models')->cascadeOnDelete();
        });

        Schema::table('user_milestones', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('milestone_id')->references('id')->on('milestones')->cascadeOnDelete();
        });

        Schema::table('points', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('user_badges', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('badge_id')->references('id')->on('badges')->restrictOnDelete();
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('class_id')->references('id')->on('class_models')->cascadeOnDelete();
        });

        Schema::table('promo_usages', function (Blueprint $table) {
            $table->foreign('promo_id')->references('id')->on('promos')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('order_id')->references('id')->on('orders')->restrictOnDelete();
        });

        Schema::table('feedbacks', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('class_id')->references('id')->on('class_models')->cascadeOnDelete();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreign('sub_category_id')->references('id')->on('sub_categories')->restrictOnDelete();
        });

        Schema::table('event_attendees', function (Blueprint $table) {
            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('bookmarks', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        Schema::table('follows', function (Blueprint $table) {
            $table->foreign('follower_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('followed_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('user_class_contents', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('class_id')->references('id')->on('class_models')->nullOnDelete();
            $table->foreign('content_id')->references('id')->on('educational_contents')->nullOnDelete();
        });

        Schema::table('commission_earnings', function (Blueprint $table) {
            $table->foreign('mentor_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });

        Schema::table('withdrawals', function (Blueprint $table) {
            $table->foreign('mentor_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('wallet_id')->references('id')->on('wallets')->restrictOnDelete();

        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->foreign('mentor_id')->references('id')->on('users')->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Buat rollback untuk semua foreign key
        $tables = [
            'sub_categories',
            'class_models',
            'orders',
            'social_accounts',
            'audit_logs',
            'support_tickets',
            'ticket_responses',
            'enrollments',
            'order_items',
            'promo_usages',
            'educational_contents',
            'webinar_recordings',
            'points',
            'user_badges',
            'milestones',
            'user_milestones',
            'feedbacks',
            'certificates',
            'event_attendees',
            'user_subscriptions',
            'bundle_items',
            'events',
            'bookmarks',
            'follows',
            'user_class_contents',
            'comission_settings',
            'commission_earnings',
            'withdrawals',
            'wallets',
            'modules_of_courses',
        ];

        // Drop all foreign key constraints for each table
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Get all foreign key constraints for the current table
                $foreignKeys = Schema::getConnection()
                    ->getDoctrineSchemaManager()
                    ->listTableForeignKeys($tableName);

                // Drop each foreign key constraint
                foreach ($foreignKeys as $foreignKey) {
                    $table->dropForeign($foreignKey->getName());
                }
            });
        }
    }
};
