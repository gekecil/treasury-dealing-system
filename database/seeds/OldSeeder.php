<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pdo = new PDO(
            'pgsql:host='.config('database.connections.pgsql.host').';dbname=treasury_v2',
            config('database.connections.pgsql.username'),
            config('database.connections.pgsql.password')
        );

        $tables = DB::setPdo($pdo)->table('migrations')
            ->where('batch', 1)
            ->whereRaw("migration not similar to '%(create_failed_jobs_table|create_roles_table|create_groups_table|create_market_hours_table)'")
            ->orderBy('id')
            ->get()
            ->map( function($item) {
                return substr($item->migration, 25, -6);
            });

        $tables->each( function($table) use($pdo) {
            $tables = DB::reconnect()->getSchemaBuilder()->getColumnListing($table);

            $query = DB::setPdo($pdo)->table($table);

            if ($table === 'sales_deals') {
                $query->whereNotExists(function ($query) use($table) {
                    $query->select(DB::raw(1))
                    ->from('modifications')
                    ->whereRaw('modifications.deal_created_id = '.$table.'.id')
                    ->where('interbank_sales', 2)
                    ->where('confirmed', true);
                });
            }

            if (in_array($table, ['sales_deal_files', 'sales_deal_rates', 'special_rate_deals'])) {
                $query->whereNotExists(function ($query) use($table) {
                    $query->select(DB::raw(1))
                    ->from('modifications')
                    ->whereRaw('modifications.deal_created_id = '.$table.'.sales_deal_id')
                    ->where('interbank_sales', 2)
                    ->where('confirmed', true);
                });
            }

            $query->get()->each( function($item) use($table, $tables) {
                $item = (array) $item;

                if ($table === 'currencies') {
                    $item['primary_code'] = substr($item['currency_code'], -3);
                    $item['secondary_code'] = substr($item['currency_code'], 0, -3) ?: null;

                    if ($item['secondary_code']) {
                        $item['secondary_code'] .= $item['primary_code'];
                    }
                }

                if ($table === 'currency_pairs') {
                    $item['dealable_fx_rate'] = true;
                }

                if ($table === 'sales_deals') {
                    $item['lhbu_remarks_code'] = array_values(array_filter(preg_split('/[^0-9]/', $item['lhbu_remarks_code'])));
                    $item['lhbu_remarks_code'] = $item['lhbu_remarks_code'][0];
                    $item['lhbu_remarks_kind'] = array_values(array_filter(preg_split('/[^0-9]/', $item['lhbu_remarks_kind'])));
                    $item['lhbu_remarks_kind'] = $item['lhbu_remarks_kind'][0];
                }

                if ($table === 'interbank_deals') {
                    $item['basic_remarks'] = $item['remarks'];
                }

                if (count(array_diff(array_keys($item), $tables))) {
                    $item = array_intersect_key($item, array_flip($tables));
                }

                try {
                    DB::reconnect()->table($table)->insert(
                        $item
                    );
                } catch(\Exception $e) {
                    dd($e->getMessage());
                }

            });

            echo $table.PHP_EOL;

            if ($query->exists() && property_exists($query->first(), 'id')) {
                DB::reconnect()->statement("SELECT setval('".$table."_id_seq', (SELECT max(id) FROM ".$table."))");
            }

        });
    }
}
