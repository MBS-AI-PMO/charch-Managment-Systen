<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MemberLookupController extends Controller
{
    public function search(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        return response()->json(
            User::where('name', 'like', "%$q%")
                ->orderBy('name')
                ->limit(10)
                ->get(['id', 'name'])
        );
    }
}
