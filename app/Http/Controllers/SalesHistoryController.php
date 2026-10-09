<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SalesHistoryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(in_array($request->user()->role, ['admin', 'manager'], true), 403);

        $sales = Sale::with(['customer', 'user', 'items.product'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = trim((string) $request->input('q'));
                $q->where(function ($sub) use ($term) {
                    $sub->where('invoice_no', 'like', "%{$term}%")
                        ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('sales.index', [
            'sales' => $sales,
            'search' => (string) $request->input('q', ''),
            'from' => (string) $request->input('from', ''),
            'to' => (string) $request->input('to', ''),
        ]);
    }
}
