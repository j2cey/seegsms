<?php

use Illuminate\Database\Seeder;

class RoleUserTableSeeder extends Seeder
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
        $nbroles = 6;

        // Affecter user 1 (SYS)
        $data = $this->affecterroleall($data,$lastid,1,$nbroles);

        // Affecter user 2 (Jude parfait)
        $data = $this->affecterroleall($data,$lastid,2,$nbroles);

        // Affecter user 3
        $data = $this->affecterrole($data,$lastid,3,[5]);

        // Affecter user 4
        $data = $this->affecterrole($data,$lastid,4,[6]);

        DB::table('role_user')->insert($data);

        /*DB::table('role_user')->insert([
            [
                'id' => 1,
                'role_id' => 1,
                'user_id' => 1,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ], [
                'id' => 2,
                'role_id' => 2,
                'user_id' => 1,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ], [
                'id' => 3,
                'role_id' => 3,
                'user_id' => 1,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ], [
                'id' => 4,
                'role_id' => 4,
                'user_id' => 1,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ],
        ]);*/
    }

    private function affecterrole($data,&$lastid,$userid,$rolelist){

        foreach($rolelist as $role){
            $lastid = $lastid + 1;
            $data[] = [
                'id' => $lastid,
                'role_id' => $role,
                'user_id' => $userid,
                'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
            ];
        }

        return $data;
    }

    private function affecterroleall($data,&$lastid,$userid,$nbroles){

        for ($i = 1; $i <= $nbroles; $i ++){
            $data = $this->affecterrole($data,$lastid,$userid,[$i]);
        }

        return $data;
    }
}
