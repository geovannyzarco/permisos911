<?php

namespace App\Http\Controllers;

use App\Models\Marcacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\MarcacionImportService;

class MarcacionImportController extends Controller
{
    public function import(Request $request, MarcacionImportService $service)
    {
        dd($request->file('file'));
        $request->validate([
            'file' => ['required', 'file', 'mimes:txt'],
        ]);

        $result = $service->importFromTxt(
            $request->file('file')->getRealPath()
        );

        return back()->with('success', $result);
    }
}
