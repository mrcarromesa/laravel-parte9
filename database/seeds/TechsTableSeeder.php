<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TechsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $techs = ['PHP', 'React', 'NodeJS', 'Delphi'];

        foreach ($techs as $value) {
            DB::table('techs')->insert([
                'nome' => $value
            ]);
        }
    }
}
