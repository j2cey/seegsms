<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            [
                'id' => 1,
                'name' => 'Administrer tout',
                'slug' => 'high.syst.manage',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ], [
                'id' => 2,
                'name' => 'Gerer les Utilisateurs',
                'slug' => 'manage.users',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ], [
                'id' => 3,
                'name' => 'Gerer les Roles',
                'slug' => 'manage.roles',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ], [
                'id' => 4,
                'name' => 'Gerer les Permissions',
                'slug' => 'manage.permissions',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ], [
                'id' => 5,
                'name' => 'Gerer les Campagnes',
                'slug' => 'manage.campaigns',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ], [
                'id' => 6,
                'name' => 'Configurer les Campagnes',
                'slug' => 'config.campaigns',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ], [
                'id' => 7,
                'name' => 'Valider les Campagnes N1',
                'slug' => 'validate.campaigns.n1',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],[
                'id' => 8,
                'name' => 'Valider les Campagnes N2',
                'slug' => 'validate.campaigns.n2',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],[
                'id' => 9,
                'name' => 'Accéder aux config du système',
                'slug' => 'seegsms.config.access',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],[
                'id' => 10,
                'name' => 'Modifier les config du système',
                'slug' => 'seegsms.config.modify',
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],
        ]);
    }
}
