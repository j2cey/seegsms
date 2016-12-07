<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Traits\DateUtilitiesTrait;

class Taskqueue extends Model
{
    use DateUtilitiesTrait;

    protected $fillable = ['id', 'taskcode', 'taskuid', 'taskdesc', 'pickupuid', 'pickup_at'];

    public function __construct(array $attributes = array())
    {
        /* override your model constructor */
        parent::__construct($attributes);
    }

    public function getfree()
    {
        $uid = uniqid();

        if ($this->freeze(1, $uid) > 0) {
            $data = self::where('taskcode', $this->taskcode)
                ->where('pickupuid', $uid)
                ->first()->toArray();

            $this->fill($data);
            $this->syncOriginal();
            $this->exists = true;

            $this->save();

            return true;
        } else {
            return false;
        }
    }

    public function goNext($taskcode, $taskdesc = null)
    {
        if (is_null($taskdesc)) {
            // Nothing to do
        } else {
            $this->setAttribute('taskdesc', $taskdesc);
        }
        $this->setAttribute('taskcode', $taskcode);
        $this->setAttribute('pickupuid', "0");

        $this->save();
    }

    public function setfree()
    {
        $this->unfreeze();
        $this->save();
    }

    public function endqueue()
    {
        $this->destroy($this->id);
    }

    private function freeze($limit, $uid)
    {
        return DB::table($this->getTable())->whereIn('id', DB::table($this->getTable())
            ->select('id')
            ->where('taskcode', $this->taskcode)
            ->where('pickupuid', "0")
            ->orderBy('created_at', 'desc')
            ->take($limit)
        )->update(['pickupuid' => $uid, 'pickup_at' => $this->getNowDateTime()]);
    }

    private function unfreeze()
    {
        $this->setAttribute('pickupuid', "0");
    }
}
