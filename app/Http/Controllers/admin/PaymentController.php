<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = \App\Models\Payment::with(['user', 'booking', 'booking.ride'])
            ->latest()
            ->get();
            
        $totalCollected = $payments->whereIn('status', ['completed', 'captured', 'success'])->sum('amount');
        $totalRefunds = $payments->where('status', 'refunded')->sum('amount');
        
        $settings = \App\Models\Setting::first();
        $gstPercentage = $settings->gst_percentage ?? 18; 
        $commissionPercentage = 10; 
        
        $taxCollected = $totalCollected * ($gstPercentage / 100);
        
        $platformCommission = $totalCollected * ($commissionPercentage / 100);
        
        $driverPayouts = $totalCollected - $platformCommission - $taxCollected;

        return view('admin.payment.payment', compact(
            'payments',
            'totalCollected',
            'totalRefunds',
            'taxCollected',
            'platformCommission',
            'driverPayouts'
        ));
    }

    public function exportCsv()
    {
        $fileName = 'payments_export_' . date('Y-m-d_H-i-s') . '.csv';
        $payments = \App\Models\Payment::with(['user', 'booking'])->latest()->get();

        $columns = array('Transaction ID', 'Booking ID', 'User', 'Amount', 'Payment Method', 'Status', 'Date');

        $file = fopen('php://temp', 'w');
        fputcsv($file, $columns);

        foreach ($payments as $payment) {
            $row['Transaction ID']  = $payment->transaction_id ?? 'TXN-'.$payment->id;
            $row['Booking ID']    = $payment->booking_id ?? 'N/A';
            $row['User']    = $payment->user ? $payment->user->name : 'N/A';
            $row['Amount']  = $payment->amount;
            $row['Payment Method']  = $payment->payment_method;
            $row['Status']  = $payment->status;
            $row['Date']    = $payment->created_at;

            fputcsv($file, array($row['Transaction ID'], $row['Booking ID'], $row['User'], $row['Amount'], $row['Payment Method'], $row['Status'], $row['Date']));
        }

        rewind($file);
        $csvData = stream_get_contents($file);
        fclose($file);

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Pragma', 'no-cache')
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Expires', '0');
    }

    public function monthlyReport()
    {
        $fileName = 'monthly_report_' . date('F_Y') . '.csv';
        $payments = \App\Models\Payment::with(['user', 'booking'])
                    ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))
                    ->latest()
                    ->get();

        $columns = array('Transaction ID', 'Booking ID', 'User', 'Amount', 'Payment Method', 'Status', 'Date');

        $file = fopen('php://temp', 'w');
        fputcsv($file, $columns);

        foreach ($payments as $payment) {
            $row['Transaction ID']  = $payment->transaction_id ?? 'TXN-'.$payment->id;
            $row['Booking ID']    = $payment->booking_id ?? 'N/A';
            $row['User']    = $payment->user ? $payment->user->name : 'N/A';
            $row['Amount']  = $payment->amount;
            $row['Payment Method']  = $payment->payment_method;
            $row['Status']  = $payment->status;
            $row['Date']    = $payment->created_at;

            fputcsv($file, array($row['Transaction ID'], $row['Booking ID'], $row['User'], $row['Amount'], $row['Payment Method'], $row['Status'], $row['Date']));
        }

        rewind($file);
        $csvData = stream_get_contents($file);
        fclose($file);

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Pragma', 'no-cache')
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Expires', '0');
    }
}
