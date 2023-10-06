<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use App\Services\CashMachineService;
use App\Exceptions\ValidationExceptions\NoteUnavailableException;
use App\Exceptions\ValidationExceptions\InvalidArgumentException;

class CashMachineTest extends TestCase
{
    public static function validWithdrawDataProvider()
    {
        return [
            [150, [100, 50]],
            [200, [100, 100]],
            [900, [100, 100, 100, 100, 100, 100, 100, 100, 100]],
            [1100, [100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 50, 50]],
            [80, [50, 20, 10]],
            [30, [20, 10]],
            [10, [10]],
        ];
    }

    #[DataProvider('validWithdrawDataProvider')]
    public function testValidWithdraw($amount, $expectedNotes)
    {
        $service = new App\Services\CashMachineService();
        $result = $service->withdrawNotes($amount);
        
        $this->assertEquals($expectedNotes, $result);
    }

    public static function invalidAmountDataProvider()
    {
        return [[-50], [-333], [-100]];
    }

    #[DataProvider('invalidAmountDataProvider')]
    public function testInvalidAmount($amount)
    {
        $this->expectException(InvalidArgumentException::class);
        
        $service = new App\Services\CashMachineService();
        $service->withdrawNotes($amount);
    }

    public static function noteUnavailableDataProvider()
    {
        return [[33], [333], [199]];
    }

    #[DataProvider('noteUnavailableDataProvider')]
    public function testNoteUnavailable($amount)
    {
        $this->expectException(NoteUnavailableException::class);
        $this->expectExceptionMessage("Note unavailable");
        
        $service = new App\Services\CashMachineService();
        $service->withdrawNotes($amount);
    }
    
    public function testMultiWithdrawValid()
    {
        $service = new App\Services\CashMachineService();
        $service->withdrawNotes(900);
        $result = $service->withdrawNotes(200);
        
        $this->assertEquals([100, 50, 50], $result);
    }

    public function testMultiWithdrawValidAllNotes()
    {
        $service = new App\Services\CashMachineService();
        $service->withdrawNotes(1000); // 10 * 100
        $service->withdrawNotes(500);  // 10 * 50
        $service->withdrawNotes(200);  // 10 * 20
        $result = $service->withdrawNotes(100);
        
        $this->assertEquals([10, 10, 10, 10, 10, 10, 10, 10, 10, 10], $result);
    }

    public function testMultiWithdrawUnValid()
    {
        $service = new App\Services\CashMachineService();
        try {
            $service->withdrawNotes(900);
            $service->withdrawNotes(199); // exception Note unavailable
        } catch (\Exception $th) {
            $this->assertInstanceOf('App\Exceptions\ValidationExceptions\NoteUnavailableException', $th);
        }
    }

    public function testMultiWithdrawWithUnValidAndValid()
    {
        $service = new App\Services\CashMachineService();
        try {
            $service->withdrawNotes(900);
            $service->withdrawNotes(199); // exception Note unavailable
        } catch (\Exception $th) {
            $result = $service->withdrawNotes(200);
            $this->assertEquals([100, 50, 50], $result);
        }
    }

    public function testMultiWithdrawUnvalidAllNotes()
    {
        $service = new App\Services\CashMachineService();
        $service->withdrawNotes(1000); // 10 * 100
        $service->withdrawNotes(500);  // 10 * 50
        $service->withdrawNotes(200);  // 10 * 20
        $service->withdrawNotes(100);  // 10 * 10

        try {
            $result = $service->withdrawNotes(100); // exception Note unavailable
        } catch (\Exception $th) {
            $this->assertInstanceOf('App\Exceptions\ValidationExceptions\NoteUnavailableException', $th);
        }
    } 
}