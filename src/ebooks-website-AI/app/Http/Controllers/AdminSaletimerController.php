<?php

namespace App\Http\Controllers;

use App\Models\Saletimer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminSaletimerController extends Controller
{
    public function index()
    {
        // Lấy bản ghi saletimer đầu tiên, nếu không có thì tạo mới
        $saletimer = Saletimer::first();
        if (!$saletimer) {
            $saletimer = Saletimer::create([
                'sale_timer' => Carbon::now()->addDays(7), // Mặc định 7 ngày sau
                'status' => 0, // Tắt Flash Sale mặc định
                'end_date' => Carbon::now()->addDays(14), // Kết thúc sau 14 ngày
            ]);
        }

        return view('admin.saletimer.index', compact('saletimer'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'sale_timer' => 'required|date',
            'status' => 'required|boolean',
            'end_date' => 'nullable|date|after:sale_timer',
        ]);

        $saletimer = Saletimer::first();
        if (!$saletimer) {
            $saletimer = new Saletimer();
        }

        $saletimer->update([
            'sale_timer' => $request->sale_timer,
            'status' => $request->status,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('admin.saletimer.index')->with('success', 'Cập nhật Flash Sale thành công!');
    }
}