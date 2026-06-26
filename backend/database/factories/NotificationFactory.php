<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

   public function definition(): array
{
    return [
        'id' => fake()->uuid(),
        'type' => 'App\Notifications\DatabaseNotification',
        'notifiable_type' => \App\Models\User::class,
        'notifiable_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        // On force l'encodage JSON ici pour court-circuiter le problème de cast
        'data' => json_encode([
            'title' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'type' => fake()->randomElement(['system', 'validation', 'publication', 'information']),
        ]),
        'read_at' => fake()->boolean(30) ? now() : null,
    ];
}
}