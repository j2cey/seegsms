<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Input;

class SeegsmsconfigController extends Controller
{
    public function getIndex()
    {
        $campaigns = Campaign::all();

        return response()->success(compact('campaigns'));
    }

    public function getSeegsmsconfigs()
    {
        $configs = Config::get('seegsms.campaignfile');

        return response()->success(compact('configs'));
    }

    public function getSeegsmsconfigsShow($detail)
    {
        $seegsmsconfigs = Config::get('seegsms');

        return response()->success(compact('seegsmsconfigs'));
    }

    public function putSeegsmsconfigsShow()
    {
        $configs = Input::get('data');
        $seegsmsconfigs = $configs['seegsmsconfigs'];

        $configarray = Config::get('seegsms');

        foreach ($seegsmsconfigs as $skey => $config){
            $sectionvalues = $config['svalue'];

            foreach ($sectionvalues as $ckey => $sectionvalue){
                $configarray[$skey]['svalue'][$ckey]['value'] = $sectionvalue['value'];
            }
        }

        $data = var_export($configarray, 1);
        if(\File::put(dirname(app_path()) . '/config/seegsms.php', "<?php\n return $data ;")) {
            // Successful, return Redirect...
        }

        return response()->success(compact('configarray'));
    }
}
