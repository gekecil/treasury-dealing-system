<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pdo = new PDO(
            'pgsql:host='.config('database.connections.pgsql.host').';dbname=TDS_III',
            config('database.connections.pgsql.username'),
            config('database.connections.pgsql.password')
        );

        $tables = DB::setPdo($pdo)->table('migrations')
            ->where('batch', 1)
            ->orderBy('id')
            ->get()
            ->map( function($item) {
                return substr($item->migration, 25, -6);
            });

        $tables->each( function($table) use($pdo) {
            $data = DB::setPdo($pdo)
                ->table($table)
                ->get()
                ->each( function($data) use($pdo, $table) {
                    $insert = DB::reconnect()
                        ->table($table)
                        ->insert(
                            ((array) $data)
                        );
                });

            echo $table.PHP_EOL;

            if (Schema::hasColumn($table, 'id') && DB::table($table)->exists()) {
                DB::reconnect()->statement("SELECT setval('".$table."_id_seq', (SELECT max(id) FROM ".$table."))");
            }

        });

    }
}
