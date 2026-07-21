<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_code' => 'NVP-' . strtoupper(Str::random(10)),
            'status' => 'pending',
            'payment_method' => 'transfer',
            'payment_status' => 'unpaid',
            'subtotal' => fake()->randomFloat(2, 100, 1000),
            'discount_amount' => 0,
            'shipping_fee' => fake()->randomFloat(2, 10, 50),
            'total_amount' => fake()->randomFloat(2, 100, 1000),
            'shipping_full_name' => fake()->name(),
            'shipping_phone' => fake()->phoneNumber(),
            'shipping_address' => fake()->address(),
            'shipping_ward' => 'Ward 1',
            'shipping_district' => 'District 1',
            'shipping_province' => 'Province 1',
        ];
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function confirmed(): static
    {
        return $this->state(['status' => 'confirmed']);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }
}
