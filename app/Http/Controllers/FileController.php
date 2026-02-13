<?php

namespace App\Http\Controllers;

use App\Models\PartyTransferAttachment;
use App\Models\BankTransferAttachment;
use App\Models\GeneralVoucherAttachment;
use App\Models\ExpenseAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    public function downloadPartyTransferAttachment(PartyTransferAttachment $attachment)
    {
        // Check if user has access to the party transfer
        $partyTransfer = $attachment->partyTransfer;
        
        if (!$partyTransfer || $partyTransfer->business_id !== session('active_business')) {
            abort(403, 'Access denied');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->original_name
        );
    }

    public function downloadBankTransferAttachment(BankTransferAttachment $attachment)
    {
        // Check if user has access to the bank transfer
        $bankTransfer = $attachment->bankTransfer;
        
        if (!$bankTransfer || $bankTransfer->business_id !== session('active_business')) {
            abort(403, 'Access denied');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->original_name
        );
    }

    public function downloadGeneralVoucherAttachment(GeneralVoucherAttachment $attachment)
    {
        // Check if user has access to the general voucher
        $generalVoucher = $attachment->generalVoucher;
        
        if (!$generalVoucher || $generalVoucher->business_id !== session('active_business')) {
            abort(403, 'Access denied');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->original_name
        );
    }

    public function downloadExpenseAttachment(ExpenseAttachment $attachment)
    {
        // Check if user has access to the expense
        $expense = $attachment->expense;
        
        if (!$expense || $expense->business_id !== session('active_business')) {
            abort(403, 'Access denied');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download(
            $attachment->file_path,
            $attachment->original_name
        );
    }
} 