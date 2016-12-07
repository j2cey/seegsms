<?php

use Illuminate\Database\Seeder;

class ValidationworkflowsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('validationworkflows')->insert([
            [
                'id' => 1,
                'title' => 'Validation des campagne planifiées',
                'model' => 'Campaignplannings',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],
        ]);

        DB::table('validationworkflowsteps')->insert([
            [
                'id' => 1,
                'workflow_id' => 1,
                'step' => 1,
                'validator' => 'validate.campaigns.n1',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],
        ]);

        DB::table('validationworkflowsteps')->insert([
            [
                'id' => 2,
                'workflow_id' => 1,
                'step' => 2,
                'validator' => 'validate.campaigns.n2',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],
        ]);
    }
}
