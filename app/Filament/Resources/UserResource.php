<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\City;
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
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Collection;

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

                    Section::make('Bank details')
                    //->columns(3)
                    ->schema([
                            Forms\Components\TextInput::make('bank_account')
                                ->maxLength(255)
                                ->default(null),
                        
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
                Tables\Columns\TextColumn::make('dni')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('active')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)  
                    ->onIcon('heroicon-m-user')
                    ->offIcon('heroicon-m-user')
                    ->onColor('success')
                    ->offColor('danger')
                    ->disabled(),

            ])
            ->filters([
                //
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
            //
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
