<?php

namespace App\Repositories;

use App\Enums\TenderSubmissionEnum;
use App\Models\TenderItemDetail;
use App\Models\TenderItemDetailCategory;
use App\Models\TenderVendorItemDetail;
use App\Models\TenderVendorItemText;
use App\RefListOption;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenderItemSpecificationRepository extends BaseRepository
{

    private $logName = 'TenderItemDetailRepository';
    private $fields = [
        'field1' => ['description', 'requirement', 'reference'],
        'field2' => ['description', 'requirement', 'reference','data','respond'],
    ];

    public function __construct()
    {}

    public function fields($type = 'field1')
    {
        return $this->fields[$type];
    }

    /**
     * find detail item categories
     *
     * @param $number, tender_number
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findCategories($number, $createDefault = false)
    {
        try {
            $models = TenderItemDetailCategory::where('tender_number', $number)
                ->orderBy('order')
                ->get();
            if($createDefault && ($models == null || $models->count() == 0)){
                $models = $this->createDefaultCategory($number);
            }
            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findCategories error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find detail item categories
     *
     * @param $number, tender_number
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findVendorCategories($number, $vendorId)
    {
        try {
            $categoriesId = TenderVendorItemDetail::select('category_id')
                ->where('tender_number', $number)
                ->where('submission_method',3)
                ->where('vendor_id', $vendorId)
                ->groupBy('category_id')
                ->pluck('category_id');
            $models = TenderItemDetailCategory::where('tender_number', $number)
                ->whereIn('line_id', $categoriesId)
                ->orderBy('order')
                ->get();

            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findCategories error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find detail item categories
     *
     * @param $number, tender_number
     * @param $categoryId, categoryId
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function findItemDetail($number, $categoryId)
    {
        try {
            $query = TenderItemDetail::where('tender_number', $number)
                ->where('category_id', $categoryId);
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findItemDetail error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * find detail item categories
     *
     * @param App\TenderParameter $tender, tender
     * @param $categoryId, categoryId
     * @param $stageType, stageType
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function findVendorItemDetail($tender, $params, $stageType)
    {
        try {
            $query = TenderVendorItemDetail::where('tender_number', $tender->tender_number)
                ->where('submission_method', $stageType)
                ->where('vendor_id', $params['vendor_id'])
                ->where('category_id', $params['category_id']);
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findItemDetail error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function findVendorItemDetailByVendor($number, $vendorId, $stageType)
    {
        try {
            $query = TenderVendorItemDetail::where('tender_number', $number)
                ->where('submission_method', $stageType)
                ->where('vendor_id', $vendorId);
            return $query;
        } catch (Exception $e) {
            Log::error($this->logName . '::findItemDetail error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * save record
     *
     * @param \App\TenderParameter $tender
     * @param array $params
     * @param string $pageType
     *
     * @return boolean
     *
     * @throws \Exception
     */
    public function save($tender, $params, $pageType)
    {
        $params['tender_number'] = $tender->tender_number;
        if($params['type'] == 1){
            return $this->saveCategory($params);
        } else if($params['type'] == 2) {
            if($pageType == 'items'){
                return $this->saveItem($params);
            }else{
                return $this->saveVendorItem($params);
            }
        }
    }


    /**
     * save record item text
     *
     * @param string $number, tender number
     *
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @throws \Exception
     */
    private function createDefaultCategory($number)
    {
        try {

            $options = RefListOption::where('type', 'item_specification_category')
                        ->orderBy('id')
                        ->get();

            if($options!= null && $options->count() > 0){
                // insert data;
                $data = [];
                $counter = 0;
                foreach ($options as $cat) {
                    $counter++;
                    $data = [
                        'tender_number' => $number,
                        'key' => $cat->key,
                        'category_name' => $cat->value,
                        'template_id' => in_array($cat->key, ['cat4','cat5']) ? 2 : 1,
                        'order' => $counter,
                    ];
                    TenderItemDetailCategory::create($data);
                }
            }
            return TenderItemDetailCategory::where('tender_number', $number)
                ->orderBy('order')
                ->get();
        } catch (Exception $e) {
            Log::error($this->logName . '::createDefaultCategory error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * save record item category
     *
     * @param array $data
     *
     * @return boolean
     *
     * @throws \Exception
     */
    private function saveCategory($data)
    {
        try {
            $model = new TenderItemDetailCategory();
            if(!empty($data['id'])) {
                $model = TenderItemDetailCategory::find($data['id']);
            }else{
                $count = TenderItemDetailCategory::where('tender_number', $data['tender_number'])->count();
                $data['order'] = $count + 1;
            }
            $model->fill($data);
            $model->save($data);

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::saveCategory error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * save record item specification
     *
     * @param array $data
     *
     * @return boolean
     *
     * @throws \Exception
     */
    private function saveItem($data)
    {
        try {
            $model = new TenderItemDetail();
            if(!empty($data['id'])) {
                $model = TenderItemDetail::find($data['id']);
            }
            $model->fill($data);
            $model->save($data);

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::saveItem error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * save record item specification vendor
     *
     * @param array $data
     *
     * @return boolean
     *
     * @throws \Exception
     */
    private function saveVendorItem($data)
    {
        try {
            $vendor = Auth::user()->vendor;
            $stageType = !empty($params['stage_type'])
                ? $params['stage_type']
                : TenderSubmissionEnum::WORKFLOW_MAPPING_TYPE['process_technical_evaluation'];
            $data['submission_method'] = $stageType;
            if($vendor != null){
                $data['vendor_id'] = $vendor->id;
                $data['vendor_code'] = $vendor->vendor_code;
            }

            $model = new TenderVendorItemDetail();
            if(!empty($data['id'])) {
                $model = TenderVendorItemDetail::find($data['id']);
            }
            $model->fill($data);
            $model->save($data);

            return true;
        } catch (Exception $e) {
            Log::error($this->logName . '::saveVendorItem error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * delete record
     *
     * @param \App\TenderParameter $tender
     * @param array $params
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function delete($tender, $params)
    {
        // $params['tender_number'] = $tender->tender_number;
        if($params['type'] == 1){
            return $this->deleteCategory($params['id']);
        } else if($params['type'] == 2) {
            return $this->deleteItem($params['id']);
        }
    }

    /**
     * delete record item
     *
     * @param int $primaryKey
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function deleteCategory($primaryKey)
    {
        try {
            DB::beginTransaction();
            $category = TenderItemDetailCategory::findOrFail($primaryKey);
            $items = TenderItemDetail::where('category_id', $category->id)
                ->forceDelete();
            $result = $category->forceDelete();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::deleteItem error : ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * delete record item
     *
     * @param int $primaryKey
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function deleteItem($primaryKey)
    {
        try {
            DB::beginTransaction();
            $model = TenderItemDetail::findOrFail($primaryKey);
            $result = $model->delete();
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::deleteItem error : ' . $e->getMessage());
            throw $e;
        }
    }
}
