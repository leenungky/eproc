<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ref\RefMaterialGroup;
use App\Models\Ref\RefScopeOfSupply;
use App\Models\Ref\RefScopeMaterial;

class RefScopeOfSupplySeeder extends Seeder{

    public function run(){
        $data = [
            ['CN01','FUEL'],
            ['CN02','GASES'],
            ['CN03','PACKING'],
            ['CN04','TOOLS CONSUMABLE'],
            ['CN05','WELDING CONSUMABLES'],
            ['CN06','CIVIL CONSUMABLES'],
            ['MT01','PLATES'],
            ['MT02','PIPES'],
            ['MT03','LINE PIPES'],
            ['MT04','TUBES'],
            ['MT05','FITTINGS'],
            ['MT06','FLANGES'],
            ['MT07','STRUCTURAL'],
            ['MT08','GASKETS'],
            ['MT09','CHEMICALS'],
            ['MT10','VALVES'],
            ['MT11','INSULATION MATERIAL'],
            ['MT12','PACKAGE'],
            ['MT13','FLEXIBLE PIPE SYSTEM'],
            ['MT14','MATTRESSES & SAND BA'],
            ['MT15','UMBILICAL SYSTEM'],
            ['MT16','SUBSEA MISCELLANEOUS'],
            ['MT17','SPECIAL MATERIALS'],
            ['MT18','FORGINGS'],
            ['MT19','LINE PIPE COATING'],
            ['MT20','ELECTRICAL BULK MATE'],
            ['MT21','SUBSEA ISOLATION SYS'],
            ['MT22','INSTRUMENTATION MATE'],
            ['PA01','GENERAL PARTS'],
            ['S0001','MARINE VESSEL CHARTE'],
            ['S0002','ENGINEERING'],
            ['S0003','TEST & CALIBRATION'],
            ['S0004','INSPECTION'],
            ['S0005','SURVEY'],
            ['S0006','TRAINING & CERTIFICA'],
            ['S0007','IT'],
            ['S0008','OFFICE, ACCOMODATION'],
            ['S0009','MAINTENANCE & REPAIR'],
            ['S0010','HSSE SERVICES'],
            ['S0011','RENTAL EQUIPMENT'],
            ['S0012','FABRICATION'],
            ['S0013','CONSTRUCTION & INSTA'],
            ['S0014','FORMALITIES'],
            ['S0015','LOGISTICS'],
            ['S0016','PROFESSIONAL SERVICE'],
            ['S0017','OTHER SERVICES'],
            ['TE01','GENERAL TOOLS'],
            ['TE02','WELDING EQUIPMENT'],
            ['TE03','SAFETY EQUIPMENT & A'],
            ['TE04','E&I TOOLS'],
            ['TE05','INSPECTION EQUIPMENT'],
            ['TE06','LIFTING MATERIAL HAN'],
            ['TE07','PAINTING EQUIPMENT'],
            ['TE08','STORAGE'],
            ['TE09','MACHINERY'],
            ['TE10','SCAFFOLDING'],
            ['TE11','SAFETY UNIFORM'],
            ['TF01','IT'],
            ['TF02','OFFICE'],
            ['TF03','UNIFORM'],
            ['VH01','VEHICLES'],
            ['VH02','HEAVY EQUIPMENT'],
        ];
        $input = [];
        $inputScopeMaterial = [];
        foreach($data as $row){
            $input[] = ['id'=>$row[0],'description'=>$row[1],'created_at'=>now()];
            $inputScopeMaterial[] = ['scope_id'=>$row[0],'material_group_id'=>$row[0]];
        }

        try {
            DB::beginTransaction();
            DB::statement('TRUNCATE TABLE ref_material_groups CASCADE;');
            DB::statement('TRUNCATE TABLE ref_scope_of_supplies CASCADE;');
            DB::statement('TRUNCATE TABLE ref_scope_materials CASCADE;');
            RefMaterialGroup::insert($input);
            RefScopeOfSupply::insert($input);
            RefScopeMaterial::insert($inputScopeMaterial);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }

    }
}