<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'product', 'variant'])
            ->latest()
            ->get();

        return view('seller.electronic-installment.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);

        return view('seller.electronic-installment.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'status' => $request->status,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Status order berhasil diperbarui.');
    }

    public function delete($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()
            ->back()
            ->with('success', 'Order berhasil dihapus.');
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'serial_number' => 'required|string|max:255',
            'imei' => 'required|string|max:255',
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'serial_number' => $request->serial_number,
            'imei' => $request->imei,
            'status' => 'menunggu pembayaran',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Order berhasil diterima.');
    }
}
