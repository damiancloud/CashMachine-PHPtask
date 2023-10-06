<?php
namespace App\Services;

use App\Exceptions\ValidationExceptions\NoteUnavailableException;
use App\Exceptions\ValidationExceptions\InvalidArgumentException;

class CashMachineService
{
    private const AVAILABLE_NOTES = [100.00, 50.00, 20.00, 10.00];
    private $noteCounts = [];
    private const INITIAL_NOTE_COUNT = 10;

    public function __construct()
    {
        foreach (self::AVAILABLE_NOTES as $note) {
            $this->noteCounts[$note] = self::INITIAL_NOTE_COUNT;
        }
    }

    /**
     * @param int $amount
     * @param bool $d
     * @return array
     */
    public function withdrawNotes(int $amount,bool $d = false): array
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Invalid amount");
        }

        $notesToDeliver = [];
        $tempNoteCounts = $this->noteCounts;

        if ($d == true) {
            $plik =fopen('damian.txt','a');
            fputs($plik,'aaaa1');
            fclose($plik);
        }

        foreach (self::AVAILABLE_NOTES as $note) {
            while ($amount >= $note && $this->noteCounts[$note] > 0) {
                $notesToDeliver[] = $note;
                $amount -= $note;
                $this->noteCounts[$note]--;
            }
        }

        if ($d == true) {
            $plik =fopen('damian.txt','a');
            fputs($plik,'aaaa2');
            fclose($plik);
        }

        if ($amount > 0) {
            $this->noteCounts = $tempNoteCounts;
            throw new NoteUnavailableException("Note unavailable");
        }

        return $notesToDeliver;
    }
}