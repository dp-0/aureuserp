<?php

namespace Webkul\Purchase;

use Webkul\Support\Console\Commands\InstallCommand;
use Webkul\Support\Package;
use Webkul\Support\PackageServiceProvider;

class PurchaseServiceProvider extends PackageServiceProvider
{
    public static string $name = 'purchases';

    public static string $viewNamespace = 'purchases';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2025_02_11_101100_create_purchases_order_groups_table',
                '2025_02_11_101101_create_purchases_requisitions_table',
                '2025_02_11_101105_create_purchases_requisition_lines_table',
                '2025_02_11_101110_create_purchases_orders_table',
                '2025_02_11_101118_create_purchases_order_lines_table',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2025_01_11_094022_create_purchases_product_settings',
            ])
            ->runsSettings()
            ->hasDependencies([
                'products',
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations();
            });
    }

    public function packageBooted(): void
    {
        //
    }
}
