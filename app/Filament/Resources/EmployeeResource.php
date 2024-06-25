<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Indicator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?string $navigationLabel = 'الموظفين';
    protected static ?string $modelLabel = 'موظقف';
    protected static ?string $pluralModelLabel = 'موظفين';

    protected static ?string $recordTitleAttribute = 'first_name';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User name')
                ->description('Put the user information here')
                ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('date_of_birth')
                        ->displayFormat('d/m/Y')
                        ->native(false)
                        ->required(),
                ])->columns(3),
                Forms\Components\Section::make('Address')
                ->description('Put the address details here')
                ->schema([
                    Forms\Components\TextInput::make('address')
                        ->required()
                        ->maxLength(255),
                        Forms\Components\Select::make('city_id')
                        ->relationShip('city', 'name')
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('zip_code')
                        ->required()
                        ->maxLength(255),
                ])->columns(3),
                Forms\Components\Section::make('Hired')
                ->description('Put the job details here')
                ->schema([
                    Forms\Components\Select::make('department_id')
                    ->relationShip('department', 'name')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->required(),
                    Forms\Components\DatePicker::make('date_hired')
                    ->displayFormat('d/m/Y')
                    ->native(false)
                    ->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_hired')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('Department')->relationship('department','name'),
                SelectFilter::make('City')->relationship('city','name'),
                Filter::make('date_hired')
                ->form([
                    DatePicker::make('date_hired_from'),
                    DatePicker::make('date_hired_until'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['date_hired_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('date_hired', '>=', $date),
                        )
                        ->when(
                            $data['date_hired_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('date_hired', '<=', $date),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];
             
                    if ($data['date_hired_from'] ?? null) {
                        $indicators[] = Indicator::make('Hired from ' . Carbon::parse($data['date_hired_from'])->toFormattedDateString())
                            ->removeField('date_hired_from');
                    }
             
                    if ($data['date_hired_until'] ?? null) {
                        $indicators[] = Indicator::make('Hired until ' . Carbon::parse($data['date_hired_until'])->toFormattedDateString())
                            ->removeField('date_hired_until');
                    }
             
                    return $indicators;
                })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->first_name .' '. $record->last_name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name'];
    }   

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'City' => $record->city->name,
            'Department' => $record->department->name,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['city', 'department']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }
}
