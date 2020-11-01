<?php

namespace App\Repositories;

use App\Jobs\SendEmail;
use App\Mail\QueuingMail;
use App\Mail\TestMail;
use App\Models\PoHeader;
use App\PurchaseOrder;
use App\PurchaseOrderItem;
use App\Models\TenderVendor;
use App\Vendor;
use App\RefPurchaseOrg;
use App\RefPurchaseGroup;
use App\TenderItem;
use App\TenderParameter;
use App\Models\Ref\RefCurrency;
use App\Models\PoReplicationStatus;
use App\Repositories\TenderRepository;
use App\Repositories\VendorRepository;
use App\Repositories\PoTenderItemsRepository;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use App\Repositories\TenderProcessNegotiationRepository;
use App\VendorProfile;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\SapConnector;
use App\TenderWorkflow;
use App\TenderWorkflowHelper;
use App\Traits\AccessLog;

class PoRepository extends TenderRepository
{
    use AccessLog;

    private $logName = 'PoCreation';
    public $guarded = ['sap_message', 'id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'deleted_at', 'deleted_by'];


    public function __construct()
    {
    }

    public function fields()
    {
        $fields = ["vendor_name", "vendor_code"];
        // if(Auth::user()->isVendor()){
        //     $this->guarded[] = 'public_status';
        //     // $this->guarded[] = 'result_attachment';
        //     $this->guarded[] = 'result_description';
        // } else {
        //     // $this->guarded[] = 'result_attachment';
        //     $this->guarded[] = 'result_description';
        //     $this->guarded[] = 'note';
        // }
        foreach (Schema::getColumnListing((new PurchaseOrder())->table) as $field) {
            if (!in_array($field, $this->guarded))
                $fields[] = $field;
        }
        //dd($fields);

        return $fields;
    }

    public function fieldsItem()
    {
        return ["po_item", "pr_number", "pr_line_number", "product_code", "product_name", "qty", "satuan", "est_unit_price", "overall_limit", "unit_price", "total", "mata_uang", "complience", "delivery_date"];
    }

    /**
     * find all data TenderParameter
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findAll()
    {
        try {
            $models = PurchaseOrder::all();
            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findAll error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }



    public function findlistPo()
    {
        return $this->findlistPo2();
    }


    public function findlistPo2()
    {
        try {
            $models = PurchaseOrder::select(
                "po_list.id",
                "po_list.vendor_code",
                "vendors.vendor_name",
                "po_list.tender_number",
                "po_list.eproc_po_number",
                DB::raw("COALESCE(po_list.sap_po_number,'') as sap_po_number"),
                "po_list.eproc_po_status",
                DB::raw("po_list.created_at as created_on"),
                "po_list.deleted_at"
            )
                ->join("vendors", "po_list.vendor_code", "vendors.vendor_code")
                ->join("tender_parameters", "tender_parameters.tender_number", "po_list.tender_number");
            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findAll error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }


    public function findlistPoByTender($id)
    {
        try {
            $models = $this->findlistPo()->where("tender_parameters.id", $id);
            $isVendor = Auth::user()->isVendor();
            if ($isVendor) {
                $vendor = Vendor::where("vendor_code", Auth::user()->userid)->first();
                $models = $models->where("vendors.id", $vendor->id);
            }
            return $models;
        } catch (Exception $e) {
            Log::error($this->logName . '::findAll error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }

    public function findProfile(Request $request, $table)
    {
        $vendor_id = $request->get("loc-cat");
        $loc_cat = $request->get("loc-cat");
        return $this->qryFindProfile($table, $vendor_id, $loc_cat);
    }

    public function qryFindProfile($table, $vendor_id, $loc_cat = null)
    {
        $tmp = DB::table($table)
            ->select(
                "$table.*",
                'vendors.vendor_group',
                'ref_countries.country_description',
                'ref_provinces.region_description',
                'ref_cities.city_description',
                'ref_sub_districts.district_description'
            )
            ->join('vendor_profiles', function ($join) use ($table) {
                $join->on('vendor_profiles.id', '=', $table . ".vendor_profile_id")
                    ->whereNull('vendor_profiles.deleted_at');
            })
            ->join('vendors', function ($join) {
                $join->on('vendors.id', '=', "vendor_profiles.vendor_id")
                    ->whereNull('vendors.deleted_at');
            })
            ->join('ref_countries', function ($join) use ($table) {
                $join->on('ref_countries.country_code', '=', $table . '.country')
                    ->whereNull('ref_countries.deleted_at');
            })
            ->leftJoin('ref_provinces', function ($join) use ($table) {
                $join->on('ref_provinces.region_code', '=', $table . '.province');
                $join->on('ref_provinces.country_code', '=', $table . '.country')
                    ->whereNull('ref_provinces.deleted_at');
            })
            ->leftJoin('ref_cities', function ($join)  use ($table) {
                $join->on('ref_cities.city_code', '=', $table . '.city');
                $join->on('ref_cities.country_code', $table . '.country');
                $join->on('ref_cities.region_code', $table . '.province')
                    ->whereNull('ref_cities.deleted_at');
            })
            ->leftJoin('ref_sub_districts', function ($join) use ($table) {
                $join->on('ref_sub_districts.district_code', '=', $table . '.sub_district');
                $join->on('ref_sub_districts.country_code', $table . '.country');
                $join->on('ref_sub_districts.region_code', $table . '.province');
                $join->on('ref_sub_districts.city_code', $table . '.city')
                    ->whereNull('ref_sub_districts.deleted_at');
            })
            //->where("$table.id", $request->get('id'))
            // ->where("$table.vendor_profile_id", $request->get("vendor_id"))
            // ->where("$table.location_category", $request->get("loc-cat"))
            //$vendor_id, $loc_cat,
            ->where("$table.vendor_profile_id", $vendor_id)
            ->orderBy("$table.vendor_profile_id", 'DESC');
        if ($loc_cat != null) {
            $tmp = $tmp->where("$table.location_category", $loc_cat);
        }
        $tmp = $tmp->first();
        return $tmp;
    }

    public function findItemDetail($eproc_po_number, $tender, $vendorCode)
    {
        try {
            $tenderNumber = $tender->tender_number;
            $purchaseGroup = RefPurchaseGroup::where("id", $tender->purchase_group_id)->first();

            $vendor = Vendor::select(
                "vendors.id",
                DB::raw("vendor_profiles.id as vendor_profiles_id"),
                "vendor_code",
                DB::raw("vendor_profile_generals.company_name as vendor_name"),
                "vendors.vendor_group"
            )
                ->join("vendor_profiles", "vendor_profiles.vendor_id", "vendors.id", "left")
                ->join("vendor_profile_generals", "vendor_profile_generals.vendor_profile_id", "vendor_profiles.id", "left")
                ->where("vendor_code", $vendorCode)
                ->first();

            $currency = RefCurrency::orderBy("currency")->get();
            $header_technical = $this->getHeaderTechnical($eproc_po_number, $tenderNumber, $vendor->vendor_code);
            $header_commercial = $this->getHeaderCommercial($eproc_po_number, $tenderNumber, $vendor->vendor_code);
            $po_header_text = DB::table("po_header_text")->where("tender_number", $tenderNumber)->get();
            $po_term_text = DB::table("po_header_term_payment_text")->where("tender_number", $tenderNumber)->get();
            $po_list = PurchaseOrder::where("tender_number", $tenderNumber)
                ->where("vendor_code", $vendor->vendor_code)->first();
            $po_header = PoHeader::select(DB::raw("po_header.*"), "po_vendor_profile.address_1", "po_vendor_profile.address_2")
                ->join("po_vendor_profile", "po_vendor_profile.id", "=", "po_header.vendor_profile_id", "left")
                ->where("po_header.tender_number", $tenderNumber)
                ->where("po_header.vendor_code", $vendor->vendor_code)->first();

            $company_code = null;
            if (isset($po_header->assign_purchorg_company_code_id)) {
                $company_code = DB::table("ref_assign_purchorg_compcode")->select("ref_company_code.company_code", "ref_company_code.description")
                    ->join("ref_company_code", "ref_company_code.company_code", "=", "ref_assign_purchorg_compcode.company_code")
                    ->where("ref_assign_purchorg_compcode.id", $po_header->assign_purchorg_company_code_id)->first();
            }

            $purchaseOrg = RefPurchaseOrg::where("id", $tender->purchase_org_id)->first();
            $po_header_profile = DB::table("vendor_profile_generals")->where("vendor_profile_id", $vendor->vendor_profiles_id)->get();
            //dd($vendor);
            $stageType = 6;
            $neRepo = new TenderProcessNegotiationRepository();
            $models = [
                'vendor' => $vendor,
            ];
            $models["vendor_profiles"] = $po_header_profile;
            $models["purchaseOrg"] = $purchaseOrg;
            $models["purchaseGroup"] = $purchaseGroup;
            $models["currency"] = $currency;
            $models["header_technical"] = $header_technical;
            $models["header_commercial"] = $header_commercial;
            $models["po_list"] = $po_list;
            $models["po_header"] = $po_header;
            $models["po_header_text"] = $po_header_text;
            $models["po_term_text"] = $po_term_text;
            $models["company_code"] = $company_code;
            return $models;
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
     * @return \App\Models\PurchaseOrder $data
     */
    // public function findById($primaryKey, $withThrow = true)
    // {
    //     try {
    //         if ($withThrow) {
    //             return TenderAanwijzings::findOrFail($primaryKey);
    //         } else {
    //             return TenderAanwijzings::find($primaryKey);
    //         }
    //     } catch (Exception $e) {
    //         Log::error($this->logName . '::findById error : ' . $e->getMessage());
    //         Log::error($e);
    //         throw $e;
    //     }
    // }

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
            $query = TenderAanwijzings::where('tender_number', $number)
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
            $deleted_at = ["deleted_at" => date("Y-m-d H:i:s")];
            $model = PurchaseOrder::where("id",$primaryKey)->first();

            $arrTable = ["po_list","po_header", "po_items","po_header_commercial_awarding",
                "po_header_technical_awarding", "po_header_term_payment_text","po_header_text","po_item_commercial_awarding",
                "po_item_detail_services","po_item_technical_awarding","po_item_text","po_tax_codes", "po_additional_costs"];
            foreach ($arrTable as $table){
                DB::table($table)->where("eproc_po_number", $model->eproc_po_number)->update($deleted_at);
            }

            $result = PurchaseOrder::where("id",$primaryKey)->update($deleted_at);

            if ($path) {
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

    public function findAddressByCategory($params)
    {
        $data = DB::table("vendor_profile_generals")->where("vendor_profile_id", $params["vprof_id"])->where("location_category", $params["cat"])->get();
        return array("total" => count($data), "data" => $data);
    }

    /**
     * find data TenderParameter by id
     *
     * @param int $primaryKey
     *
     * @return \App\TenderParameter $data
     */
    public function findTenderParameterByTenderNumber($primaryKey, $withThrow = true, $whiteRelation = false)
    {
        try {
            if ($whiteRelation) {
                $query = $this->queryTenderParameter()
                    ->select(
                        'tender_parameters.*',
                        'ref_purchase_groups.description as internal_organization',
                        'ref_purchase_orgs.description as purchase_organization',
                        DB::raw('sm.value as submission_method_value'),
                        DB::raw('em.value as evaluation_method_value'),
                        DB::raw('tm.value as tender_method_value')
                    )
                    // ->leftJoin('ref_list_options as tm', function ($join) {
                    //     $join->on('tm.key', '=', 'tender_parameters.tender_method')
                    //         ->where('tm.type','tender_method_options');
                    // })
                    ->where('tender_parameters.tender_number', $primaryKey);
            } else {
                $query = TenderParameter::where('tender_number', $primaryKey)
                    ->OfUser(Auth::user());
            }
            $query = $query->OfUser(Auth::user());
            if ($withThrow) {
                return $query->firstOrFail(); // TenderParameter::findOrFail($primaryKey);
            } else {
                return $query->first(); //TenderParameter::find($primaryKey);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::findTenderParameterById error : ' . $e->getMessage());
            Log::error($e);
            throw $e;
        }
    }



    public function findItemPo($tender, $vcode, $type, $params = null)
    {
        if (isset($params["data_type"])) {
            if ($params["data_type"] == "document_type") {
                $doc_type = DB::table("tender_document_type")->where("tender_number", $tender->tender_number)
                    ->where("vendor_code", $vcode)->first();
                return response()->json($doc_type);
            } else {
                $number = $tender->tender_number;
                $status_replication = PoReplicationStatus::where("tender_number", $number)->first();
                $item = (new PoTenderItemsRepository)->findByTenderNumber($number, $vcode, $params);
                return DataTables::of($item)->make(true);
            }
        } else {
            $eproc_po_number = $params["eproc_po_number"];
            $dt = $this->getDataItemSubmision($tender, $eproc_po_number, $vcode);
            foreach ($dt as $val) {
                $harga = $val->qty * (($val->est_unit_price ?? 0) + ($val->overall_limit ?? 0));
                $sql = $this->setValueAdditionalCost($tender, $harga, "CT2", $val->item_id);
                $harga_add = DB::select(DB::raw($sql))[0]->total;
                $harga_baru = $harga + $harga_add;
                $val->total = $harga_baru;
            }
            return DataTables::of($dt)->make(true);
        }
    }

    public function getTotalAddHeaderCost($request, $tender, $vcode)
    {
        $harga = $request->get("total");
        $sql = $this->setHeaderValueAdditionalCost($request, $tender, $vcode, $harga);
        $harga_add = DB::select(DB::raw($sql))[0]->total;
        $harga_baru = $harga + $harga_add;
        return round($harga_baru);
    }


    public function setValueAdditionalCost($tender, $harga, $type, $item_id)
    {
        $sql = $this->setValueAdditionalCostTenderNumber($tender->tender_number, $harga, $type, $item_id);
        return $sql;
    }

    public function setValueAdditionalCostTenderNumber($tender_number, $harga, $type, $item_id)
    {
        $sql = "select sum(total) as total from (
            select sum(total) as total from (select CASE  WHEN calculation_pos=1 then  value  ELSE -value  END as total from po_additional_costs where tender_number='" . $tender_number . "' and conditional_type='" . $type . "' and value is not null and deleted_at is null and item_id=" . $item_id . ")
tbl
                union
                select sum(total) as total from (select
                 CASE
                  WHEN calculation_pos=1 then
                     ((percentage/100)*" . $harga . ")
                  ELSE
                     -((percentage/100)*" . $harga . ")
                 END as total
                    FROM po_additional_costs
                where tender_number='" . $tender_number . "' and conditional_type='" . $type . "' and deleted_at is null and item_id=" . $item_id . ") tbl) tabel";
        return $sql;
    }

    public function setHeaderValueAdditionalCost($request, $tender, $vcode, $harga)
    {
        $eproc_po_number = $request->get("eproc_po_number");
        $type = "CT1";
        $sql = $this->setHeaderValueAdditionalCostEprocPoNumber($eproc_po_number, $tender->tender_number, $vcode, $harga);
        return $sql;
    }

    public function setHeaderValueAdditionalCostEprocPoNumber($eproc_po_number, $tender_number, $vcode, $harga)
    {
        $type = "CT1";
        $sql = "select sum(total) as total from (
            select sum(total) as total from (select CASE  WHEN calculation_pos=1 then  value  ELSE -value  END as total from po_additional_costs where tender_number='" . $tender_number . "' and conditional_type='" . $type . "' and value is not null and deleted_at is null and eproc_po_number='" . $eproc_po_number . "' and vendor_code='" . $vcode . "')
tbl
                union
                select sum(total) as total from (select
                 CASE
                  WHEN calculation_pos=1 then
                     ((percentage/100)*" . $harga . ")
                  ELSE
                     -((percentage/100)*" . $harga . ")
                 END as total
                    FROM po_additional_costs
                where tender_number='" . $tender_number . "' and conditional_type='" . $type . "' and deleted_at is null and eproc_po_number='" . $eproc_po_number . "' and vendor_code='" . $vcode . "') tbl) tabel";
        return $sql;
    }

    public function getDataItemSubmision($tender, $eproc_po_number, $vcode)
    {
        $tbl_tender_items = "po_items";
        $dt = $this->getQueryItemSubmision($tbl_tender_items, $eproc_po_number, "po_item_technical_awarding", "po_item_commercial_awarding", $tender->tender_number, $vcode, 5, 6)->get();
        if (count($dt) == 0) {
            $dt = $this->getQueryItemSubmision($tbl_tender_items, $eproc_po_number,"po_item_technical_awarding", "po_item_commercial_awarding", $tender->tender_number, $vcode, 3, 4)->get();
        }
        return $dt;
    }

    public function getAdditionalCost($tender, $vcode)
    {
    }
    public function saveDocumentType($tender_number, $vcode, $type, $params)
    {
        try {
            DB::beginTransaction();
            $sql = DB::table("tender_document_type")->where("tender_number", $tender_number)->where("vendor_code", $vcode);
            if ($sql->first() == null) {
                if ($type == "document_type") {
                    $sql->insert(["tender_number" => $tender_number, "vendor_code" => $vcode, "document_type" => $params["document_type"]]);
                } else if ($type == "delivery_date") {
                    $sql->insert(["tender_number" => $tender_number, "vendor_code" => $vcode, "delivery_date" => $params["delivery_date"]]);
                } else if ($type == "document_date") {
                    $sql->insert(["tender_number" => $tender_number, "vendor_code" => $vcode, "document_date" => $params["document_date"]]);
                }
            } else {
                if ($type == "document_type") {
                    $sql->update(["tender_number" => $tender_number, "vendor_code" => $vcode, "document_type" => $params["document_type"]]);
                } else if ($type == "delivery_date") {
                    $sql->update(["tender_number" => $tender_number, "vendor_code" => $vcode, "delivery_date" => $params["delivery_date"]]);
                } else if ($type == "document_date") {
                    $sql->update(["tender_number" => $tender_number, "vendor_code" => $vcode, "document_date" => $params["document_date"]]);
                }
            }
            DB::commit();
            return true; // $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::save error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function savedata($tender, $vcode, $type, $params)
    {
        try {
            DB::beginTransaction();
            $this->savePoHeader($tender, $vcode, $type, $params);
            $this->savePoDetail($tender, $vcode, $type, $params);
            DB::commit();
            return true; // $model;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::save error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function savePoHeader($tender, $vcode, $type, $params)
    {
        $arr_item = array();
        parse_str($params["item"], $arr_item);
        $arr_text1 = $arr_item;
        $arr_text2 = $arr_item;
        $tender_number = $tender->tender_number;

        unset($arr_item['header_text'], $arr_item['term_of_payment'], $arr_item['net-value'], $arr_item['total-header'],
        $arr_item['tkd_percentage'], $arr_item['incoterms'], $arr_item['incoterms_location']);
        if (empty($arr_item['vendor_profile_id'])) {
            unset($arr_item['vendor_profile_id']);
        }

        $arr_item["tender_number"] = $tender_number;
        $arr_item["vendor_code"] = $vcode;
        $po_header = PoHeader::where("tender_number", $tender_number)->where("vendor_code", $vcode)->first();
        if (empty($po_header)) {
            DB::table('po_header')->insert($arr_item);
        } else {
            PoHeader::where("tender_number", $tender_number)->where("vendor_code", $vcode)
                ->update($arr_item);
        }
        $po_list = PurchaseOrder::where("tender_number", $tender_number)->where("vendor_code", $vcode)->first();
        if ($arr_text1["header_text"] != "") {
            //$arr_text = explode("\n", str_replace("\r", "", $arr_text1["header_text"]));
            $arr_text = str_replace("\r\n", "_*_", $arr_text1["header_text"]);
            $arr_text = explode('_*_', $arr_text);

            $arr_datas = array();
            $counter = 0;
            foreach ($arr_text as $value) {
                $counter++;
                $arr_data = array();
                $arr_data["tender_number"] = $tender_number;
                $arr_data['TEXT_ID'] = 'B01';
                $arr_data['TEXT_ID_DESC'] = 'Header text';
                $arr_data['TEXT_FORM'] = $counter < (count($arr_text)) ? '*' : '';
                $arr_data["TEXT_LINE"] = $value;
                $arr_data["po_number"] = isset($po_list->eproc_po_status) ? isset($po_list->eproc_po_status) : '';
                $arr_datas[] = $arr_data;
            }
            DB::table("po_header_text")->where("tender_number", $tender_number)->delete();
            DB::table("po_header_text")->insert($arr_datas);
        }
        if ($arr_text2["term_of_payment"] != "") {
            //$arr_text = explode("\n", str_replace("\r", "", $arr_text2["term_of_payment"]));
            $arr_text = str_replace("\r\n", "_*_", $arr_text1["term_of_payment"]);
            $arr_text = explode('_*_', $arr_text);
            $arr_datas = array();
            $counter = 0;

            foreach ($arr_text as $value) {
                $counter++;
                $arr_data = array();
                $arr_data["tender_number"] = $tender_number;
                $arr_data['TEXT_ID'] = 'B01';
                $arr_data['TEXT_ID_DESC'] = 'Payterm text';
                $arr_data['TEXT_FORM'] = $counter < (count($arr_text)) ? '*' : '';
                $arr_data["TEXT_LINE"] = $value;
                $arr_data["po_number"] = isset($po_list->eproc_po_status) ? isset($po_list->eproc_po_status) : '';
                $arr_datas[] = $arr_data;
            }
            DB::table("po_header_term_payment_text")->where("tender_number", $tender_number)->delete();
            DB::table("po_header_term_payment_text")->insert($arr_datas);
        }
    }
    public function savePoDetail($tender, $vcode, $type, $params)
    {
        if (count($params["detail"]) > 0) {
            $table = "po_items";
            $dt_items = DB::table("po_items")->where("tender_number", $tender->tender_number)->first();
            if (empty($dt_items)) {
                $this->dataReplikasiByCondition("tender_items", "po_items", $tender);
            }
            //dd($params["detail"][0]["id"]);
            foreach ($params["detail"] as $key => $value) {
                if (isset($value["id"])) {
                    DB::table("po_items")->where("id", $value["id"])->update(["expected_delivery_date" => $value["val"]]);
                }
            }
        }
    }

    public function getHeaderTechnical($eproc_po_number, $tenderNumber,  $vendor_code)
    {
        $header_technical =  $this->getQryTechnical("po_header_technical_awarding", $eproc_po_number, $tenderNumber,  $vendor_code)->first();
        return $header_technical;
    }

    public function getHeaderCommercial($eproc_po_number, $tenderNumber,  $vendor_code)
    {
        $header_commrecial =  $this->getQryCommercial("po_header_commercial_awarding", $eproc_po_number, $tenderNumber,  $vendor_code)->first();
        return $header_commrecial;
    }

    public function getQryTechnical($tblName, $eproc_po_number, $tenderNumber,  $vendor_code, $itemId = null)
    {
        $qry = DB::table($tblName) //"po_header_technical_awarding"
            ->where("tender_number", $tenderNumber)
            ->where("vendor_code", $vendor_code)
            ->where("eproc_po_number", $eproc_po_number);
        if (isset($itemId)) {
            $qry->where("item_id", $itemId);
        }
        return $qry;
    }

    public function getQryCommercial($tableName, $eproc_po_number, $tenderNumber,  $vendor_code, $itemId = null)
    {
        $qry = DB::table($tableName) //"po_header_commercial_awarding"
            ->where("tender_number", $tenderNumber)
            ->where("vendor_code", $vendor_code)
            ->where("eproc_po_number", $eproc_po_number);
        if ($itemId != null) {
            $qry->where("item_id", $itemId);
        }
        return $qry;
    }

    // return ["po_item", "pr_number", "pr_line_number", "product_code", "product_name", "qty", "satuan", "est_unit_price", "overall_limit", "unit_price", "total", "mata_uang", "complience", "delivery_date"];
    public function getQueryItemSubmision($tbl_tender_items, $eproc_po_number, $tblTectdest, $tblComdest, $tenderNumber, $vcode, $techSubMethod, $comSubMethod)
    {
        $dt = DB::table(DB::raw($tbl_tender_items . " as tender_items"))
            ->select(
                "tender_items.item_category",
                "tender_items.id",
                "tender_items.item_id",
                DB::raw("tender_items.number as pr_number"),
                DB::raw("tender_items.line_number as pr_line_number"),
                "tender_items.product_code",
                DB::raw("tech.description as product_name"),
                DB::raw("tech.qty as qty"),
                DB::raw("tender_items.uom as satuan"),
                DB::raw("com.price_unit as unit_price"),
                DB::raw("com.currency_code as mata_uang"),
                DB::raw("0 as total"),
                DB::raw("tender_items.expected_delivery_date as delivery_date"),
                DB::raw("com.est_unit_price as est_unit_price"),
                DB::raw("com.overall_limit as overall_limit"),
                DB::raw("com.compliance as complience")
            );
        if ($tbl_tender_items == "po_items") {
            $dt = DB::table(DB::raw($tbl_tender_items . " as tender_items"))
                ->select(
                    "tender_items.item_category",
                    "tender_items.id",
                    "tender_items.item_id",
                    DB::raw("tender_items.number as pr_number"),
                    DB::raw("tender_items.po_item"),
                    DB::raw("tender_items.line_number as pr_line_number"),
                    "tender_items.product_code",
                    DB::raw("tech.description as product_name"),
                    DB::raw("tech.qty as qty"),
                    DB::raw("tender_items.uom as satuan"),
                    DB::raw("com.price_unit as unit_price"),
                    DB::raw("com.currency_code as mata_uang"),
                    DB::raw("0 as total"),
                    DB::raw("tender_items.expected_delivery_date as delivery_date"),
                    DB::raw("com.est_unit_price as est_unit_price"),
                    DB::raw("com.overall_limit as overall_limit"),
                    DB::raw("com.compliance as complience")
                );
        }
        $dt = $dt->join(
            DB::raw($tblTectdest . " as tech"),
            function ($join) {
                $join->on("tech.tender_number", "tender_items.tender_number")
                    ->on("tech.eproc_po_number", "tender_items.eproc_po_number")
                    ->on("tech.item_id", "tender_items.item_id");
            }
        )
            ->join(DB::raw($tblComdest . " as com"), function ($join) {
                $join->on("com.tender_number", "tender_items.tender_number")
                    ->on("com.eproc_po_number", "tender_items.eproc_po_number")
                    ->on("com.item_id", "tender_items.item_id");
            })
            ->where("tender_items.tender_number", $tenderNumber)
            ->where(DB::raw("tech.submission_method"), $techSubMethod)
            ->where(DB::raw("com.submission_method"), $comSubMethod)
            ->where(DB::raw("com.vendor_code"), $vcode)
            ->where("tender_items.vendor_code", $vcode)
            ->where("tender_items.eproc_po_number", $eproc_po_number)
            ->where(DB::raw("tech.vendor_code"), $vcode);
        return $dt;
    }

    public function submitToSap($params)
    {
        $success = true;
        if ($success) {
            $tender_number = $params["tender_number"];
            $tender = "";
            $pageType = "";
            $this->completeEvaluation($tender, "po_creation");
        }
    }

    public function completeEvaluation($tender, $pageType)
    {
        DB::beginTransaction();
        TenderWorkflow::where('tender_number', $tender->tender_number)
            ->where('page', $pageType)
            ->update(['is_done' => 1]);

        $pages = (new TenderWorkflowHelper())->getAllPages($tender);
        $idx = array_search($pageType, $pages);
        $next = '';
        if ($idx !== false) {
            $nextPage = $pages[$idx + 1];
            $next = route('tender.show', ['id' => $tender->id, 'type' => $nextPage]);
            // update tender parameter status
            $workflow = TenderWorkFlow::where('tender_number', $tender->tender_number)
                ->where('page', $nextPage)
                ->first();
            if (!is_null($workflow)) {
                $tender->status = $workflow->status;
                $tender->workflow_status = $workflow->workflow_status;
                $tender->workflow_values = $workflow->page; // .'-'.$params['action_type'];
                $tender->save();
            }
        }
        DB::commit();
    }

    public function sendEmail($tender, $params = null)
    {
        try {
            $tenderVendor = (new TenderVendorRepository)->findByTenderNumber($tender->tender_number);
            $teams = (new TenderEvaluatorRepository)->findByTenderNumber($tender->tender_number);
            $emailTeams = '';
            if ($teams->count() > 0) {
                $emailTeams = $teams->pluck('email')->toArray();
            }

            foreach ($tenderVendor as $vendor) {
                $mailTos = $vendor->pic_email;
                $paramsEmail = [
                    'mailtype' => 'tender_aanwijzing',
                    'subject' => 'INVITED: ' . __('tender.aanwijzing') . ' - ' . $tender->tender_number . ' ' . $tender->title,
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
                if (!empty($emailTeams)) {
                    $details['cc'] = array_merge($details['cc'], $emailTeams);
                }
                SendEmail::dispatch($details);
            }
        } catch (Exception $e) {
            Log::error($this->logName . '::sendEmail error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function replikasi()
    {
        // $this->dataReplikasi("tender_header_commercial", "po_header_commercial_awarding");
        // $this->dataReplikasi("tender_header_technical", "po_header_technical_awarding");
        // $this->dataReplikasi("tender_item_technical", "po_item_technical_awarding");
        // $this->dataReplikasi("tender_item_commercial", "po_item_commercial_awarding");

        //$this->dataReplikasi("tender_header_technical", "tender_header_technical_awarding");
        //$this->dataReplikasi("tender_header_commercial_awarding", "po_header_commercial_awarding");
        //$this->dataReplikasi("tender_item_technical", "tender_item_technical_awarding");
        //$this->dataReplikasi("tender_items", "po_items", "delete");
        // $this->dataReplikasi("tender_item_technical_awarding", "po_item_technical_awarding");
        // $this->dataReplikasi("tender_item_commercial_awarding", "po_item_commercial_awarding");
        // dd("selesai");
    }

    public function replikasiByCondition($tender, $vendorCode, $type)
    {
        try {
            DB::beginTransaction();
            $this->dataReplikasiByConditionValid("tender_items", "po_items", $tender, $vendorCode, $type);
            $this->dataReplikasiByConditionValid("tender_additional_costs", "po_additional_costs", $tender, $vendorCode, $type);
            $this->dataReplikasiByConditionValid("tender_header_commercial", "po_header_commercial_awarding", $tender, $vendorCode, $type);
            $this->dataReplikasiByConditionValid("tender_header_commercial", "po_header_commercial_awarding", $tender, $vendorCode, $type);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($this->logName . '::delete error : ' . $e->getMessage());
            throw $e;
        }
    }

    public function dataReplikasiByConditionValid($tableFrom, $tableDest, $tender, $vendorCode, $type)
    {
        $where = "where tender_number='" . $tender->tender_number . "' AND deleted_at is null";
        //if ($tableFrom=="po_additional_costs")
        // $sql = "delete from ".$tableDest." ".$where;
        // DB::delete($sql);
        $fields = Schema::getColumnListing($tableFrom);

        $str_fields = join(",", $fields);
        $sql = "select " . $str_fields . " from " . $tableFrom . " " . $where;
        $data = DB::select(DB::raw($sql));
        $arr_data = array();
        foreach ($data as $key => $value) {
            $arr_per_data = array();
            foreach ($value as $key1 => $value1) {
                $arr_per_data[$key1] = $value1;
            }
            $arr_data[] = $arr_per_data;
        }
        DB::table($tableDest)->insert($arr_data);
    }

    public function dataReplikasiByCondition($tableFrom, $tableDest, $tender)
    {
        $sql = "delete from " . $tableDest . " where tender_number='" . $tender->tender_number . "'";
        DB::delete($sql);
        $fields = Schema::getColumnListing($tableFrom);

        $str_fields = join(",", $fields);
        $sql = "select " . $str_fields . " from " . $tableFrom . " where tender_number='" . $tender->tender_number . "'";
        $data = DB::select(DB::raw($sql));
        $arr_data = array();
        foreach ($data as $key => $value) {
            $arr_per_data = array();
            foreach ($value as $key1 => $value1) {
                $arr_per_data[$key1] = $value1;
            }
            $arr_data[] = $arr_per_data;
        }
        DB::table($tableDest)->insert($arr_data);
    }


    public function dataReplikasi($tableFrom, $tableDest, $command = "select")
    {
        $sql = "delete from " . $tableDest;
        DB::delete($sql);
        if ($command == "delete") {
            dd("selesai");
        }
        $fields = Schema::getColumnListing($tableFrom);

        $str_fields = join(",", $fields);
        $sql = "select " . $str_fields . " from " . $tableFrom;
        $data = DB::select(DB::raw($sql));
        $arr_data = array();
        foreach ($data as $key => $value) {
            $arr_per_data = array();
            foreach ($value as $key1 => $value1) {
                $arr_per_data[$key1] = $value1;
                if ($tableFrom == "tender_header_commercial") {
                    unset($arr_per_data["action_status"]);
                } else if ($tableFrom == "tender_header_technical") {
                    unset($arr_per_data["action_status"]);
                } else if ($tableFrom == "tender_item_technical") {
                    unset($arr_per_data["action_status"]);
                } else if ($tableFrom == "tender_item_commercial") {
                    unset($arr_per_data["action_status"]);
                }
            }
            $arr_data[] = $arr_per_data;
        }

        DB::table($tableDest)->insert($arr_data);
    }
    public function sendData($po_list)
    {
        try{
            $vcode = $po_list->vendor_code;
            $eproc_po_number = $po_list->eproc_po_number;
            $tender_number = $po_list->tender_number;
            $vendorRepo = new VendorRepository();
            $tenderRepo = new TenderRepository();

            $po_header                      = DB::table("po_header")->where("tender_number", $tender_number)->where("vendor_code", $vcode)->where("eproc_po_number", $eproc_po_number)->first();

            //get details by po_header
            $tender                         = $tenderRepo->findTenderParameter(true)->where("tender_parameters.tender_number", $tender_number)->first();
            $vendor                         = $vendorRepo->getQueryVendor()->where('vendor_code', $vcode)->first();
            $po_header_commercial_awarding  = DB::table("po_header_commercial_awarding")->where("tender_number", $tender_number)->where("vendor_code", $vcode)->where("eproc_po_number", $eproc_po_number)->first();
            $po_header_technical_awarding   = DB::table("po_header_technical_awarding")->where("tender_number", $tender_number)->where("vendor_code", $vcode)->where("eproc_po_number", $eproc_po_number)->first();
            $po_header_text                 = DB::table("po_header_text")->where("tender_number", $tender_number)->where("eproc_po_number", $eproc_po_number)->get();
            $po_header_term_payment_text    = DB::table("po_header_term_payment_text")->where("tender_number", $tender_number)->where("eproc_po_number", $eproc_po_number)->get();
            $po_items      = DB::table("po_items")->select(
                DB::raw("po_items.*"),
                "po_item_technical_awarding.description",
                "po_item_technical_awarding.qty",
                "po_item_commercial_awarding.est_unit_price",
                "po_item_commercial_awarding.currency_code",
                DB::raw("po_item_commercial_awarding.price_unit as unit_price"),
                "po_item_commercial_awarding.overall_limit",
                DB::raw("0 as subtotal")
            )
                ->join("po_item_technical_awarding", function($join){
                    $join->on("po_item_technical_awarding.item_id", "po_items.item_id")
                    ->on("po_item_technical_awarding.eproc_po_number","po_items.eproc_po_number")
                    ->where(function($query){
                        $query->where("po_item_technical_awarding.compliance","<>",'no_quote')
                              ->orWhereNull("po_item_technical_awarding.compliance");
                    });
                })
                ->join("po_item_commercial_awarding", function($join){
                    $join->on("po_item_commercial_awarding.item_id", "po_items.item_id")
                    ->on("po_item_commercial_awarding.eproc_po_number","po_items.eproc_po_number")
                    ->where("po_item_commercial_awarding.compliance","<>",'no_quote');
                })
                ->where("po_items.tender_number", $tender_number)
                ->where("po_items.vendor_code", $vcode)
                ->where("po_items.eproc_po_number", $eproc_po_number)
                ->get();

            $txt_header_text = "";
            $your_ref = "";
            foreach ($po_header_text as $value) {
                $txt_header_text = $txt_header_text . $value->TEXT_LINE . " ";
                if (!empty($value->created_by)) {
                    $your_ref = $value->created_by;
                }
            }

            $txt_pay_text = "";
            foreach ($po_header_term_payment_text as $value) {
                $txt_pay_text = $txt_pay_text . $value->TEXT_LINE . " ";
            }


            $total_item = 0;
            $arr_items = array();
            $arr_details = array();
            $arr_conditems = array();
            $arr_textitems = array();
            foreach ($po_items as $key => $val) {
                // $harga = $val->qty * ($val->est_unit_price ?? 0 + $val->overall_limit ?? 0) / $val->unit_price;
                $harga = ($val->est_unit_price ?? 0) + ($val->overall_limit ?? 0);
                // Log::debug(['harga'=>$harga,'est_unit'=>$val->est_unit_price,'overall'=>$val->overall_limit]);
                // $sql = $this->setValueAdditionalCostTenderNumber($tender_number, $harga, "CT2", $val->item_id);
                // $harga_add = DB::select(DB::raw($sql))[0]->total;
                // $harga_baru = $harga + $harga_add;
                $harga_baru = $harga;
                // $val->subtotal = $harga_baru;
                $val->subtotal = ($val->currency_code == "IDR") ? round($harga_baru, 0) : round($harga_baru, 2);
                $total_item = $total_item + $val->subtotal;
                $arr_items[] = [
                    'RUN_ID' => $po_list->eproc_po_number,
                    'PO_ITEM' => $val->po_item,
                    'ITEM_CAT' => $val->item_category,
                    'ACCTASSCAT' => $val->account_assignment,
                    'MATERIAL' => $val->product_code,
                    'SHORT_TEXT' => $val->description,
                    'DELIVERY_DATE' => Carbon::createFromFormat('Y-m-d H:i:s', $val->expected_delivery_date)->format('d.m.Y'),
                    'MATL_GROUP' => $val->product_group_code,
                    'PLANT' => $val->plant,
                    'STGE_LOC' => $val->storage_loc,
                    'QUANTITY' => $val->qty,
                    'NET_PRICE' => $val->item_category==0 ? $val->subtotal : '',
                    'LIMIT_AMOUNT' => $val->item_category==9 ? $val->subtotal : '',
                    'PRICE_UNIT' => intval($val->unit_price),
                    'PREQ_NAME' => $val->requisitioner,
                    'TRACKINGNO' => $val->tracking_number,
                    'TAX_CODE' => 'V1',
                    'GR_BASEDIV' => 'X',
                    'GR_NON_VAL' => '',
                    'PO_UNIT' => $val->uom,
                    'GL_ACCOUNT' => $val->gl_account,
                    'COST_CODE' => $val->cost_code,
                    'PREQ_NO' => $val->number,
                    'PREQ_ITEM' => $val->line_number
                ];

                if ($val->item_category == 9) {
                    //set service items
                    $services = DB::table('po_item_detail_services')
                        ->where("tender_number", $tender_number)
                        ->where("vendor_code", $vcode)
                        ->where("eproc_po_number", $eproc_po_number)
                        ->where("item_id", $val->item_id)
                        ->get();
                    foreach ($services as $service) {
                        $arr_details[] = [
                            'RUN_ID' => $eproc_po_number,
                            'PO_ITEM' => $val->po_item,
                            'EXT_LINE' => $service->EXTROW,
                            'SERVICE' => $service->SRVPOS,
                            'QUANTITY' => $service->MENGE,
                            'GR_PRICE' => $val->item_category == 9 ? $val->subtotal : '',
                            'PRICE_UNIT' => intval($val->unit_price),
                            'BASE_UOM' => $service->MEINS,
                            'LIMIT' => '',
                            'GL_ACCOUNT' => $val->gl_account,
                            'COST_CODE' => $val->cost_code,
                            'USERF1_NUM' => '',
                            'USERF2_NUM' => '',
                        ];
                    }
                }
                //set conditional type item
                $condItems = DB::table('po_additional_costs')
                    ->where("tender_number", $tender_number)
                    ->where("conditional_type", 'CT2')
                    ->where("vendor_code", $vcode)
                    ->where("eproc_po_number", $eproc_po_number)
                    ->where("item_id", $val->item_id)
                    ->orderBy('id')
                    ->get();
                $i = 1;
                foreach ($condItems as $condItem) {
                    $arr_conditems[] = [
                        'RUN_ID' => $eproc_po_number,
                        'PO_ITEM' => $val->po_item,
                        'COND_COUNT' => $i++,
                        'COND_TYPE' => $condItem->conditional_code,
                        'COND_VALUE' => ($condItem->percentage ?? 0) + ($condItem->value ?? 0),
                        'CURRENCY' => $po_header_commercial_awarding->currency_code
                    ];
                }
                //set item text
                $itemTexts = DB::table('po_item_text')
                    ->where("tender_number", $tender_number)
                    ->where("vendor_code", $vcode)
                    ->where("eproc_po_number", $eproc_po_number)
                    ->where("item_id", $val->item_id)
                    ->orderBy('id')
                    ->get();
                foreach ($itemTexts as $itemText) {
                    $arr_textitems[] = [
                        'RUN_ID' => $eproc_po_number,
                        'PO_ITEM' => $val->po_item,
                        'TEXT_ID' => "F01",
                        'TEXT_FORM' => $itemText->TEXT_FORM,
                        'TEXT_LINE' => $itemText->TEXT_LINE,
                    ];
                }
            }
            // $sql_total = $this->setHeaderValueAdditionalCostEprocPoNumber($eproc_po_number, $tender_number, $vcode, $total_item);
            // $total = DB::select(DB::raw($sql_total))[0]->total;
            // if (empty($total)){
            // $total = $total_item;
            // }
            // $po_item_text = DB::table("po_item_text")->where("tender_number", $tender_number)->where("vendor_code", $vcode)->where("eproc_po_number", $eproc_po_number)->get();
            // $po_tax_codes = DB::table("po_tax_codes")->where("tender_number", $tender_number)->where("vendor_code", $vcode)->where("eproc_po_number", $eproc_po_number)->get();

            $condHeads = DB::table('po_additional_costs')
                ->where("tender_number", $tender_number)
                ->where("conditional_type", 'CT1')
                ->where("vendor_code", $vcode)
                ->where("eproc_po_number", $eproc_po_number)
                ->orderBy('id')
                ->get();
            $arr_condheads = [];
            $i = 1;
            foreach ($condHeads as $condHead) {
                $arr_condheads[] = [
                    'RUN_ID' => $eproc_po_number,
                    'COND_COUNT' => $i++,
                    'COND_TYPE' => $condHead->conditional_code,
                    'COND_VALUE' => ($condHead->percentage ?? 0) + ($condHead->value ?? 0),
                    'CURRENCY' => isset($po_header_commercial_awarding->currency_code) ? $po_header_commercial_awarding->currency_code : ""
                ];
            }

            $sap = new SapConnector();
            $tkdn_overall = "NO";
            if ($tender->tkdn_option==1){
                // $tkdn_overall1 = $po_header_technical_awarding->tkdn_percentage;
                $tkdn_overall = ($po_header_technical_awarding->tkdn_percentage ?? 0) <= 0 ? "" : $po_header_technical_awarding->tkdn_percentage;
            }
            $arr_data =   [
                'T_HEADER' => [
                    'item' =>  [
                        'RUN_ID' => $eproc_po_number,
                        'DOC_TYPE' => $po_header->document_type ?? "",
                        'DOC_DATE' => isset($po_header->document_date) ? Carbon::createFromFormat('Y-m-d H:i:s', $po_header->document_date)->format('d.m.Y') : date('d.m.Y'),
                        'PURCH_ORG' => $tender->org_code ?? "",
                        'PUR_GROUP' => $tender->group_code ?? "",
                        'VENDOR' => $vendor->sap_vendor_code ?? "",
                        'CURRENCY' => isset($po_header_commercial_awarding->currency_code) ? $po_header_commercial_awarding->currency_code : "",
                        'SALES_PERS' => '',
                        'TELEPHONE' => '',
                        'YOUR_REF' => $your_ref, //ini created by yang mana
                        'OUR_REF' => isset($vendor->vendor_code) ? $vendor->vendor_code : "",
                        'PMNTTRMS' => 'Z030',
                        'INCOTERM1' => $po_header_commercial_awarding->incoterm,
                        'INCOTERMS2L' => $po_header_commercial_awarding->incoterm_location,
                        'DOWNPAY_TYPE' => '',
                        'DOWNPAY_PERCENT' => '',
                        'DOWNPAY_AMOUNT' => '',
                        'DOWNPAY_DUEDATE' => '',
                        'RETENTION_TYPE' => '',
                        'RETENTION_PERCENTAGE' => '',
                        'TRANS_VIA' => '',
                        'MODA_TRANS' => '',
                        'TKDN_OVERALL' => $tkdn_overall,                    ]
                ],
                'T_ITEM' => [
                    'item' => $arr_items
                ],
                'T_TEXTHEAD' => [
                    'item' => [
                        [
                            'RUN_ID' => $eproc_po_number,
                            'TEXT_ID' => 'F01',
                            'TEXT_FORM' => '*',
                            'TEXT_LINE' => $txt_header_text,
                        ],
                        [
                            'RUN_ID' => $eproc_po_number,
                            'TEXT_ID' => 'F07',
                            'TEXT_FORM' => '*',
                            'TEXT_LINE' => $txt_pay_text,
                        ],
                    ],
                ],
            ];
            if (count($arr_condheads) > 0) {
                $arr_data['T_CONDHEAD']['item'] = $arr_condheads;
            }
            if (count($arr_conditems) > 0) {
                $arr_data['T_CONDITEM']['item'] = $arr_conditems;
            }
            if (count($arr_details) > 0) {
                $arr_data['T_DETAIL']['item'] = $arr_details;
            }
            if (count($arr_textitems) > 0) {
                $arr_data['T_TEXTITEM']['item'] = $arr_textitems;
            }

            // dd(array(
            //     "arr_data"=>$arr_data,
            //     "po_header"=>$po_header,
            //     "po_items"=>$po_items,
            //     'comm'=>$po_header_commercial_awarding,
            //     'Tech'=>$po_header_technical_awarding,
            //     "vendor"=>$vendor,
            //     "tender"=>$tender,
            //     "item_text" => $po_item_text,
            //     "item_tax_codes"=> $po_tax_codes
            // ));

            if ($vendor->sap_vendor_code) {
                //dd($tkdn_overall1, $tkdn_overall, $arr_data);
                $result = $sap->call(
                    'create_po',
                    $arr_data
                );
                $this->poToSapListLog("============== REQUEST TO SAP (Create Update BP) ===============");
                $this->poToSapListLog($sap->requestMessage);
                $this->poToSapListLog("============== SAP RESPONSE (Create Update BP) ===============");
                $this->poToSapListLog($sap->responseMessage);

                $jin = json_encode($arr_data);
                $jout = json_encode($result);

                $this->poToSapListLog("================input start : " . $tender_number . ", vendor_code: " . $vcode . ". eproc_po_number: " . $eproc_po_number . "================");
                $this->poToSapListLog($jin);
                $this->poToSapListLog("================output ================");
                $this->poToSapListLog($jout);
                $sap_po_number = "";
                if (isset($result["RETURN"]["ITEM"])) {
                    $messages = [];
                    $success = false;
                    if(isset($result["RETURN"]["ITEM"]["TYPE"])){
                        //only 1 return item
                        if ($result["RETURN"]["ITEM"]["TYPE"] == 'S') {
                            $success = true;
                            $sap_po_number = $result["RETURN"]["ITEM"]["PO_NUMBER"];
                            $messages[] = '['.$result["RETURN"]["ITEM"]["TYPE"].']: '.$result["RETURN"]["ITEM"]['MESSAGE'] . "\n";
                        }
                    }else{
                        //multiple return items
                        if ($result["RETURN"]["ITEM"][0]["TYPE"] == 'S') {
                            $success = true;
                            $sap_po_number = $result["RETURN"]["ITEM"][0]["PO_NUMBER"];
                        }
                        foreach ($result["RETURN"]["ITEM"] as $value) {
                            $messages[] = '['.$value['TYPE'].']: '.$value['MESSAGE'] . "\n";
                        }
                    }
                } else {
                    $success = false;
                    $messages = ["Error processing WSDL"];
                }
                $this->poToSapListLog("================message results ================");
                $this->poToSapListLog(implode("", $messages));
                $this->poToSapListLog("================message end ================");
            } else {
                $success = false;
                $sap_po_number = "";
                $messages = ["Vendor didn't have SAP Vendor Code."];
            }

            if ($success) {
                Log::info("update $eproc_po_number to $sap_po_number");
                $res = DB::table('po_list')->where('eproc_po_number', $eproc_po_number)
                    ->update([
                        'sap_po_number' => $sap_po_number,
                        'eproc_po_status' => 'submit',
                        'sap_message' => implode("", $messages),
                        'updated_at' => now()
                    ]);
            } else {
                Log::info("fail save $eproc_po_number to sap");
                Log::info(implode("", $messages));
                $res = DB::table('po_list')->where('eproc_po_number', $eproc_po_number)
                    ->update([
                        'sap_message' => implode("", $messages),
                        'updated_at' => now()
                    ]);
            }

            return [
                'status' => $success,
                'eproc_po_number' => $eproc_po_number,
                'sap_po_number' => $sap_po_number,
                'vendor_number' => $vendor->vendor_code,
                'messages' => $messages
            ];
        } catch (Exception $e) {
            // Log::error($e->getMessage());
            Log::error($e);
        }
    }

    public function commandSap()
    {
        $lists = DB::table("po_list")->where(DB::raw('trim(sap_po_number)', ""))->get();
        foreach ($lists as $value) {
            $this->sendData($value);
        }
    }

    public function sapSend($tender_number, $type)
    {
        $lists = DB::table("po_list")
            ->where("tender_number", $tender_number)
            ->where(DB::raw('trim(sap_po_number)', ""))
            ->whereNull("deleted_at")
            ->get();
        $results = [];
        foreach ($lists as $value) {
            $results[] = $this->sendData($value);
        }

        $success = false;
        $message = "";
        //success if there is one or more po created.
        foreach ($results as $result) {
            $success = $success || $result['status'];
            $message .= "====Result for " . $result['eproc_po_number'] . ":\n" . implode("", $result['messages']) . "===\n";
        }

        return ['status' => $success, 'message' => $message, 'details' => $results];

        // if (isset($result['RETURN'])) {
        //     if (!isset($result['RETURN']['ITEM']['TYPE'])) {
        //         $status = true;
        //         foreach ($result['RETURN']['ITEM'] as $item) {
        //             switch ($item['TYPE']) {
        //                 case 'S':
        //                     $status = $status && true;
        //                     break; //success
        //                 case 'E':
        //                     $status = $status && false;
        //                     break; //error
        //                 case 'W':
        //                     $status = $status && true;
        //                     break; //warning
        //                 case 'I':
        //                     $status = $status && true;
        //                     break; //info
        //                 case 'A':
        //                     $status = $status && false;
        //                     break; //abort
        //             }
        //         }
        //     } else {
        //         switch ($result['RETURN']['ITEM']['TYPE']) {
        //             case 'S':
        //                 $status = true;
        //                 break; //success
        //             case 'E':
        //                 $status = true;
        //                 break; //error
        //             case 'W':
        //                 $status = true;
        //                 break; //warning
        //             case 'I':
        //                 $status = true;
        //                 break; //info
        //             case 'A':
        //                 $status = false;
        //                 break; //abort
        //         }
        //     }
        //     $message = "";
        //     if (!isset($result['RETURN']['ITEM']['MESSAGE'])) {
        //         foreach ($result['RETURN']['ITEM'] as $item) {
        //             $message .= $item['MESSAGE'] . "\n";
        //         }
        //     } else {
        //         $message = $result['RETURN']['ITEM']['MESSAGE'];
        //     }
        //     $this->poToSapListLog("================message end ================");
        //     Log::debug($message);
        //     return ['status' => $status, 'message' => $message];
        // } else {
        //     return ['status' => false, 'message' => "Network Connection Error. Please contact administrator."];
        // }
    }
}
