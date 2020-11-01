<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\RefProvince;
use App\RefCity;
use App\RefSubDistrict;

class ReferenceController extends Controller
{
    //
    public function __construct() {
    }
    
    public function get_province(Request $request){
        $country_code = $request->countrycode;
        $search = strtolower($request->search);
        $column = 'region_description';
        return RefProvince::select('region_code as id','region_description as text')
                ->where('country_code', $country_code)
                ->whereRaw("LOWER($column) LIKE '%$search%'")
//                ->where('LOWER(region_description)', 'LIKE', '%' . strtolower($search) . '%')
                ->withTrashed(false)->orderby('region_description', 'ASC')->get();
    }
    
    public function get_city(Request $request){
        $country_code = $request->countrycode;
        $region_code = $request->regioncode;
        $search = strtolower($request->search);
        $column = 'city_description';
        return RefCity::select('city_code as id','city_description as text')
                ->where('country_code', $country_code)
                ->where('region_code', $region_code)
                ->whereRaw("LOWER($column) LIKE '%$search%'")
                ->withTrashed(false)->orderby('city_description', 'ASC')->get();
    }
    
    public function get_subdistrict(Request $request){
        $country_code = $request->countrycode;
        $region_code = $request->regioncode;
        $city_code = $request->citycode;
        $search = strtolower($request->search);
        $column = 'district_description';
        return RefSubDistrict::select('district_code as id','district_description as text')
                ->where('country_code', $country_code)
                ->where('region_code', $region_code)
                ->where('city_code', $city_code)
                ->whereRaw("LOWER($column) LIKE '%$search%'")
                ->withTrashed(false)->orderby('district_description', 'ASC')->get();
    }
}
