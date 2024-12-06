<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
//use App\Models\Role;
use App\Models\User;
use Closure;
use DeepCopy\Matcher\PropertyTypeMatcher;
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
//use Illuminate\Container\Attributes\DB;
use Illuminate\Support\Facades\DB as FacadesDB;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;



class UserResource extends Resource
{
        protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                            ->maxLength(255),
                        Forms\Components\TextInput::make('dni')
                            ->maxLength(255)
                            ->default(null), 
                        Forms\Components\TextInput::make('birthdate')
                            ->maxLength(255)
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
/*
                    $table->unsignedBigInteger('country_id');
                    $table->unsignedBigInteger('state_id');
                    $table->unsignedBigInteger('city_id');
                    $table->string('bank_account');
*/

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
                            /*
                        Forms\Components\Select::make('country_id')
                            ->relationship(name : 'country', titleAttribute:'name')
                            ->default(205)
                            ->selectablePlaceholder(false)
                            ->preload(),
                            //->searchable()
                            //->live(),
                        Forms\Components\Select::make('state_id')
                            //->relationship(name : 'state', titleAttribute:'name')
                            ->default(31)
                            ->selectablePlaceholder(false)
                            ->preload(),
                            //->searchable()
                            //->live(),
                        Forms\Components\Select::make('city_id')
                            //->relationship(name : 'city', titleAttribute:'name')
                            ->default(4815)
                            ->selectablePlaceholder(false)
                            ->preload(),
                            //->searchable()
                            //->live(),
                            */
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('surname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\IconColumn::make('active')
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
