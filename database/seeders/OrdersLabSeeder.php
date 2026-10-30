<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class OrdersLabSeeder extends Seeder
{

    private const CUSTOMER_COUNT = 2000;
    private const ORDER_COUNT = 50000;
    private const BATCH_SIZE = 1000;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        mt_srand(42);

        Schema::disableForeignKeyConstraints();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('customers')->truncate();
        Schema::enableForeignKeyConstraints();

        $now = now()->toDateTimeString();
        $cities = ['Northvale', 'Eastmere', 'Westbridge', 'Southport', 'Hillcrest', 'Riverton', 'Oakford', 'Lakeside'];
        $statuses = ['pending', 'processing', 'shipped', 'shipped', 'delivered', 'delivered', 'delivered', 'cancelled'];
        $products = [
            ['Demo Desk Lamp', 2499],
            ['Demo Notebook Set', 899],
            ['Demo Wireless Mouse', 1999],
            ['Demo Ceramic Mug', 750],
            ['Demo Backpack', 4999],
            ['Demo Water Bottle', 1299],
            ['Demo Phone Stand', 1099],
            ['Demo Desk Mat', 2299],
        ];
        $customers = [];
        for ($i = 1; $i <= self::CUSTOMER_COUNT; $i++) {
            $customers[] = [
                'id'=> $i,
                'name' => "Demo Customer {$i}",
                'email'=> "customer{$i}@example.com",
                'city'=> $cities[$i % count($cities)],
                'created_at'=> $now,
                'updated_at'=>$now,
            ];
        }

        foreach(array_chunk($customers, 500) as $chunk) {
            DB::table('customers')->insert($chunk);
        }

    
        $orders = [];
        $items = [];
        $yearInSeconds = 365 * 24 * 60* 60;

        for ($orderId = 1; $orderId <= self::ORDER_COUNT; $orderId++) {
            $total = 0;
            $lineCount = mt_rand(1, 5);

            for ($line = 0; $line < $lineCount; $line++) {
                [$productName, $unitPrice] = $products[mt_rand(0, count($products) - 1)];
                $quantity = mt_rand(1, 4);
                $total += $quantity * $unitPrice;

                $items[] = [
                    'order_id' => $orderId,
                    'product_name' => $productName,
                    'quantity' => $quantity,
                    'unit_price_cents' => $unitPrice,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $orders[] = [
                'id' => $orderId,
                'customer_id' => mt_rand(1, self::CUSTOMER_COUNT),
                'status' => $statuses[mt_rand(0, count($statuses) - 1)],
                'total_cents' => $total,
                'placed_at' => now()->subSeconds(mt_rand(0, $yearInSeconds))->toDateTimeString(),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($orderId % self::BATCH_SIZE === 0) {
                DB::table('orders')->insert($orders);
                foreach(array_chunk($items, 1000) as $chunk) {
                    DB::table('order_items')->insert($chunk);
                }
                $orders = [];
                $items = [];
            }
        }

    }
}
