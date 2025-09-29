<?php

namespace Database\Factories;

use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServerFactory extends Factory
{
    protected $model = Server::class;

    public function definition(): array
    {
        return [
            'image' => null,
            'host' => $this->faker->domainName(),
            'ip' => $this->faker->ipv4(),
            'description' => $this->faker->text(80),
            'order' => 0,
        ];
    }
}
