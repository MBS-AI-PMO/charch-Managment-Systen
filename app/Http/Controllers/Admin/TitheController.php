<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tithe;
use App\Models\TitheFund;
use App\Models\User;
use Illuminate\Http\Request;
use Mews\Purifier\Facades\Purifier;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TitheController extends Controller
{
    public function index(Request $request)
    {
        $q = Tithe::with(['user', 'fund', 'recorder'])->latest('received_at');

        if ($fund = $request->input('fund')) {
            $q->where('fund_id', $fund);
        }
        if ($method = $request->input('method')) {
            $q->where('method', $method);
        }
        if ($search = $request->input('q')) {
            $q->where(function ($qq) use ($search) {
                $qq->where('giver_name', 'like', "%$search%")
                    ->orWhere('reference', 'like', "%$search%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%$search%"));
            });
        }

        return view('admin.tithes.index', [
            'tithes' => $q->paginate(25)->withQueryString(),
            'funds' => TitheFund::active()->get(),
            'filters' => $request->only(['fund', 'method', 'q']),
        ]);
    }

    public function create()
    {
        return view('admin.tithes.create', [
            'funds' => TitheFund::active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['amount_cents'] = (int) round(((float) $data['amount']) * 100);
        unset($data['amount']);

        if (! empty($data['user_id'])) {
            $data['giver_name'] = User::find($data['user_id'])?->name;
        }

        $data['note'] = ! empty($data['note']) ? Purifier::clean($data['note'], 'cms') : null;
        $data['recorded_by'] = $request->user()->id;

        Tithe::create($data);

        return redirect()->route('admin.tithes.index')->with('status', 'Gift recorded.');
    }

    public function edit(Tithe $tithe)
    {
        return view('admin.tithes.edit', [
            'tithe' => $tithe,
            'funds' => TitheFund::active()->get(),
        ]);
    }

    public function update(Request $request, Tithe $tithe)
    {
        $data = $this->validateData($request);
        $data['amount_cents'] = (int) round(((float) $data['amount']) * 100);
        unset($data['amount']);

        if (! empty($data['user_id'])) {
            $data['giver_name'] = User::find($data['user_id'])?->name;
        }

        $data['note'] = ! empty($data['note']) ? Purifier::clean($data['note'], 'cms') : null;

        $tithe->update($data);

        return redirect()->route('admin.tithes.index')->with('status', 'Gift updated.');
    }

    public function destroy(Tithe $tithe)
    {
        $tithe->delete();

        return back()->with('status', 'Gift deleted.');
    }

    public function export(Request $request): StreamedResponse
    {
        $q = Tithe::with(['user', 'fund', 'recorder'])->latest('received_at');
        if ($fund = $request->input('fund')) {
            $q->where('fund_id', $fund);
        }

        $filename = 'tithes-'.now()->toDateString().'.csv';

        return response()->streamDownload(function () use ($q) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Giver', 'Fund', 'Method', 'Amount', 'Reference', 'Recorded By']);
            foreach ($q->cursor() as $t) {
                fputcsv($out, [
                    $t->received_at?->toDateString(),
                    self::csvSafe($t->giverDisplayName()),
                    $t->fund->name,
                    $t->method,
                    number_format($t->amount_cents / 100, 2),
                    self::csvSafe((string) $t->reference),
                    self::csvSafe($t->recorder?->name ?? ''),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'giver_name' => ['nullable', 'string', 'max:160'],
            'fund_id' => ['required', 'integer', 'exists:tithe_funds,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'in:cash,bank_transfer,cheque,other'],
            'received_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private static function csvSafe(string $value): string
    {
        if ($value !== '' && in_array($value[0], ['=', '+', '-', '@'], true)) {
            return "'".$value;
        }

        return $value;
    }
}
