<?php

namespace App\Exports;

use App\Http\Controllers\SleepControllerAPI;
use App\Sleep;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SleepsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Sleep::all();
    }

    public function map($sleep): array
    {
        $w = SleepControllerAPI::computeTimeInHours($sleep->wakeUpTime);
        $s = SleepControllerAPI::computeTimeInHours($sleep->sleepTime);
        $sleep->totalSleepHours = abs($s - $w);
        return array_merge(User::where('id',$sleep->userId)->first(['name', 'gender', 'weight', 'height', 'email'])->toArray(), $sleep->toArray());
    }

    public function headings(): array
    {
        return [
            'Nome',
            'Género',
            'Peso',
            'Altura',
            'Email',
            'Id registo',
            'Data',
            'Id utilizador',
            'Hora levantar',
            'Hora deitar',
            'Acordou durante a noite?',
            'Atividades antes de adormecer',
            'Motivos que influenciam',
            'Total horas sono'
        ];
    }
}
