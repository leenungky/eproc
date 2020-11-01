<?php

namespace App\Repositories;

use App\Jobs\SendEmail;
use App\Mail\QueuingMail;
use App\Mail\TestMail;
use App\Models\TenderAanwijzings;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class TenderAanwijzingRepository extends BaseRepository
{

    private $logName = 'TenderAanwijzingRepository';
    public $guarded = ['id','tender_number','action_status','line_id','created_at','created_by','updated_at','updated_by','deleted_at','deleted_by'];


    public function __construct()
    {
    }

    public function fields()
    {
        $fields = [];
        if(Auth::user()->isVendor()){
            $this->guarded[] = 'public_status';
            // $this->guarded[] = 'result_attachment';
            $this->guarded[] = 'result_description';
        } else {
            // $this->guarded[] = 'result_attachment';
            $this->guarded[] = 'result_description';
            $this->guarded[] = 'note';
        }
        foreach(Schema::getColumnListing((new TenderAanwijzings())->table) as $field){
            if(!in_array($field,$this->guarded)) $fields[] = $field;
        }
        return $fields;
    }

    /**
     * find all data TenderParameter
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll()
    {
        try {
            $models = TenderAanwijzings::all();
            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findAll error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * find data TenderParameter by id
     *
     * @param int $primaryKey
     *
     * @return \App\Models\TenderAanwijzings $data
     */
    public function findById($primaryKey, $withThrow = true)
    {
        try {
            if ($withThrow) {
                return TenderAanwijzings::findOrFail($primaryKey);
            } else {
                return TenderAanwijzings::find($primaryKey);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::findById error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * find data by tender number
     *
     * @param string $number
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findByTenderNumber($number)
    {
        try {
            $query = TenderAanwijzings::where('tender_number',$number)
                    ->OfPublic(Auth::user())
                    ->get();
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findByTenderNumber error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * save record
     *
     * @param array $params
     *
     * @return \App\Models\TenderAanwijzings updated data
     *
     * @throws \Exception
     */
    public function save($params)
    {
        try {
            DB::beginTransaction();
            $model = new TenderAanwijzings();
            if(isset($params['id'])){
                $model = TenderAanwijzings::find($params['id']);
            }
            $model->fill($params);
            $model->save();
            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::save error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * delete record
     *
     * @param int $primaryKey
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function delete($primaryKey, $path = null)
    {
        try {
            DB::beginTransaction();
            $model = TenderAanwijzings::findOrFail($primaryKey);
            $result = $model->delete();
            if($path){
                $this->removeStorage($path . '/' . $model->attachment);
            }
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::delete error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function sendEmail($tender, $params = null)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findByTenderNumber($tender->tender_number);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $emailTeams = '';
            if($teams->count() > 0){
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach($tenderVendor as $vendor){
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_aanwijzing',
                    'subject' => 'INVITED: '.__('tender.aanwijzing').' - '.$tender->tender_number.' '.$tender->title,
                    'view_data' => [
                        'tender' => $tender,
                        'event' => $this->findById($params['id']),
                        'vendor' => $vendor,
                        'linkTender' => route('tender.show', ['id' => $tender->id, 'type' => 'process_registration']),
                    ],
                ];
                $details = [
                    'email' => $mailTos,
                    'mailable' => new QueuingMail((object) $paramsEmail),
                ];
                $details['cc'] = [];
                if(!empty($emailTeams)){
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmail error : ' . $e->getMessage());
            throw $e;
        }
    }
}
