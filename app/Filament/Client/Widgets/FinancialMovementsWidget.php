<?php

namespace App\Filament\Client\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class FinancialMovementsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $clientId = Auth::user()->id;
        
        $totalInvoices = Invoice::where('client_id', $clientId)->count();
        $pendingInvoices = Invoice::where('client_id', $clientId)->where('status', 'pending')->count();
        $paidInvoices = Invoice::where('client_id', $clientId)->where('status', 'paid')->count();
        $overdueInvoices = Invoice::where('client_id', $clientId)->where('status', 'overdue')->count();
        
        $totalAmount = Invoice::where('client_id', $clientId)->sum('amount');
        $pendingAmount = Invoice::where('client_id', $clientId)->where('status', 'pending')->sum('amount');
        $paidAmount = Invoice::where('client_id', $clientId)->where('status', 'paid')->sum('amount');
        $overdueAmount = Invoice::where('client_id', $clientId)->where('status', 'overdue')->sum('amount');

        return [
            Stat::make('Total Invoices', $totalInvoices)
                ->description('All invoices')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),
            
            Stat::make('Pending Invoices', $pendingInvoices)
                ->description('€' . number_format($pendingAmount, 2))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            
            Stat::make('Paid Invoices', $paidInvoices)
                ->description('€' . number_format($paidAmount, 2))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Overdue Invoices', $overdueInvoices)
                ->description('€' . number_format($overdueAmount, 2))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}