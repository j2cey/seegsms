<?php

use Illuminate\Database\Seeder;

class CampaignTypesTableSeeder extends Seeder
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

        // Ajouter type 1
        $data = $this->ajoutertypedecampagne($data,$lastid,'Factures','Infos factures',2);

        // Ajouter type 2
        $data = $this->ajoutertypedecampagne($data,$lastid,'Relances','Campagne Relances',1);

        // Ajouter type 3
        $data = $this->ajoutertypedecampagne($data,$lastid,'Message ponctuel','Simple message aux clients',3);

        DB::table('campaigntypes')->insert($data);
    }

    private function ajoutertypedecampagne($data,&$lastid,$title,$descript,$prioritylevel){

        $lastid = $lastid + 1;

        $data[] = [
            'id' => $lastid,
            'title' => $title,
            'descript' => $descript,
            'prioritylevel' => $prioritylevel,
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'updated_at' => \Carbon\Carbon::now()->toDateTimeString(),
        ];

        return $data;
    }
}
