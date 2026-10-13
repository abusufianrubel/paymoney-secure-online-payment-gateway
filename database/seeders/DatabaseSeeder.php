<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Model::unguard();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // core PayMoney seeders with minimal or no foreign key dependencies
        $this->call(RolesTableSeeder::class);
        $this->call(AppStoreCredentialsTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(CryptoProvidersTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(EmailConfigsTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(MerchantGroupsTableSeeder::class);
        $this->call(MetasTableSeeder::class);
        $this->call(NotificationTypesTableSeeder::class);
        $this->call(OauthClientsTableSeeder::class);
        $this->call(OauthPersonalAccessClientsTableSeeder::class);
        $this->call(PaymentMethodsTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
        $this->call(PreferencesTableSeeder::class);
        $this->call(ReasonsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(SmsConfigsTableSeeder::class);
        $this->call(SocialsTableSeeder::class);
        $this->call(TicketStatusesTableSeeder::class);
        $this->call(TransactionTypesTableSeeder::class);
        $this->call(TimeZonesTableSeeder::class);
        
        if (checkDemoEnvironment() || app()->runningInConsole()) {
            $this->call(AdminsTableSeeder::class);
        }

        // core PayMoney seeders with minimal or no foreign key dependencies for demo
        if (checkDemoEnvironment()) {
            $this->call(UsersTableSeeder::class);
            $this->call(ActivityLogsTableSeeder::class);
            $this->call(AddonsTableSeeder::class);
            $this->call(AppTokensTableSeeder::class);
            $this->call(BackupsTableSeeder::class);
            $this->call(OauthAccessTokensTableSeeder::class);
            $this->call(OauthAuthCodesTableSeeder::class);
            $this->call(OauthRefreshTokensTableSeeder::class);
            $this->call(PagesTableSeeder::class);
            $this->call(PasswordResetsTableSeeder::class);
            $this->call(FailedJobsTableSeeder::class);
            $this->call(JobsTableSeeder::class);
            $this->call(AppTransactionsInfosTableSeeder::class);
            $this->call(CoinpaymentLogTrxesTableSeeder::class);
            $this->call(PersonalAccessTokensTableSeeder::class);
            $this->call(DeviceLogsTableSeeder::class);
            $this->call(VerifyUsersTableSeeder::class);
            $this->call(ParametersTableSeeder::class);
        }

        // core PayMoney seeders with foreign key dependencies
        $this->call(EmailTemplatesTableSeeder::class);
        $this->call(NotificationSettingsTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);
        $this->call(FeesLimitsTableSeeder::class);

        // core PayMoney seeders with foreign key dependencies for demo
        if (checkDemoEnvironment()) {
            $this->call(MerchantsTableSeeder::class);
            $this->call(MerchantAppsTableSeeder::class);
            $this->call(MerchantPaymentsTableSeeder::class);
            $this->call(TicketsTableSeeder::class);
            $this->call(TicketRepliesTableSeeder::class);
            $this->call(UserDetailsTableSeeder::class);
            $this->call(FilesTableSeeder::class);
            $this->call(BanksTableSeeder::class);
            $this->call(QrCodesTableSeeder::class);
            $this->call(WalletsTableSeeder::class);
            $this->call(PayoutSettingsTableSeeder::class);
            $this->call(WithdrawalsTableSeeder::class);
            $this->call(WithdrawalDetailsTableSeeder::class);
            $this->call(TransfersTableSeeder::class);
            $this->call(RequestPaymentsTableSeeder::class);
            $this->call(DepositsTableSeeder::class);
            $this->call(CurrencyExchangesTableSeeder::class);
            $this->call(CurrencyPaymentMethodsTableSeeder::class);
            $this->call(TransactionsTableSeeder::class);
            $this->call(DisputesTableSeeder::class);
            $this->call(DisputeDiscussionsTableSeeder::class);
        }
    }
}
