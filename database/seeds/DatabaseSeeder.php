<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Disable Foreign Key Check
        Schema::disableForeignKeyConstraints();
        
        $this->call(PermissionsSeeder::class);
        $this->call(TenderApprovers::class);
        $this->call(UserSeeder::class);
        
        $this->call(SapSeeder::class);
        $this->call(RefCountrySeeder::class);

        $this->call(RefStatusSeeder::class);
        $this->call(RefCompanyTypeSeeder::class);
        $this->call(RefPurchaseOrgSeeder::class);
        $this->call(RefPurchaseGroupSeeder::class);
        $this->call(RefListOptionSeeder::class);
        $this->call(RefPlantSeeder::class);
        $this->call(RefCurrencySeeder::class);
        $this->call(RefScopeOfSupplySeeder::class);
        $this->call(RefSysParamSeeder::class);

        $this->call(ConditionalTypeSeeder::class);
        // $this->call(ConditionalTypeOptionSeeder::class);
        $this->call(TaxCodeSeeder::class);
        
        $this->call(PagesSeeder::class);
        $this->call(ClearStorage::class);
        
        // Enable Foreign Key Check
        Schema::enableForeignKeyConstraints();

    }
}
