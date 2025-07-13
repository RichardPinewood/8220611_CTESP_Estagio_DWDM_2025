<?php

namespace App\Filament\Client\Widgets;

use App\Models\Invoice;
use App\Models\FinancialMovement;
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
        
        $pendingAmount = Invoice::where('client_id', $clientId)->where('status', 'pending')->sum('amount');
        $paidAmount = Invoice::where('client_id', $clientId)->where('status', 'paid')->sum('amount');
        $overdueAmount = Invoice::where('client_id', $clientId)->where('status', 'overdue')->sum('amount');
        
        $currentBalance = FinancialMovement::where('client_id', $clientId)
            ->orderBy('processed_at', 'desc')
            ->value('balance_after') ?? 0;
        
        $totalMovements = FinancialMovement::where('client_id', $clientId)->count();

        return [
            Stat::make('Account Balance', '€' . number_format($currentBalance, 2))
                ->description('Current account balance')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($currentBalance >= 0 ? 'success' : 'danger'),
            
            Stat::make('Pending Invoices', $pendingInvoices)
                ->description('€' . number_format($pendingAmount, 2))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            
            Stat::make('Paid Invoices', $paidInvoices)
                ->description('€' . number_format($paidAmount, 2))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Financial Movements', $totalMovements)
                ->description('Total transactions')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info'),
        ];
    }
}