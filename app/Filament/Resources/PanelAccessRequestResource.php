<?php

namespace App\Filament\Resources;

use App\Filament\Actions\ActivateUser;
use Filament\Infolists\Components\IconEntry;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\PanelAccessRequest;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Actions\DeactivateUser;
use App\Filament\Actions\ViewInfoAction;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Actions\Action;
use App\Filament\Resources\PanelAccessRequestResource\Pages;

class PanelAccessRequestResource extends Resource
{
    protected static ?string $model = PanelAccessRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $modelLabel = 'Portal Request';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Request ID')
                    ->prefix('PR-')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('doctor.name')
                    ->label('Dr.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('requester.name')
                    ->label('Requested By')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('requester.roles.name')
                    ->label('Role')
                    ->toggleable()
                    ->badge()
                    ->color(fn($record) => $record->requester?->roleColor() ?? 'secondary'),
                TextColumn::make('state.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn($record) => $record->state->color)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('hasLoginAccount')
                    ->label('Portal A/c')
                    ->getStateUsing(fn($record) => $record->doctor->hasLoginAccount() ? 'Yes' : 'No')
                    ->toggleable()
                    ->badge(fn($state) => $state === 'Yes' ? 'success' : 'danger')
                    ->color(fn($state) => $state === 'Yes' ? 'success' : 'danger'),
                TextColumn::make('reviewer.name')
                    ->label('Reviewed By')
                    ->placeholder('NA')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->placeholder('NA')
                    ->toggleable()
                    ->dateTime('d M y @ H:i'),
                // ->default(fn($record) => $record->reviewed_at ?? 'NA'),
                TextColumn::make('created_at')
                    ->label('Submitted On')
                    ->toggleable()
                    ->dateTime('d M y @ H:i'),

            ])
            ->filters([
                //
            ])
            ->actions([
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Access Details')
                    ->compact()
                    ->collapsible()
                    ->columns(4)
                    ->visible(fn($record) => $record->doctor->hasLoginAccount())
                    ->headerActions([
                        DeactivateUser::make(),
                        ActivateUser::make()
                            ->visible(fn($record) => !$record->doctor->userAccount()?->is_active),

                    ])
                    ->schema([
                        IconEntry::make('hasLoginAccount')
                            ->label('Portal A/c Exists ?')
                            ->getStateUsing(fn($record) => $record->doctor->hasLoginAccount())
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle'),
                        TextEntry::make('accountActive')
                            ->label('A/c Status ')
                            ->getStateUsing(fn($record) => $record->doctor->userAccount()?->is_active ? 'Active' : 'Inactive')
                            ->badge(fn($state) => $state === 'Active' ? 'success' : 'danger')
                            ->color(fn($state) => $state === 'Active' ? 'success' : 'danger'),
                        TextEntry::make('accountCreatedAt')
                            ->label('Account Created At')
                            ->getStateUsing(function ($record) {
                                $user = $record->doctor->userAccount();
                                return $user ? $user->created_at->format('d M Y @ H:i') : 'N/A';
                            })
                            ->placeholder('N/A'),
                        Actions::make([
                            // DeactivateUser::make(),
                        ]),
                    ]),

                Section::make('Request Information')
                    ->compact()
                    ->collapsible()
                    ->columns(4)
                    ->columnSpanFull()
                    ->grow(true)
                    ->schema([
                        TextEntry::make('doctor.name')
                            ->label('Doctor')
                            ->helperText(fn($record) => $record->doctor->headquarter->name)
                            ->prefixAction(ViewInfoAction::for('doctor', DoctorResource::class, 'Doctor')),
                        TextEntry::make('requester.name')
                            ->label('Requested By')
                            ->helperText(
                                fn($record) => collect([
                                    $record->requester->division?->name,
                                    $record->requester->location?->name,
                                ])->filter()->join(' - ')
                            )
                            ->hint(fn($record) => $record->requester->roles->first()?->name)
                            ->hintColor(fn($record) => $record->requester->roleColor() ?? 'secondary')
                            ->prefixAction(ViewInfoAction::for('requester', UserResource::class, 'Requester')),

                        TextEntry::make('created_at')
                            ->label('Requested At')
                            ->dateTime('d-M-y @ H:i'),
                        TextEntry::make('state.name')
                            ->label('Status')
                            ->badge()
                            ->color(fn($record) => $record->state->color),
                        TextEntry::make('request_reason')
                            ->label('Reason for Request')
                            ->formatStateUsing(fn($state) => Str::headline($state)),
                        TextEntry::make('justification')
                            ->visible(fn($record) => !is_null($record->justification))
                            ->label('Remark'),
                        TextEntry::make('reviewer.name')
                            ->label('Reviewed By')
                            ->visible(fn($record) => !is_null($record->reviewer)),
                        TextEntry::make('rejection_reason')
                            ->visible(fn($record) => filled($record->rejection_reason))
                            ->label('Rejection Reason'),
                        TextEntry::make('reviewed_at')
                            ->label('Reviewed At')
                            ->dateTime('d-M-y @ H:i')
                            ->visible(fn($record) => !is_null($record->reviewed_at)),

                    ]),

                Section::make('Other Requests for this Doctor')
                    ->compact()
                    ->collapsible()
                    ->columnSpanFull()
                    ->visible(function ($record) {
                        return PanelAccessRequest::where('doctor_id', $record->doctor_id)
                            ->where('id', '!=', $record->id)
                            ->exists();
                    })
                    ->schema([
                        TextEntry::make('other_requests_list')
                            ->label('')
                            ->getStateUsing(function ($record) {
                                $requests = PanelAccessRequest::where('doctor_id', $record->doctor_id)
                                    ->where('id', '!=', $record->id)
                                    ->with(['state', 'requester'])
                                    ->orderBy('created_at', 'desc')
                                    ->get();

                                $mappedRequests = $requests->map(function ($req) {
                                    $status = $req->state->name;
                                    $requestedBy = $req->requester->name;
                                    $date = $req->created_at->format('d M Y');
                                    $reason = Str::headline($req->request_reason);

                                    $line = "PR-{$req->id} • {$status} • {$requestedBy} • {$date} • {$reason}";

                                    if ($req->rejection_reason) {
                                        $line .= "\nRejection: " . $req->rejection_reason;
                                    }

                                    return '• ' . $line;

                                });

                                $result = $mappedRequests->join("\n\n");

                                return new HtmlString(nl2br(e($result)));
                            })
                            ->placeholder('No other requests found')
                            ->hiddenLabel(),
                    ]),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPanelAccessRequests::route('/'),
            'create' => Pages\CreatePanelAccessRequest::route('/create'),
            'edit' => Pages\EditPanelAccessRequest::route('/{record}/edit'),
            'view' => Pages\ViewPanelAccessRequest::route('/{record}'),
        ];
    }
}
