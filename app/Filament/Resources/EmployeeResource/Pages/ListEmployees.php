<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Models\Employee;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    
    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'This Week' => Tab::make()->modifyQueryUsing(function($query){
                $query->whereDate('date_hired','>=',now()->subWeek());
            })->badge(Employee::query()->whereDate('date_hired','>=',now()->subWeek())->count()),
            'This Month' => Tab::make()->modifyQueryUsing(function($query){
                $query->whereDate('date_hired','>=',now()->subMonth());
            })->badge(Employee::query()->whereDate('date_hired','>=',now()->subMonth())->count()),
            'This Year' => Tab::make()->modifyQueryUsing(function($query){
                $query->whereDate('date_hired','>=',now()->subYear());
            })->badge(Employee::query()->whereDate('date_hired','>=',now()->subYear())->count()),
        ];
    }
}
