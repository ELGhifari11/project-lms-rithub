<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 80% chance of being mentor, 20% chance of being admin
        $role = $this->faker->randomElement(['mentor', 'mentor', 'mentor', 'student', 'admin']);

        // Generate base name that will be used for username and email
        $name = $this->faker->unique()->email();
        $firstName = Str::slug(Str::before($name, ' '));
        $lastName = Str::slug(Str::after($name, ' '));

        return [
            'name' => $name,
            'username' => $firstName . $this->faker->randomNumber(3),
            'email' => $firstName . '.' . $lastName . '@' . $this->faker->freeEmailDomain(),
            'phone' => '08' . $this->faker->numberBetween(1000000000, 9999999999),
            'password' => Hash::make('1'),
            'bio' => $this->faker->paragraph(),
            'role' => $role,
            'is_verified' => $this->faker->boolean(80),
            'cover_photo_url' => $this->faker->imageUrl(1920, 1080, 'people', true, 'Faker'),
            'social_media' => [
                'facebook' => 'https://www.facebook.com/groups/343332268488155',
                'instagram' => 'https://www.instagram.com/zackdfilms/',
                'linkedin' => $this->faker->url(),
                'twitter' => $this->faker->url(),
            ],
            'preference' => function () {
                $categoryId = $this->faker->randomElement(\App\Models\Category::pluck('id')->toArray());
                $subCategory = \App\Models\SubCategory::where('category_id', $categoryId)
                    ->inRandomOrder()
                    ->first();

                // Return null if no subcategory found, otherwise return its ID
                return $subCategory ? $subCategory->id : null;
            },
            'profession' => function (array $attributes) {
                $subCategory = \App\Models\SubCategory::find($attributes['preference']);
                return $subCategory->name ?? null;
            },
            'price' => $role === 'mentor' ?
                $this->faker->numberBetween(100000, 1000000) : 0,
            'lifetime_price' => $role === 'mentor' ? function (array $attributes) {
                $price = $attributes['price'] ?? 50000;
                return $this->faker->numberBetween($price + 10000, $price + 100000);
            } : 0,
            'point' => $this->faker->numberBetween(0, 1000),
            'last_login_at' => $this->faker->optional()->dateTimeBetween('-30 days', 'now'),
            'email_verified_at' => now(),
            'created_at' => $createdAt = $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween($createdAt, 'now'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
