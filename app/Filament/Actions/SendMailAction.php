<?php

namespace App\Filament\Actions;

use App\Mail\GenericMail;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Mail;

class SendMailAction
{
    // Single Record Action
    public static function make(): Action
    {
        return Action::make('sendMail')
            ->label('Mail')
            ->icon('heroicon-m-envelope')
            ->modalHeading('Send Email')
            ->form(self::form())
            ->action(function (array $data, $record) {
                Mail::to($record->email)->queue( // ✅ Queueing
                    new GenericMail(
                        subject: $data['subject'],
                        body: $data['body']
                    )
                );

                Notification::make()
                    ->title('Email Queued')
                    ->body("Email has been queued for {$record->email}.")
                    ->success()
                    ->send();
            });
    }

    // Bulk Action
    public static function makeBulk(): BulkAction
    {
        return BulkAction::make('sendMail')
            ->label('Send Mail to Selected')
            ->icon('heroicon-m-envelope')
            ->modalHeading('Send Email to Selected Users')
            ->form(self::Bulkform())
            ->action(function (array $data, $records) {
                foreach ($records as $record) {
                    Mail::to($record->email)->queue( // ✅ Queueing
                        new GenericMail(
                            subject: $data['subject'],
                            body: $data['body']
                        )
                    );
                }

                Notification::make()
                    ->title('Emails Queued')
                    ->body('Emails have been queued for '.$records->count().' users.')
                    ->success()
                    ->send();
            });
    }

    protected static function form(): array
    {
        return [
            Forms\Components\TextInput::make('email')
                ->label('To')
                ->disabled()
                ->default(fn ($record) => $record->email)
                ->extraAttributes(['class' => 'badge badge-primary']),

            Forms\Components\TextInput::make('subject')
                ->label('Subject')
                ->required(),

            Forms\Components\RichEditor::make('body')
                ->label('Body')
                ->required(),
        ];
    }

    protected static function Bulkform(): array
    {
        return [
            Forms\Components\TextInput::make('email')
                ->label('To')
                ->disabled()
                ->default('Multiple recipients')
                ->extraAttributes(['class' => 'badge badge-primary']),
            Forms\Components\TextInput::make('subject')
                ->label('Subject')
                ->required(),

            Forms\Components\RichEditor::make('body')
                ->label('Body')
                ->required(),
        ];
    }
}
