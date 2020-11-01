<?php

use Illuminate\Support\Facades\Route;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

// Route::get('/', function () {
//     return view('index');
// });

Route::get('/', 'AnnouncementController@open')->name('main');
// Route::get('/', 'PagesController@index')->name('main');
Route::get('/contact', 'PagesController@contact');
Route::get('/{page}/{name}', 'PagesController@page')
      ->where('page', '(guide|procedure)');
Route::get('/sap/test', 'PagesController@test')->name('test');
Route::get('/sap/email', 'PagesController@testEmail')->name('testemail');
Route::get('/sap/bank', 'PagesController@testBank')->name('testbank');
Route::get('/sap/legacy', 'LegacyController@testLegacy')->name('testlegacy');
Route::get('/legacy', 'LegacyController@legacy')->name('legacy');
Route::get('/logoutuser', 'Auth\LoginController@logout')->name('logoutuser');


Route::get('/page/manage', 'PagesController@managePage')->name('managepage');
Route::post('/page/manage', 'PagesController@storePage')->name('storepage');
Route::post('/page/data', 'PagesController@datatable_serverside')->name('listpage');
Route::delete('/page/manage/{id}', 'PagesController@deletePage')->name('deletepage');

Route::prefix('/pdf')->name('pdf.')->group(function(){
  Route::get('/report/profile/{id?}', 'ReportController@pdf_profile')->name('report');
});

Route::prefix('/excel')->name('excel.')->group(function(){
  Route::get('/report', 'ReportController@excel')->name('report');
  Route::get('/applicants', 'ReportController@applicants_excel')->name('applicants');
  Route::get('/candidates', 'ReportController@candidates_excel')->name('candidates');
  Route::get('/vendors', 'ReportController@vendors_excel')->name('vendors');
  Route::get('/sanctions', 'ReportController@sanctions_excel')->name('sanctions');
  Route::get('/evaluationlist', 'ReportController@evaluationlist_excel')->name('evaluationlist');
  Route::get('/evaluationform/{id}', 'ReportController@evaluationform_excel')->name('evaluationform');
});

// ================ Announcement ROUTES ================
Route::prefix('/announcement')->name('announcement.')->group(function(){
    Route::get('/open', 'AnnouncementController@open')->name('open');
    Route::get('/tender', 'AnnouncementController@tender')->name('tender');
    Route::get('/print/{id}', 'TenderPrintController@printWorkPlane')->name('print-tender');
    Route::get('/tender-followed', 'AnnouncementController@tenderFollowed')->name('tenderFollowed');
    Route::post('/data-table/{type}', 'AnnouncementController@openDatatable')->name('open.datatable');
    Route::post('/save/{action}', 'AnnouncementController@saveTenderVendor')->name('saveVendorAction');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/registration', 'ApplicantController@registration')->name('registration');
Route::post('/change-password', 'PersonnelController@changePassword')->name('change-password');

Route::get('/translation/manage', 'TranslationController@manage')->name('managelocale');
Route::post('/translation/manage', 'TranslationController@storeLocale')->name('storelocale');
Route::get('/locale/{locale}', 'TranslationController@changeLocale')->name('setlocale');

// ================ ADMIN ROUTES ================
Route::prefix('/admin')->name('admin.')->group(function(){
  Route::get('/vendors', 'VendorController@view_vendors')->name('vendors');
  Route::get('/candidates', 'CandidateController@view_candidates')->name('candidates');
  Route::get('/applicants', 'ApplicantController@view_applicants')->name('applicants');
});

// ================ ADMIN ROUTES ================
Route::prefix('/reference')->name('reference.')->group(function(){
  Route::post('/country', 'ReferenceController@get_country')->name('country');
  Route::post('/province', 'ReferenceController@get_province')->name('province');
  Route::post('/city', 'ReferenceController@get_city')->name('city');
  Route::post('/subdistrict', 'ReferenceController@get_subdistrict')->name('subdistrict');
});

// ================= APPLICANTS ROUTES ==================
Route::prefix('/applicants')->name('applicant.')->group(function(){
    Route::post('/submission', 'ApplicantController@submit_form')->name('submission');
    Route::post('/data', 'ApplicantController@datatable_list_applicants')->name('data');
    Route::post("/profile", "ApplicantController@show_profile")->name('profile');
    Route::post("/approval", "ApplicantController@approval")->name("approval");
});

// ================= CANDIDATES ROUTES ==================
Route::prefix('/candidates')->name('candidate.')->group(function(){
  Route::get('/', 'CandidateController@index')->name('list');
  Route::post('/data', 'CandidateController@datatable_list_candidates')->name('data');
  Route::get("/profile/{vendorid}", "CandidateController@show_profiles")->name("profile");
  // Route::post("/profile", "CandidateController@show_profiles")->name("profile");
  Route::get("/profile/{vendorid}/{submenu}", "CandidateController@view_detail_profiles")->name("profile-detail");
  Route::post("/approval", "CandidateController@approval")->name("approval");
  // Route::get("/sap/test/{id}", "CandidateController@sap_create_change_vendor")->name("test-sap");
});

// ================== VENDORS ROUTES ====================
Route::prefix('/vendor')->name('profile.')->group(function(){
  Route::get('/profile', 'VendorProfileController@view_show_profile')->name('show');
  Route::get('/edit-profile/{submenu?}', 'VendorProfileController@view_edit_profile')->name('edit');
  Route::get('/find-data-{type}', 'VendorProfileController@find_profile')->name('find-data');
  Route::post('/send-submission}', 'VendorProfileController@send_submission')->name('send-submission');
  Route::post('/store-{type}', 'VendorProfileController@create_profile')->name('create');
  Route::post('/update-{type}', 'VendorProfileController@update_profile')->name('update');
  Route::delete('/revert-{type}', 'VendorProfileController@revert_profile')->name('revert');
  Route::delete('/revertall-{type}/{id?}', 'VendorProfileController@revertall_profile')->name('revertall');
  Route::delete('/finishall-{type}/{id?}', 'VendorProfileController@finishall_profile')->name('finishall');
  Route::get('/approve-profile/{submenu?}', 'VendorProfileController@view_edit_profile')->name('approve-profile');
});

Route::prefix('/vendors')->name('vendor.')->group(function(){
  Route::get('/', 'VendorController@index')->name('list');
  Route::post('/data', 'VendorController@datatable_list_vendors')->name('data');
  Route::get("/profile/{vendorid}", "CandidateController@show_profiles")->name("profile");
  Route::get("/profile/{vendorid}/{submenu}", "CandidateController@view_detail_profiles")->name("profile-detail");
  Route::post("/approval", "VendorController@approval")->name("approval");
});

Route::prefix('/vendor')->name('vendor.')->group(function(){
  Route::post('/data', 'VendorController@datatable_list_vendors')->name('data');
  Route::post("/show-candidate", "CandidateController@show_profiles")->name("show");
  Route::get('/input-sanction', 'VendorSanctionController@sanction_input')->name('sanction_input');
  Route::get('/vendor-option-list', 'VendorSanctionController@vendor_option_list')->name('vendor_option_list');
  Route::get('/sanction', 'VendorSanctionController@sanction_view')->name('sanction');
  Route::get('/sanction/{id}', 'VendorSanctionController@sanction_data')->name('sanction_data');
  Route::get('/sanction/detail/{id}', 'VendorSanctionController@sanction_detail')->name('sanction_detail');
  Route::post('/sanction/detail/{id}', 'VendorSanctionController@sanction_patch')->name('sanction_patch');
  Route::post('/sanction', 'VendorSanctionController@sanction_store')->name('sanction_store');
  Route::post('/sanction/data', 'VendorSanctionController@sanction_data_list')->name('sanction_data_list');
  Route::post('/sanction/history/{id?}', 'VendorSanctionController@sanction_history_list')->name('sanction_history_list');
  Route::post('/sanction/comment-history/{id?}', 'VendorSanctionController@sanction_comment_history_list')->name('sanction_comment_history_list');
  Route::post('/sanction/current/{id?}', 'VendorSanctionController@sanction_current_list')->name('sanction_current_list');

  //Route::post('/sanction/approval', 'VendorSanctionController@approval_sanction_old')->name('sanction_approval');

  Route::get('/user-management', 'VendorController@view_user_management')->name('usermanagement');
});
Route::prefix('/vendor/evaluation')->name('vendor.evaluation.')->group(function(){
  Route::get('/config/score', 'VendorEvaluationController@score')->name('score');
  Route::post('/config/score/data', 'VendorEvaluationController@score_data')->name('score_data');
  Route::post('/config/score', 'VendorEvaluationController@score_store')->name('score_store');
  Route::delete('/config/score/{id}', 'VendorEvaluationController@score_delete')->name('score_delete');

  Route::get('/config/criteria-group', 'VendorEvaluationController@criteria_group')->name('criteria_group');
  Route::get('/config/criteria-group-json', 'VendorEvaluationController@criteria_group_json')->name('criteria_group_json');
  Route::post('/config/criteria-group/data', 'VendorEvaluationController@criteria_group_data')->name('criteria_group_data');
  Route::post('/config/criteria-group', 'VendorEvaluationController@criteria_group_store')->name('criteria_group_store');
  Route::delete('/config/criteria-group/{id}', 'VendorEvaluationController@criteria_group_delete')->name('criteria_group_delete');

  Route::get('/config/criteria', 'VendorEvaluationController@criteria')->name('criteria');
  Route::post('/config/criteria/data', 'VendorEvaluationController@criteria_data')->name('criteria_data');
  Route::post('/config/criteria', 'VendorEvaluationController@criteria_store')->name('criteria_store');
  Route::delete('/config/criteria/{id}', 'VendorEvaluationController@criteria_delete')->name('criteria_delete');

  Route::get('/parameter/list', 'VendorEvaluationController@evaluation')->name('evaluation');
  Route::get('/parameter/{id}/{type?}', 'VendorEvaluationController@evaluation_detail')->name('evaluation_detail');
  Route::get('/parameter/{id}/form/{subid}', 'VendorEvaluationController@evaluation_detail_form_data')->name('evaluation_detail_form_data');
  Route::post('/parameter', 'VendorEvaluationController@evaluation_store')->name('evaluation_store');
  Route::post('/parameter/data', 'VendorEvaluationController@evaluation_data')->name('evaluation_data');
  Route::post('/parameter/finish', 'VendorEvaluationController@evaluation_store_finish')->name('evaluation_store_finish');
  Route::post('/parameter/{id}/submit', 'VendorEvaluationController@evaluation_detail_submit')->name('evaluation_detail_submit');
  Route::post('/parameter/{id}/approval', 'VendorEvaluationController@evaluation_detail_approval')->name('evaluation_detail_approval');
  Route::post('/parameter/{id}/{type}', 'VendorEvaluationController@evaluation_detail_store')->name('evaluation_detail_store');
  Route::post('/parameter/{id}/{type}/data', 'VendorEvaluationController@evaluation_detail_data')->name('evaluation_detail_data');
  Route::post('/parameter/{id}/{type}/finish', 'VendorEvaluationController@evaluation_detail_store_finish')->name('evaluation_detail_store_finish');
  Route::delete('/parameter/{id}/{type}/{subid}', 'VendorEvaluationController@evaluation_detail_delete')->name('evaluation_detail_delete');
  Route::patch('/parameter/{id}/{type}/up/{subid}', 'VendorEvaluationController@evaluation_detail_up')->name('evaluation_detail_up');
  Route::patch('/parameter/{id}/{type}/down/{subid}', 'VendorEvaluationController@evaluation_detail_down')->name('evaluation_detail_down');
});

// ================ BUYER ROUTES ==================
Route::prefix('/purchase-requisition')->name('pr.')->group(function(){
  Route::get('/', 'PurchaseRequisitionController@index')->name('list');
  Route::post('/data', 'PurchaseRequisitionController@datatable_serverside')->name('data');
  Route::delete('/{id}', 'PurchaseRequisitionController@delete')->name('delete');
  Route::post('/syncSapData', 'PurchaseRequisitionController@syncSapData')->name('syncSapData');
});

Route::prefix('/tender')->name('tender.')->group(function(){
  Route::get('/', 'TenderController@index')->name('list');
  Route::post('/', 'TenderController@store')->name('store');
  Route::post('/draft', 'TenderController@storeDraft')->name('draft');
  Route::post('/data', 'TenderController@datatable_serverside')->name('data');
  Route::post('/data-vendor', 'TenderController@getDatatableVendor')->name('dataVendor');
  Route::get('/datatable/{id}/{type?}', 'TenderController@getDatatableItem')->name('dataItem');
  Route::get('/print/{id}/{type?}/{print?}', 'TenderPrintController@print')->name('print');

  Route::get('/{id}/{type?}', 'TenderController@show')->name('show');
  Route::get('/{id}/{type?}/{action?}/{param_id?}', 'TenderController@show')->name('show');
  Route::post('/{id}/{type?}', 'TenderController@save')->name('save');
  Route::post('/{id}/{type?}/{action?}', 'TenderController@save')->name('save');
  Route::delete('/{id}', 'TenderController@delete')->name('delete');
  Route::delete('/{id}/{type}/{itemid}', 'TenderController@deleteItem')->name('delete-item');
});

Route::prefix('/admin/personnels')->name('personnel.')->group(function(){
  Route::get('/', 'PersonnelController@index')->name('list');
  Route::get('/usermanagement', 'PersonnelController@userManagement')->name('usermanagement');
  Route::post('/', 'PersonnelController@store')->name('store');
  Route::post('/data', 'PersonnelController@datatable_serverside')->name('data');
  Route::post('/usermanagement', 'PersonnelController@changeUserAccount')->name('change-account');
  Route::get('/{id}', 'PersonnelController@show')->name('show');
  Route::post('/{id}', 'PersonnelController@edit')->name('edit');
  Route::post('/{id}/password', 'PersonnelController@adminChangePassword')->name('admin-change-password');
  Route::delete('/{id}', 'PersonnelController@delete')->name('delete');
});
Route::prefix('/admin/roles')->name('role.')->group(function(){
  Route::get('/', 'RoleController@index')->name('list');
  Route::post('/', 'RoleController@store')->name('store');
  Route::post('/data', 'RoleController@datatable_serverside')->name('data');
  Route::get('/{id}', 'RoleController@show')->name('show');
  Route::delete('/{id}', 'RoleController@delete')->name('delete');
});
Route::prefix('/admin/buyers')->name('buyer.')->group(function(){
  Route::get('/', 'BuyerController@index')->name('list');
  Route::post('/', 'BuyerController@store')->name('store');
  Route::post('/data', 'BuyerController@datatable_serverside')->name('data');
  Route::get('/{id}', 'BuyerController@show')->name('show');
  Route::patch('/{id}', 'BuyerController@edit')->name('edit');
  Route::delete('/{id}', 'BuyerController@delete')->name('delete');
});

Route::prefix('/schedule')->name('schedule.')->group(function(){
  Route::get('/test', 'SchedulerTesterController@index')->name('test');
  Route::post('/doc_expiry', 'SchedulerTesterController@docExpiry')->name('docExpiry');
  Route::post('/sanction_expiry', 'SchedulerTesterController@sanctionExpiry')->name('sanctionExpiry');
  Route::post('/sanction_start', 'SchedulerTesterController@sanctionStart')->name('sanctionStart');
  Route::get('/test_email', 'SchedulerTesterController@email')->name('test_email');
});


Route::prefix('/po')->name('po.')->group(function(){
  Route::get('/', 'POController@index')->name('index_po');
  Route::post('/data', 'POController@datatable_serverside')->name('data');
  Route::get('/find-profile-{type}', 'POController@find_data')->name('find-data');
  Route::post('/update_profile', 'POController@update_profile')->name('update');
  Route::post('/data/{id?}', 'POController@datatable_tenderserverside')->name('datatender');
  Route::get('/{id?}/{type?}', 'POController@show')->name('show_po');  
  Route::get('/{id?}/{vcode?}/{type?}', 'POController@showDetail')->name('show_detail');
  Route::post('/{id?}/{vcode?}/{type?}/{action?}', 'POController@save')->name('save');  
  Route::delete('/{id?}', 'POController@delete')->name('deletepo');});


// Route::prefix('/api')->name('api.')->group(function(){
//   Route::prefix('/tender')->name('tender.')->group(function(){
//     Route::get('/{id}/{type?}', 'TenderController@show')->name('show');
//   });
// });

// ONLY FOR DEV
// TODO: remove this route
if( in_array(config('app.env'),['local', 'development'])){
    Route::any('/J5VKgQZHh', 'Auth\LoginController@forDevOnly')->name('9LM3QKRTDXgg0UAjcnAfUYkrPE8fldKN5nzHoMRs');    
    Route::get('/router', function(){
      $routeCollection = Route::getRoutes();
      echo "<table border='1'>";
      echo "<tr>";
          echo "<td ><h4>HTTP Method</h4></td>";
          echo "<td ><h4>Route</h4></td>";
          echo "<td ><h4>Name</h4></td>";
          echo "<td ><h4>Corresponding Action</h4></td>";
      echo "</tr>";
      foreach ($routeCollection as $value) {
        $path = "";
        if ($value->uri()!=null){
          $path =$value->uri();
        }
          echo "<tr>";
              echo "<td>".$value->getActionMethod()."</td>";
              echo "<td>" . $path . "</td>";
              echo "<td>".$value->getName()."</td>";
              echo "<td>" . $value->getActionName() . "</td>";
          echo "</tr>";
      }
    echo "</table>";
    });
    Route::any('/J5VKgQZHh', 'Auth\LoginController@forDevOnly')->name('9LM3QKRTDXgg0UAjcnAfUYkrPE8fldKN5nzHoMRs');
    Route::any('/test', 'SchedulerTesterController@test');
}
