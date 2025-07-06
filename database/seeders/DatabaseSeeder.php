<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Badge;
use App\Models\Event;
use App\Models\Order;
use App\Models\Point;
use App\Models\Promo;
use App\Models\Banner;
use App\Models\Bundle;
use App\Models\Follow;
use App\Models\AuditLog;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Feedback;
use App\Models\Milestone;
use App\Models\OrderItem;
use App\Models\UserBadge;
use App\Models\BundleItem;
use App\Models\ClassModel;
use App\Models\Enrollment;
use App\Models\PromoUsage;
use App\Models\Certificate;
use App\Models\CommissionEarning;
use App\Models\CommissionSetting;
use App\Models\SubCategory;
use App\Models\EventAttendee;
use App\Models\SupportTicket;
use App\Models\UserMilestone;
use App\Models\TicketResponse;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserSubscription;
use App\Models\WebinarRecording;
use App\Models\EducationalContent;
use App\Models\ModuleOfCourse;
use App\Models\UserClassContents;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "Seeding data...";
        echo "\n";
        echo "Clearing data...";
        echo "\n";
        User::factory(50)->create();
        echo "\n";
        echo "Data user seeded successfully.";
        Category::factory(3)->create();
        echo "\n";
        echo "Data category seeded successfully.";
        $this->call([
            SubCategorySeeder::class,
        ]);
        echo "\n";
        echo "Data subcategory seeded successfully.";


        if (!User::where('email', 'admin@test.com')->exists()) {
            User::factory()->create([
                'name' => 'testing',
                'email' => 'admin@test.com',
                // 'role' => 'admin',
                'password' => Hash::make('1'),
                'email_verified_at' => Carbon::now()
            ]);
        }

        if (!User::where('email', 'mentor@test.com')->exists()) {
            User::factory()->create([
                'name' => 'testing2',
                'email' => 'mentor@test.com',
                // 'role' => 'mentor',
                'password' => Hash::make('1'),
                'email_verified_at' => Carbon::now()
            ]);
        }
    }
}
