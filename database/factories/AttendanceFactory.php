<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('-1 month', 'now');
        $checkOut = (clone $checkIn)->modify('+8 hours');
        $status = fake()->randomElement(['on_time', 'late', 'absent']);

        return [
            'user_id' => User::factory(),
            'date' => $checkIn->format('Y-m-d'),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'status' => $status,
            'total_hours' => $status === 'absent' ? 0 : 8,
        ];
    }
}
