<?php

namespace App\Providers;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureTextInput();
        $this->configureSelect();
        $this->configureTextarea();
        $this->configureDatePicker();
        $this->configureDateTimePicker();
        $this->configureTable();
    }

    private function configureTextInput(): void
    {
        TextInput::configureUsing(function (TextInput $field): void {
            $field->minLength(2)
                ->maxLength(255);
        });
    }
    
    private function configureSelect(): void
    {
        Select::configureUsing(function (Select $field): void {
            $field->native(false);
        });
    }
    
    private function configureTextarea(): void
    {
        Textarea::configureUsing(function (Textarea $field): void {
            $field->minLength(2)
                ->maxLength(1024);
        });
    }
    
    private function configureDatePicker(): void
    {
        DatePicker::configureUsing(function (DatePicker $field): void {
            $field->format('d-m-Y')
                ->native(false);
        });
    }
    
    private function configureDateTimePicker(): void
    {
        DateTimePicker::configureUsing(function (DateTimePicker $field): void {
            $field->format('d-m-Y H:i')
                ->native(false)
                ->seconds(false);
        });
    }
    
    private function configureTable(): void
    {
        Table::configureUsing(function (Table $table): void {
            $table->defaultSort('created_at', 'desc')
                ->paginated([10, 25, 50, 100])
                ->striped();
        });
    }
}
