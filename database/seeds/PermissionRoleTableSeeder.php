<?php

use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [];
        $lastid = 0;
        $nbpermissions = 10;

        // Affecter Super Admin
        $data = $this->affecterpermissionall($data,$lastid,1,$nbpermissions);

        // Affecter User Admin
        $data = $this->affecterpermission($data,$lastid,2,[1]);

        // Affecter Role Admin
        $data = $this->affecterpermission($data,$lastid,3,[2]);

        // Affecter Permission Admin
        $data = $this->affecterpermission($data,$lastid,4,[3]);

        // Affecter Gestionnaire Campagnes
        $data = $this->affecterpermission($data,$lastid,5,[4,6]);

        // Affecter Administrateur Campagnes
        $data = $this->affecterpermission($data,$lastid,5,[4,7]);

        DB::table('permission_role')->insert($data);
    }

    private function affecterpermission($data,&$lastid,$roleid,$permissionlist){

        foreach($permissionlist as $permission){
            $lastid = $lastid + 1;
            $data[] = [
                'id' => $lastid,
                'permission_id' => $permission,
                'role_id' => $roleid,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ];
        }

        return $data;
    }

    private function affecterpermissionall($data,&$lastid,$roleid,$nbpermissions){

        for ($i = 1; $i <= $nbpermissions; $i ++){
            $data = $this->affecterpermission($data,$lastid,$roleid,[$i]);
        }

        return $data;
    }
}
