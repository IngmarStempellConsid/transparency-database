<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{

    private $social_media_names = [
        'Facebook',
        'WhatsApp',
        'Instagram',
        'YouTube',
        'Twitter',
        'LinkedIn',
        'Messenger',
        'Snapchat',
        'Telegram',
        'Quora',
        'Medium',
        'WeChat',
        'Hike',
        'Viber',
        'Pinterest',
        'Line',
        'Tumblr',
        'Planoly',
        'Tiktok',
        'Reddit',
        'Discord'
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement($this->social_media_names),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'eu_login_username' => $this->faker->unique()->words(2,true),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
