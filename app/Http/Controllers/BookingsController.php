<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingsController extends Controller
{
    public function index(Request $request)
    {
        $selectedMonth = $request->input('month', Carbon::now()->format('F'));
        return view('lms.bookings', [
            'selectedMonth' => $selectedMonth,
        ]);
    }

    public function apiIndex(Request $request)
    {
        $selectedMonth = $request->query('month', Carbon::now()->format('F'));
        $query = Payment::query();
        if ($selectedMonth) {
            $monthNumber = Carbon::parse($selectedMonth)->month;
            $query->whereMonth('created_at', $monthNumber);
        }
        $bookings = $query->get();
        return response()->json($bookings);
    }

    public function getMonths()
    {
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        return response()->json($months);
    }

    public function destroy($id)
    {
        $booking = Payment::findOrFail($id);
        $booking->delete();
        return response()->json(['success' => 'Booking deleted successfully.']);
    }
}