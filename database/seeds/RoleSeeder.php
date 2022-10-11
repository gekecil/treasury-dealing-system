<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('roles')->insert([
			[
				'name' => 'super administrator',
				'is_interbank_dealer' => false,
				'is_sales_dealer' => false
			],
			[
				'name' => 'administrator',
				'is_interbank_dealer' => true,
				'is_sales_dealer' => true
			],
			[
				'name' => 'IT security',
				'is_interbank_dealer' => false,
				'is_sales_dealer' => false
			],
			[
				'name' => 'treasury director',
				'is_interbank_dealer' => true,
				'is_sales_dealer' => true
			],
			[
				'name' => 'treasury head',
				'is_interbank_dealer' => true,
				'is_sales_dealer' => true
			],
			[
				'name' => 'interbank senior dealer',
				'is_interbank_dealer' => true,
				'is_sales_dealer' => true
			],
			[
				'name' => 'interbank junior dealer',
				'is_interbank_dealer' => true,
				'is_sales_dealer' => true
			],
			[
				'name' => 'sales senior dealer',
				'is_interbank_dealer' => false,
				'is_sales_dealer' => true
			],
			[
				'name' => 'sales junior dealer',
				'is_interbank_dealer' => false,
				'is_sales_dealer' => true
			],
            [
				'name' => 'IT development',
				'is_interbank_dealer' => false,
				'is_sales_dealer' => false
			],
		]);
    }
}
