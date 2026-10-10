<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.orders.index', [
            'orders' => $this->filtered($request)->withCount('items')->paginate(15)->withQueryString(),
            'counts' => Order::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(Order $order)
    {
        $order->load('items.product');

        return view('admin.orders.show', [
            'order' => $order,
            'logs' => ActivityLog::with('user')
                ->where('subject_type', $order->getMorphClass())->where('subject_id', $order->id)
                ->latest('id')->get(),
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'shipping_cost' => ['nullable', 'integer', 'min:0'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['shipping_cost'] = (int) ($data['shipping_cost'] ?? 0);
        $data['total'] = $order->subtotal + $data['shipping_cost'];
        $order->update($data);

        return back()->with('success', 'Pesanan diperbarui.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Pesanan dihapus.');
    }

    public function export(Request $request): StreamedResponse
    {
        $orders = $this->filtered($request)->with('items')->get();
        ActivityLogger::log('exported', 'Export '.$orders->count().' data pesanan ke CSV', null, $request->query(), 'order');

        return response()->streamDownload(function () use ($orders) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
            fputcsv($out, ['Kode', 'Tanggal', 'Nama', 'No. HP', 'Email', 'Alamat', 'Kota', 'Provinsi', 'Produk', 'Subtotal', 'Ongkir', 'Total', 'Pembayaran', 'Status']);
            foreach ($orders as $o) {
                fputcsv($out, [
                    $o->code, $o->created_at->format('Y-m-d H:i'), $o->customer_name, $o->customer_phone, $o->customer_email,
                    $o->address, $o->city, $o->province,
                    $o->items->map(fn ($i) => "{$i->product_name} x{$i->quantity}")->implode('; '),
                    $o->subtotal, $o->shipping_cost, $o->total, $o->paymentLabel(), $o->statusLabel(),
                ]);
            }
            fclose($out);
        }, 'pesanan-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    private function filtered(Request $request)
    {
        $query = Order::latest();

        if ($search = $request->query('q')) {
            $query->where(fn ($q) => $q->where('code', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")
                ->orWhere('customer_phone', 'like', "%{$search}%"));
        }
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($from = $request->query('from')) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }
}
