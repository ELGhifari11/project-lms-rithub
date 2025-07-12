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
        // User::factory(100)->create();

        //  //TODO hapus klo udh production
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

        echo "Seeding data...";
        echo "\n";
        echo "Clearing data...";
        echo "\n";
        User::factory(25)->create();
        echo "User created";
        echo "\n";
        Category::factory(3)->create();
        echo "Category created";
        echo "\n";
        $this->call([
            SubCategorySeeder::class,
        ]);
        echo "SubCategory created";
        echo "\n";
        ClassModel::factory(500)->create();
        echo "Class created";
        echo "\n";
        ModuleOfCourse::factory(1000)->create();
        echo "ModuleOfCourse created";
        echo "\n";
        Bundle::factory(5)->create();
        echo "Bundle created";
        echo "\n";
        BundleItem::factory(50)->create();
        echo "BundleItem created";
        echo "\n";
        Order::factory(50)->create();
        echo "Order created";
        echo "\n";
        OrderItem::factory(50)->create();
        echo "OrderItem created";
        echo "\n";
        UserSubscription::factory(25)->create();
        echo "UserSubscription created";
        echo "\n";
        Enrollment::factory(1500)->create();
        echo "Enrollment created";
        echo "\n";
        EducationalContent::factory(1000)->create();
        echo "EducationalContent created";
        echo "\n";
        WebinarRecording::factory(100)->create();
        echo "WebinarRecording created";
        echo "\n";
        Milestone::factory(100)->create();
        echo "Milestone created";
        echo "\n";
        UserMilestone::factory(25)->create();
        echo "UserMilestone created";
        echo "\n";
        Point::factory(100)->create();
        echo "Point created";
        echo "\n";
        Badge::factory(100)->create();
        echo "Badge created";
        echo "\n";
        UserBadge::factory(100)->create();
        echo "UserBadge created";
        echo "\n";
        Certificate::factory(100)->create();
        echo "Certificate created";
        echo "\n";
        Promo::factory(100)->create();
        echo "Promo created";
        echo "\n";
        PromoUsage::factory(100)->create();
        echo "PromoUsage created";
        echo "\n";
        Feedback::factory(350)->create();
        echo "Feedback created";
        echo "\n";
        SupportTicket::factory(50)->create();
        echo "SupportTicket created";
        echo "\n";
        TicketResponse::factory(50)->create();
        echo "TicketResponse created";
        echo "\n";
        Event::factory(25)->create();
        echo "Event created";
        echo "\n";
        EventAttendee::factory(100)->create();
        echo "EventAttendee created";
        echo "\n";
        Banner::factory(50)->create();
        echo "Banner created";
        echo "\n";
        Follow::factory(25)->create();
        echo "Follow created";
        echo "\n";
        Bookmark::factory(25)->create();
        echo "Bookmark created";
        echo "\n";
        // UserClassContent::factory(100)->create();
        // echo "UserClassContent created";
        // echo "\n";
        Wallet::factory(25)->create();
        echo "Wallet created";
        echo "\n";
        CommissionSetting::factory(10)->create();
        $itemType = [ClassModel::class, WebinarRecording::class, Bundle::class, User::class];
        $intervals = ['monthly', 'annually', 'lifetime'];
        foreach ($itemType as $type) {
            foreach ($intervals as $interval) {
                CommissionSetting::factory()->create([
                    'item_type' => $type,
                    'interval' => $interval,
                    'is_percentage' => fake()->boolean(),
                    'is_active' => true,
                    'fixed_commission' => fake()->numberBetween(1, 100),
                    'platform_share' => fake()->numberBetween(1, 10),
                ]);
            }
        }
        echo "CommissionSetting created";
        echo "\n";
        CommissionEarning::factory(100)->create();
        echo "CommissionEarning created";
        echo "\n";
        Withdrawal::factory(100)->create();
        echo "Withdrawal created";
        echo "\n";
    }
}
