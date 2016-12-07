<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
        $this->call(CampaignTypesTableSeeder::class);
        $this->call(ValidationworkflowsTableSeeder::class);

        $tablesToCheck = ['users', 'roles', 'role_user', 'permissions', 'permission_role', 'campaigntypes','validationworkflows'];
        $this->fixPostgreSequence($tablesToCheck);

        Model::reguard();
    }

    private function fixPostgreSequence($tablesToCheck){
        if(DB::connection()->getName() == 'pgsql')
        {
            foreach($tablesToCheck as $tableToCheck)
            {
                $this->command->info('Checking the next id sequence for '.$tableToCheck);
                $highestId = DB::table($tableToCheck)->select(DB::raw('MAX(id)'))->first();
                $nextId = DB::table($tableToCheck)->select(DB::raw('nextval(\''.$tableToCheck.'_id_seq\')'))->first();
                if($nextId->nextval < $highestId->max)
                {
                    DB::select('SELECT setval(\''.$tableToCheck.'_id_seq\', '.$highestId->max.')');
                    $highestId = DB::table($tableToCheck)->select(DB::raw('MAX(id)'))->first();
                    $nextId = DB::table($tableToCheck)->select(DB::raw('nextval(\''.$tableToCheck.'_id_seq\')'))->first();
                    if($nextId->nextval > $highestId->max)
                    {
                        $this->command->info($tableToCheck.' autoincrement corrected');
                    }
                    else
                    {
                        $this->command->info('Arff! The nextval sequence is still all screwed up on '.$tableToCheck);
                    }
                }
            }
        }
    }
}
