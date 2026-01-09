<?php
namespace App\Services;

use App\Models\ProjectBukukas;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Helpers
{
    public static function tagsStringToArray($tags)
    {
        if (!$tags) {
            return [];
        }

        if (is_array($tags)) {
            return $tags;
        }

        if (is_string($tags)) {
            $decoded = json_decode($tags, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return [];
    }

    public static function tagsColorToColorText($color = null)
    {
        switch ($color) {
            case 'py-1 px-2 rounded text-xs leading-none font-semibold bg-primary-100 text-primary':
                return 'Dasar';
            case 'py-1 px-2 rounded text-xs leading-none font-semibold bg-indigo-100 text-indigo-700':
                return 'Nila';
            case 'py-1 px-2 rounded text-xs leading-none font-semibold bg-teal-100 text-teal-600':
                return 'Teal';
            case 'py-1 px-2 rounded text-xs leading-none font-semibold bg-orange-100 text-orange-600':
                return 'Oranye';
            case 'py-1 px-2 rounded text-xs leading-none font-semibold bg-pink-100 text-pink':
                return 'Pink';
            case 'py-1 px-2 rounded text-xs leading-none font-semibold bg-purple-100 text-purple':
                return 'Ungu';
            case 'py-1 px-2 rounded text-xs leading-none font-semibold bg-success-100 text-success':
                return 'Hijau';
            case 'py-1 px-2 rounded text-xs leading-none font-semibold bg-danger-100 text-danger':
                return 'Merah';
            case 'py-1 px-2 rounded text-xs leading-none font-semibold bg-warning-100 text-warning':
                return 'Kuning';
            case 'py-1 px-2 rounded text-xs leading-none font-semibold bg-info-100 text-info':
                return 'Biru';
            case 'py-1 px-2 rounded text-xs leading-none font-semibold bg-dark/30 text-dark':
                return 'Gelap';
            default:
                return 'Belum Ditentukan';
        }

        return [];
    }

    public static function tagsColorTextToColor($text = null)
    {
        if ($text === null) {
            $map = ['Dasar','Nila','Teal', 'Oranye','Pink','Ungu','Hijau','Merah','Kuning','Biru','Gelap'];
            $randomKey = array_rand($map);
            $text = $map[$randomKey];
        }

        switch ($text) {
            case 'Dasar':
                return 'py-1 px-2 rounded text-xs leading-none font-semibold bg-primary-100 text-primary';

            case 'Nila':
                return 'py-1 px-2 rounded text-xs leading-none font-semibold bg-indigo-100 text-indigo-700';

            case 'Teal':
                return 'py-1 px-2 rounded text-xs leading-none font-semibold bg-teal-100 text-teal-600';

            case 'Oranye':
                return 'py-1 px-2 rounded text-xs leading-none font-semibold bg-orange-100 text-orange-600';

            case 'Pink':
                return 'py-1 px-2 rounded text-xs leading-none font-semibold bg-pink-100 text-pink';

            case 'Ungu':
                return 'py-1 px-2 rounded text-xs leading-none font-semibold bg-purple-100 text-purple';

            case 'Hijau':
                return 'py-1 px-2 rounded text-xs leading-none font-semibold bg-success-100 text-success';

            case 'Merah':
                return 'py-1 px-2 rounded text-xs leading-none font-semibold bg-danger-100 text-danger';

            case 'Kuning':
                return 'py-1 px-2 rounded text-xs leading-none font-semibold bg-warning-100 text-warning';

            case 'Biru':
                return 'py-1 px-2 rounded text-xs leading-none font-semibold bg-info-100 text-info';

            case 'Gelap':
                return 'py-1 px-2 rounded text-xs leading-none font-semibold bg-dark/30 text-dark';

            default:
                return null;
        }
    }

    public static function getJenisCode($jenis, $bentuk = null)
    {
        if ($jenis == 'DINAS')
            return "01";
        elseif ($jenis == 'SWASTA' && $bentuk == 'KOPERASI')
            return "02";
        elseif ($jenis == 'RUMAH SAKIT')
            return "03";
        elseif ($jenis == 'BUMN')
            return "04";
        elseif ($jenis == 'BUMD')
            return "05";
        elseif ($jenis == 'SWASTA' && $bentuk == 'PT')
            return "06";
        elseif ($jenis == 'SWASTA' && $bentuk == 'CV')
            return "07";
        elseif ($jenis == 'SEKOLAH')
            return "08";
        elseif ($jenis == 'SWASTA' && $bentuk == 'UD')
            return "09";
        elseif ($jenis == 'SWASTA' && $bentuk == 'FIRMA')
            return "09";
        // elseif ($bentuk == 'FIRMA')
        //     return "09";
        // elseif ($bentuk == 'UD')
            // return "10";
        else return "0";
    }

    public static function getInvoicesTotalSummary($invoice_id = 0, $invoice_termin = null)
    {
        $invoice = ProjectBukukas::with('tax', 'item', 'payments')
            // ->where('deleted', 0)
            ->findOrFail($invoice_id);
        // subtotal item
        $invoice_subtotal = $invoice->item ? $invoice->item->total : 0;

        // ambil semua payment yang tidak terhapus
        $payments = $invoice->payments->where('deleted', 0)->sortBy('payment_date');

        if ($invoice_termin) {
            // termin sebelumnya
            $payment = $payments->take(max(0, $invoice_termin - 1));
            $payment_subtotal = $payment->sum('total');

            // termin sekarang
            $current_payment = $payments->slice($invoice_termin - 1, 1)->first();
        } else {
            $payment_done = $payments->where('status', 'terbayar');
            $payment_subtotal = $payment_done->sum('total');

            $current_payment = $payments->where('status', 'belum-terbayar')->first();
        }

        $result = new \stdClass();

        $result->invoice_subtotal = $invoice_subtotal;
        $result->tax_percentage = $invoice->tax->percentage ?? 0;
        $result->tax_name = $invoice->tax->title ?? '';
        $result->diskon = $invoice->potongan;

        $result->tax = $invoice->tax->percentage ? round($invoice_subtotal * ($invoice->tax->percentage / 100), 0) : 0;
        $result->potongan = $invoice->potongan ? round($invoice_subtotal * ($invoice->potongan / 100), 0) : 0;

        $result->invoice_total = $invoice_subtotal + $result->tax + $result->potongan;
        $result->grand_total = $result->invoice_total;
        $result->grand_total_no_pph = $invoice_subtotal + $result->tax;

        $result->payment_subtotal = $payment_subtotal;
        $result->payment_subtotal_minus = $result->grand_total - $payment_subtotal;

        $result->payment_subtotal_termin = $current_payment->total ?? 0;

        $factorPPH = $result->grand_total ? round(($invoice_subtotal + $result->tax) / $result->grand_total, 8) : 0;

        $result->payment_subtotal_termin_no_pph = round($result->payment_subtotal_termin * $factorPPH);

        if ($invoice->potongan) {
            $result->payment_total_termin_no_ppn_with_pph = round($result->payment_subtotal_termin / (1 + ($result->tax_percentage/100) + ($invoice->potongan/100)), 0);
            $result->payment_pph_termin_no_ppn_with_pph = round($result->payment_total_termin_no_ppn_with_pph * ($invoice->potongan / 100), 0);
            $result->payment_ppn_termin_no_ppn_with_pph = round($result->payment_total_termin_no_ppn_with_pph * ($result->tax_percentage / 100), 0);
        } else {
            $result->payment_total_termin_no_ppn_with_pph = 0;
            $result->payment_pph_termin_no_ppn_with_pph = 0;
            $result->payment_ppn_termin_no_ppn_with_pph = 0;
        }

        $result->payment_total_termin_no_ppn = $result->tax_percentage
            ? round($result->payment_subtotal_termin_no_pph / (1 + ($result->tax_percentage / 100)), 0)
            : $result->payment_subtotal_termin_no_pph;

        $result->payment_ppn_termin_no_ppn = round($result->payment_total_termin_no_ppn * ($result->tax_percentage / 100), 0);

        $result->payment_done_subtotal = $payment_subtotal;
        $result->payment_done_subtotal_no_pph = round($payment_subtotal * $factorPPH);

        $subtotal_invoice = $payment_subtotal + ($current_payment->total ?? 0);

        $result->payment_invoice_total = $payments->sum('total');
        $result->payment_sisa = $result->grand_total - $subtotal_invoice;
        $result->payment_sisa_no_pph = $result->grand_total_no_pph - $subtotal_invoice;

        // $result->termin_terbayar = $invoice_termin ? ($invoice_termin > 2 ? "Termin 1 - " . ($invoice_termin - 1) : "Termin 1") : 0;
        // $result->termin = $invoice_termin ? "Termin " . $invoice_termin : 0;

        $result->percentage_done = $result->grand_total ? round($result->payment_done_subtotal / $result->grand_total * 100, 0) : 0;
        $result->payment_date = $current_payment->payment_date ?? null;
        $result->invoice_code = $current_payment->invoice_code ?? null;
        $result->percentage_now = $result->grand_total ? round($result->payment_subtotal_termin / $result->grand_total * 100, 0) : 0;

        $result->balance_due = number_format($result->invoice_total, 2, ".", "");

        return $result;
    }

    public static function prioritySlugToText($slug = null)
    {
        switch ($slug) {
            case 'low':
                return 'Low';
            case 'medium':
                return 'Medium';
            case 'high':
                return 'High';
            default:
                return 'tidak terdefinisi';
        }
    }

    public static function statusSlugToText($slug = null)
    {
        switch ($slug) {
            case 'open':
                return 'Terbuka';

            case 'waiting-approval':
                return 'Menunggu Persetujuan';

            case 'approval-done':
                return 'Pengajuan Selesai';

            case 'on-progress':
                return 'Sedang Diproses';

            case 'customer-reply':
                return 'Balasan Pelanggan';

            case 'pending':
                return 'Tertunda';

            case 'resolved':
                return 'Terselesaikan';

            case 'closed':
                return 'Selesai';

            case 'reopened':
                return 'Dibuka Kembali';

            case 'rejected':
                return 'Ditolak';

            default:
                return 'Status Tidak Diketahui';
        }
    }

    public static function typeSlugToText($slug = null)
    {
        switch ($slug) {
            case 'support':
                return 'Bantuan';
            case 'complaint':
                return 'Komplain';
            case 'question':
                return 'Pertanyaan';
            default:
                return 'tidak terdefinisi';
        }
    }

    public static function generateTicketNumber(): string
    {
        $date = Carbon::now()->format('Ymd');

        $lastTicket = Ticket::whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->first();

        $lastNumber = 0;

        if ($lastTicket && preg_match('/(\d+)$/', $lastTicket->ticket_number, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return "TK-{$date}-{$nextNumber}";
    }

    public static function formatFileSize($bytes)
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }


}
