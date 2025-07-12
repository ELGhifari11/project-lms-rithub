<?php

namespace Database\Factories;

use App\Models\ClassModel;
use App\Models\EducationalContent;
use App\Models\ModuleOfCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EducationalContent>
 */
class EducationalContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = EducationalContent::class;

     public function definition(): array
    {

        $contentLinkVideo = ['https://www.youtube.com/watch?v=lTMZxWMjXQU&list=PLFIM0718LjIVknj6sgsSceMqlq242-jNf', 'https://www.youtube.com/watch?v=Q3Id0DgcrXY&list=PLFIM0718LjIVknj6sgsSceMqlq242-jNf&index=2', 'https://www.youtube.com/watch?v=k1QXd-8VbPY&list=PLFIM0718LjIVknj6sgsSceMqlq242-jNf&index=3&pp=iAQB', 'https://www.youtube.com/watch?v=8rry2ncZmfg&list=PLFIM0718LjIVknj6sgsSceMqlq242-jNf&index=4&pp=iAQB', 'https://www.youtube.com/watch?v=e-6OkXRqWaE&list=PLFIM0718LjIVknj6sgsSceMqlq242-jNf&index=5&pp=iAQB'];

        $type = $this->faker->randomElement(['video']);

        $moduleName = ['Module 1', 'Module 2', 'Module 3', 'Module 4', 'Module 5', 'Module 6', 'Module 7', 'Module 8', 'Module 9', 'Module 10'];
        return [
            'module_of_course_id' => ModuleOfCourse::inRandomOrder()->first()->id,
            'title_content' => $this->faker->sentence,
            'type' => $type,
            'order_index' => $this->faker->numberBetween(1, 10),
            'thumbnail_path' => $this->faker->url,
            'content_path' => match ($type) {
                'video' => $this->faker->randomElement($contentLinkVideo),
                'pdf'   => $this->faker->url,
            },
            'is_preview' => $this->faker->boolean(10),
            'duration' => $this->faker->numberBetween(1, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
