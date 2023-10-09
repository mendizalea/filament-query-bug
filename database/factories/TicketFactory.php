<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        return [
            'subject'      => $this->faker->word(),
            'description'  => $this->faker->text(),
            'status'       => $this->faker->randomElement(['pending', 'wait', 'active']),
            'requested_by' => User::all()->random()->id,
            'owned_by'     => User::all()->random()->id,
            'created_at'   => Carbon::now(),
            'updated_at'   => Carbon::now(),
        ];
    }
}
