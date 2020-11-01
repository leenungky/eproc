<?php

use Illuminate\Database\Seeder;

use App\RefStatus;

class RefStatusSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        //
        RefStatus::truncate();
        DB::statement('ALTER SEQUENCE ref_statuss_id_seq RESTART WITH 1');

        $data = [
            [
                'status' => 'Applicant Submission',
                'description' => 'Applicant Submission or Registration',
                'created_by' => 'initial',
            ], [
                'status' => 'Applicant Verification',
                'description' => 'Admin verify applicant registration form',
                'created_by' => 'initial',
            ], [
                'status' => 'Applicant Verified by Admin',
                'description' => 'Applicant has already verified by Admin',
                'created_by' => 'initial',
            ], [
                'status' => 'Applicant Rejected by Admin',
                'description' => 'Applicant has already rejected by Admin',
                'created_by' => 'initial',
            ], [
                'status' => 'Company Profile Submission',
                'description' => 'Completing Candidate Company Profile',
                'created_by' => 'initial',
            ], [
                'status' => 'Company Profile Verification',
                'description' => 'Admin vendor verify Company Profile submission',
                'created_by' => 'initial',
            ], [
                'status' => 'Company Profile Verified by Admin',
                'description' => 'Company Profile of Candidate has already verified by Admin',
                'created_by' => 'initial',
            ], [
                'status' => 'Company Profile Approval',
                'description' => 'Company profile approval process that has been verified by the admin',
                'created_by' => 'initial',
            ], [
                'status' => 'Company Profile Approved by QMR',
                'description' => 'Company Profile of Candidate has already approved by QMR',
                'created_by' => 'initial',
            ], [
                'status' => 'Company Profile Rejected by QMR',
                'description' => 'Company Profile of Candidate has already rejected by QMR',
                'created_by' => 'initial',
            ]
        ];
        RefStatus::insert($data); // Eloquent approach
                
        // case when using Query Builder approach
        //DB::table('table')->insert($data);                
    }

}
