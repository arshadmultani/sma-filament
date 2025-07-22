<?php

namespace App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Form;
use App\Models\Campaign;
use App\Models\KofolEntry;
use Filament\Forms\Components\Section;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class Reports extends Page implements HasForms,HasTable
{
    use InteractsWithForms,InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.reports';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Campaign')
                ->columns(3)
                ->schema([
                    Select::make('campaign_id')
                        ->label('Campaign')
                        ->options(Campaign::where('is_active', true)->pluck('name', 'id'))
                        ->native(false)
                        ->required(),
                    Select::make('report_type')
                        ->native(false)
                        ->options([
                            'kofol' => 'Kofol',
                        ])
                        ->required(),
                ])
        ];
    }

    public function submit()
    {
        $this->validate();
    }
    public function setTable($campaign){
        
    }
    public function table(Table $table): Table
    {
       return $table;
    }
}

//table 1 of campaign 1

//table 2 of campaign 1

//table 1 of campaign 2

//table 2 of campaign 2



