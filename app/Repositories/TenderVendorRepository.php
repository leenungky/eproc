<?php

namespace App\Repositories;

use App\Enums\TenderStatusEnum;
use App\Enums\TenderSubmissionEnum;
use App\Models\TenderVendor;
use App\Models\TenderVendorAwarding;
use App\Models\TenderVendorSubmission;
use App\Vendor;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenderVendorRepository extends BaseRepository
{

    private $logName = 'TenderVendorRepository';
    private $_fields = [
        'vendor_code', 'vendor_name', 'pic_full_name', 'status', 'vendor_status', 'vendor_evaluation_score',
        'scope_of_supply1', 'scope_of_supply2', 'scope_of_supply3', 'scope_of_supply4'
    ];


    public function __construct()
    {
    }

    public function fields()
    {

        return $this->_fields;
    }

    /**
     * find all data TenderParameter
     *
     * @param string $number
     * @param string|array $status
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll($number = null, $status = null)
    {
        try {
            $query = (new VendorRepository)->getQueryVendor()
                ->select(
                    'tender_vendors.id',
                    'tender_vendors.vendor_id',
                    'tender_vendors.tender_number',
                    'vendors.vendor_code',
                    'vendors.vendor_name',
                    'vendors.pic_full_name',
                    'vendors.pic_email',
                    'vendors.president_director',
                    'tender_vendors.status',
                    'tender_vendors.updated_at',
                    'vendor_profiles.id as vendor_profile_id',
                    'vendor_profiles.company_name'
                )->join('tender_vendors', 'tender_vendors.vendor_id', 'vendors.id')
                ->whereNull('tender_vendors.deleted_at')
                ->whereNull('vendors.deleted_at');

            if ($number != null) {
                $query = $query->where('tender_number', $number);
            }

            if ($status != null && is_array($status)) {
                $query = $query->whereIn('tender_vendors.status', $status);
            } else if ($status != null && is_string($status)) {
                $query = $query->where('tender_vendors.status', $status);
            }
            // return $query->get();
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findAll error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data TenderParameter by id
     *
     * @param int $primaryKey
     *
     * @return \App\Models\TenderVendor $data
     */
    public function findById($primaryKey, $withThrow = true)
    {
        try {
            if ($withThrow) {
                return TenderVendor::findOrFail($primaryKey);
            } else {
                return TenderVendor::find($primaryKey);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::findById error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    public function findByVendor($tenderNumber, $vendorId)
    {
        try {
            return TenderVendor::where('tender_number', $tenderNumber)
                ->where('vendor_id', $vendorId)
                ->first();
        } catch (Exception $e) {
            Log::error($this->logName . '::findByVendor error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data by tender number
     *
     * @param string $number
     * @param string|array $status
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findByTenderNumber($number, $status = null)
    {
        try {
            $query = (new VendorRepository)->getQueryVendor()
                ->select(
                    'tender_vendors.id',
                    'tender_vendors.vendor_id',
                    'tender_vendors.tender_number',
                    'vendors.vendor_code',
                    'vendors.vendor_name',
                    'vendors.pic_full_name',
                    'vendors.pic_email',
                    'vendors.president_director',
                    'tender_vendors.status',
                    'tender_vendors.updated_at',
                    'vendor_profiles.id as vendor_profile_id',
                    'vendor_profiles.company_name',
                    'ref_countries.country_description as country',
                    'vendors.vendor_group',
                    DB::raw("COALESCE(vendor_profiles.company_warning, 'GREEN') as status_sanction"),
                    DB::raw("CASE vendor_profiles.company_warning WHEN 'RED' THEN 'inactive' ELSE 'active' END as vendor_status"),
                    DB::raw("es.name as vendor_evaluation_score"),
                    DB::raw('esc.name as vendor_evaluation_score_category'),
                    'vpc.scope_of_supply',
                    DB::raw("'' as scope_of_supply1"),
                    DB::raw("'' as scope_of_supply2"),
                    DB::raw("'' as scope_of_supply3"),
                    DB::raw("'' as scope_of_supply4")
                )->join('tender_vendors', 'tender_vendors.vendor_id', 'vendors.id')
                ->leftJoin(
                    DB::raw("(Select distinct on (a.vendor_id) a.vendor_id,a.vendor_evaluation_id,b.category_id,a.total_score,a.updated_at
                from \"vendor_evaluation_forms\" a join \"vendor_evaluation_generals\" b on a.vendor_evaluation_id=b.id
                where b.status='APPROVED' and a.updated_at is not null order by a.vendor_id,a.updated_at desc) as eg"),
                    function ($join) {
                        $join->on('vendors.id', '=', 'eg.vendor_id');
                    }
                )
                ->leftJoin('vendor_evaluation_score_categories as esc', function ($join) {
                    $join->on('esc.id', '=', 'eg.category_id');
                })
                ->leftJoin('vendor_evaluation_scores as es', function ($join) {
                    $join->on('esc.id', '=', 'es.category_id')
                        ->whereRaw("CASE WHEN es.lowest_score_operator = '>=' THEN es.lowest_score <= eg.total_score ELSE es.lowest_score < eg.total_score END")
                        ->whereRaw("CASE WHEN es.highest_score_operator = '<=' THEN es.highest_score >= eg.total_score ELSE es.highest_score > eg.total_score END");
                })
                ->leftJoin(DB::raw("(select vendor_profile_id,STRING_AGG(classification, ' ,') as scope_of_supply from vendor_profile_competencies where deleted_at is null group by vendor_profile_id) vpc"), function ($join) {
                    $join->on('vpc.vendor_profile_id', '=', 'vendor_profiles.id');
                })
                ->where('tender_number', $number)
                ->whereNull('tender_vendors.deleted_at')
                ->whereNull('vendors.deleted_at');

            if ($status != null && is_array($status)) {
                $query = $query->whereIn('tender_vendors.status', $status);
            } else if ($status != null && is_string($status)) {
                $query = $query->where('tender_vendors.status', $status);
            }
            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findByTenderNumber error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data vendor by scope of supply
     *
     * @param array $params, tender number
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findVendorByScopeOfSupply($params)
    {
        try {
            $query = (new VendorRepository)->getQueryVendor()
                ->select(
                    'vendors.id',
                    'vendors.vendor_code',
                    'vendors.vendor_name',
                    'vendors.president_director',
                    'vendors.pic_full_name',
                    'vendors.pic_email',
                    'vendor_profiles.id as vendor_profile_id',
                    'vendor_profiles.company_name',
                    DB::raw("COALESCE(vendor_profiles.company_warning, 'GREEN') as status_sanction"),
                    DB::raw("CASE vendor_profiles.company_warning WHEN 'RED' THEN 'inactive' ELSE 'active' END as vendor_status"),
                    DB::raw("es.name as vendor_evaluation_score"),
                    DB::raw('esc.name as vendor_evaluation_score_category'),
                    'vpc.scope_of_supply',
                    DB::raw("'' as scope_of_supply1"),
                    DB::raw("'' as scope_of_supply2"),
                    DB::raw("'' as scope_of_supply3"),
                    DB::raw("'' as scope_of_supply4")
                )
                ->leftJoin(
                    DB::raw("(Select distinct on (a.vendor_id) a.vendor_id,a.vendor_evaluation_id,b.category_id,a.total_score,a.updated_at
                from \"vendor_evaluation_forms\" a join \"vendor_evaluation_generals\" b on a.vendor_evaluation_id=b.id
                where b.status='APPROVED' and a.updated_at is not null order by a.vendor_id,a.updated_at desc) as eg"),
                    function ($join) {
                        $join->on('vendors.id', '=', 'eg.vendor_id');
                    }
                )
                ->leftJoin('vendor_evaluation_score_categories as esc', function ($join) {
                    $join->on('esc.id', '=', 'eg.category_id');
                })
                ->leftJoin('vendor_evaluation_scores as es', function ($join) {
                    $join->on('esc.id', '=', 'es.category_id')
                        ->whereRaw("CASE WHEN es.lowest_score_operator = '>=' THEN es.lowest_score <= eg.total_score ELSE es.lowest_score < eg.total_score END")
                        ->whereRaw("CASE WHEN es.highest_score_operator = '<=' THEN es.highest_score >= eg.total_score ELSE es.highest_score > eg.total_score END");
                })
                ->leftJoin(DB::raw("(select vendor_profile_id,STRING_AGG(classification, ' ,') as scope_of_supply from vendor_profile_competencies where deleted_at is null group by vendor_profile_id) vpc"), function ($join) {
                    $join->on('vpc.vendor_profile_id', '=', 'vendor_profiles.id');
                });
            if (!empty($params['vendor_code'])) {
                $query->whereRaw('lower(vendors.vendor_code) like ?', ['%' . strtolower($params['vendor_code']) . '%']);
            }
            if (!empty($params['vendor_name'])) {
                $query->whereRaw("lower(vendors.vendor_name) like ?", ['%' . strtolower($params['vendor_name']) . '%']);
            }
            if (!empty($params['sos'])) {
                $query->whereRaw("vendor_profiles.id IN (select vendor_profile_id from vendor_profile_competencies where classification=?)", [$params['sos']]);
            }
            if (!empty($params['is_awarded'])) {
                $awardedVendor = TenderVendorAwarding::where('awarding_status', 'winner')
                    ->whereNull('deleted_at')
                    ->pluck('vendor_id');
                $query->whereIn('vendors.id', $awardedVendor);
            }

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findVendorByScopeOfSupply error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    public function findScopeOfSupplies()
    {
        try {
            $query = DB::table('ref_scope_of_supplies');
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findScopeOfSupplies error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    /**
     * find data by tender number
     *
     * @param \App\TenderParameter $tender
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findForNotifyTenderStarted($tender, $stageType, $vendorId = null)
    {
        try {
            $query = (new VendorRepository)->getQueryVendor()
                ->select(
                    'tender_vendors.vendor_id',
                    'tender_vendors.tender_number',
                    'vendors.vendor_code',
                    'vendors.vendor_name',
                    'vendors.pic_full_name',
                    'vendors.pic_email',
                    'vendors.president_director',
                    'vendor_profiles.company_name'
                )->join('tender_vendors', 'tender_vendors.vendor_id', 'vendors.id')
                ->where('tender_vendors.tender_number', $tender->tender_number)
                ->whereIn('tender_vendors.status', [TenderVendor::STATUS[2], TenderVendor::STATUS[4]])
                ->whereNull('tender_vendors.deleted_at')
                ->whereNull('vendors.deleted_at');

            if ($tender->prequalification == 1) {
                $query = $query->join('tender_vendor_submissions', 'tender_vendor_submissions.vendor_id', 'vendors.id')
                    ->whereIn('tender_vendor_submissions.status', [TenderVendorSubmission::STATUS[3]])
                    ->where('tender_vendor_submissions.submission_method', 1);
            }

            if ($vendorId != null && $vendorId != '') {
                $query = $query->where('tender_vendors.vendor_id', $vendorId);
            }

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findForNotifyTenderStarted error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data by tender number
     *
     * @param \App\TenderParameter $tender
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findForNotifyTenderEvaluated($tender, $stageType)
    {
        try {
            $query = (new VendorRepository)->getQueryVendor()
                ->select(
                    'tender_vendor_submissions.vendor_id',
                    'tender_vendor_submissions.tender_number',
                    'vendors.vendor_code',
                    'vendors.vendor_name',
                    'vendors.pic_full_name',
                    'vendors.pic_email',
                    'vendors.president_director',
                    'vendor_profiles.company_name',
                    'tender_vendor_submissions.status'
                )->join('tender_vendor_submissions', 'tender_vendor_submissions.vendor_id', 'vendors.id')
                ->where('tender_vendor_submissions.tender_number', $tender->tender_number)
                ->where('tender_vendor_submissions.submission_method', $stageType)
                ->whereIn('tender_vendor_submissions.status', [TenderVendorSubmission::STATUS[3], TenderVendorSubmission::STATUS[4]])
                ->whereNull('tender_vendor_submissions.deleted_at')
                ->whereNull('vendors.deleted_at');

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findForNotifyTenderEvaluated error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find data vendor for notification awarding
     *
     * @param \App\TenderParameter $tender
     *
     * @return \Illuminate\Database\Eloquent\Collection $data
     */
    public function findForNotifyTenderAwarded($tender)
    {
        try {
            $query = (new VendorRepository)->getQueryVendor()
                ->select(
                    'tender_vendors_awarding.vendor_id',
                    'tender_vendors_awarding.tender_number',
                    'tender_vendors_awarding.awarding_status',
                    'vendors.vendor_code',
                    'vendors.vendor_name',
                    'vendors.pic_full_name',
                    'vendors.pic_email',
                    'vendors.president_director',
                    'vendor_profiles.company_name'
                )->join('tender_vendors_awarding', 'tender_vendors_awarding.vendor_id', 'vendors.id')
                ->where('tender_vendors_awarding.tender_number', $tender->tender_number)
                ->whereIn('tender_vendors_awarding.status', [TenderSubmissionEnum::STATUS_ITEM[2], TenderSubmissionEnum::FLOW_STATUS[3]])
                ->whereNull('tender_vendors_awarding.deleted_at')
                ->whereNotNull('tender_vendors_awarding.awarding_status')
                ->whereNull('vendors.deleted_at');

            return $query->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::findForNotifyTenderAwarded error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * add new record
     *
     * @param array $params
     *
     * @return \App\Models\TenderVendor new inserted data
     *
     * @throws \Exception
     */
    public function create($params)
    {
        try {
            DB::beginTransaction();
            $params['status'] = TenderVendor::STATUS[0];
            $result = TenderVendor::create($params);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::create error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * bulk insert record
     *
     * @param \App\TenderParameter $tender, data vendor
     * @param array $params, data vendor
     *
     * @return boolean
     *
     * @throws \Exception
     */
    public function insertBulk($tender, array $params)
    {
        try {
            DB::beginTransaction();
            $data = [];
            foreach ($params as $val) {
                $data[] = [
                    'tender_number' => $tender->tender_number,
                    'vendor_id' => $val['id'],
                    'vendor_code' => $val['vendor_code'],
                    'tender_vendor_type' => $val['tender_vendor_type'],
                    'status' => TenderVendor::STATUS[0],
                ];
            }
            $result = TenderVendor::insertBulk($data);

            if ($tender->status == 'active') {
                $tender->public_status = TenderStatusEnum::PUBLIC_STATUS[5];
                $tender->action_status = TenderStatusEnum::ACT_CHANGE;
                $tender->save();
            }

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::insertBulk error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * update or create record
     *
     * @param array $params
     *
     * @return \App\Models\TenderVendor updated data
     *
     * @throws \Exception
     */
    public function save($params)
    {
        try {
            DB::beginTransaction();
            $model = TenderVendor::updateOrCreate(
                $params,
                [
                    'tender_number' => $params['tender_number'],
                    'vendor_id' => $params['vendor_id']
                ]
            );
            // $model->fill($params);
            // $model->save();
            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::update error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * update existing record
     *
     * @param int $primaryKey
     * @param array $params
     *
     * @return \App\Models\TenderVendor updated data
     *
     * @throws \Exception
     */
    public function update($primaryKey, $params)
    {
        try {
            DB::beginTransaction();
            $model = TenderVendor::findOrFail($primaryKey);
            $model->fill($params);
            $model->save();
            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::update error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * update existing record
     *
     * @param \App\TenderParameters $tender
     * @param array $params
     *
     * @return \App\Models\TenderVendor updated data
     *
     * @throws \Exception
     */
    public function updateByVendorId($tender, $params)
    {
        try {
            DB::beginTransaction();
            $model = TenderVendor::where('tender_number', $tender->tender_number)
                ->where('vendor_id', $params['vendor_id'])
                ->where('tender_vendor_type', 1)
                ->firstOrFail();
            $model->fill($params);
            $model->save();
            DB::commit();
            return $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::updateByVendorId error : ' . $e->getMessage());
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
    public function delete($primaryKey)
    {
        try {
            DB::beginTransaction();
            $model = TenderVendor::findOrFail($primaryKey);
            $result = $model->delete();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::delete error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }
}
