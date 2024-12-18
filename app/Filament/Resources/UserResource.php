<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\CoursesRelationManager;
use App\Models\City;
use App\Models\Course;
use App\Models\Letter;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Facades\Filament;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Shield\Contracts\HasRoles;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use phpDocumentor\Reflection\Types\Boolean;
use Filament\Forms\Components\Component;
use Filament\Resources\RelationManagers\RelationManager;

class UserResource extends Resource
{
        protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Info')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('surname')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->hiddenOn('edit')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('dni')
                            ->maxLength(255)
                            ->default(null), 
                        Forms\Components\DatePicker::make('birthdate')
                            ->default(null),
                    ]),

                    Section::make('Address Info')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(255)
                            ->default(null),
                        Forms\Components\TextInput::make('phone')
                            ->maxLength(255)
                            ->default(null),      
                        Forms\Components\Select::make('country_id')
                            ->relationship(name : 'country', titleAttribute:'name')
                            ->default(205)
                            ->selectablePlaceholder(false)
                            ->preload()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function(Set $set){
                                $set('state_id', null);
                                $set('city_id', null);
                            }),
                        Forms\Components\Select::make('state_id')
                            ->options(fn (Get $get): Collection => State::query()
                                ->where('country_id', $get('country_id'))
                                ->pluck('name','id'))
                            ->default(31)
                            ->selectablePlaceholder(false)
                            ->preload()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function(Set $set){
                                $set('city_id', null);
                            }),
                        Forms\Components\Select::make('city_id')
                            ->options(fn (Get $get): Collection => City::query()
                                ->where('state_id', $get('state_id'))
                                ->pluck('name','id'))
                            ->default(4815)
                            ->selectablePlaceholder(false)
                            ->preload()
                            ->searchable(),
                    ]),
                   
                    Section::make()
                        //->columns(3)
                        ->schema([
                        Split::make([
                            Section::make('Bank Info')
                            //->columns(3)
                            ->schema([
                                    Forms\Components\TextInput::make('bank_account')
                                        ->maxLength(255)
                                        ->default(null),                       
                            ]),

                            Section::make('User status')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make( 'roles')
                                    ->relationship('roles', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->live(),  
                                Forms\Components\Toggle::make('active')
                                    ->default(true)    
                                    ->onIcon('heroicon-m-user')
                                    ->offIcon('heroicon-m-user')
                                    ->onColor('success')
                                    ->offColor('danger'),                      
                            ]),
                        ])
                    ]),
                    
                    Section::make('User Info')
                        ->hiddenOn('create')
                        ->schema([
                            Tabs::make('athlete')
                            ->hiddenOn('create')
                            ->hidden(fn (User $record): bool => !$record->hasRole(roles: 'athlete'))
                            ->tabs([
                                Tabs\Tab::make('Athlete 1') 
                                    ->schema([
                                        Forms\Components\Select::make('courses')
                                            ->relationship('courses', 'name')
                                            ->options(function () {
                                                return Course::whereHas('season', function ($query) {
                                                    $query->where('active', true);
                                                })->get()
                                                ->pluck('name_and_letter', 'id');
                                            })
                                            ->pivotData([
                                                'active' => true,
                                            ])
                                    ]),
                            Tabs\Tab::make('Athlete 2') 
                                ->schema([
                                    // ...
                                ])
                        ]),

                        Tabs::make('coach')
                        ->hiddenOn('create')
                        ->hidden(fn (User $record): bool => !$record->hasRole(roles: 'coach'))
                        ->tabs([
                            Tabs\Tab::make('Coach 1') 
                                ->schema([
                                    Forms\Components\Select::make( 'roles')
                                        ->relationship('roles', 'name')
                                        ->multiple()
                                        ->preload()
                                        ->searchable()
                                        ->live(), 
                                ]),
                            Tabs\Tab::make('Coach 2') 
                                ->schema([
                                    // ...
                                ])
                        ])

                    ]),

            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('surname')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('phone')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('address')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false), 
                Tables\Columns\TextColumn::make('city.name')//nombre de la relación del modelo + . + campo a visualizar
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('roles.name') 
                    ->badge()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),                    
                Tables\Columns\TextColumn::make('dni')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('active')
                        ->boolean(),
            ])

            ->filters([
                TernaryFilter::make('active')
                    ->placeholder('All')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->default(true)
                    ->queries(
                        true: fn (Builder $query) => $query->where ('active', true),
                        false: fn (Builder $query) => $query->where('active', false),
                        blank: fn (Builder $query) => $query,),
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload(),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
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
            CoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}