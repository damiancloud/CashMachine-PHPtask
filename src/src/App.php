<?php
namespace App;

use Services\CashMachineService;

class App
{
    public function run(): void
    {
        $service = new Services\CashMachineService();
        try {
            $result = $service->withdrawNotes(900);
            print($result);
        } catch (\Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}
