<?php

namespace App\Http\Controllers;

use App\Models\Klaim;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KlaimController extends Controller
{
    public function index(): View
    {
        $klaims = Klaim::latest()->get();

        return view('klaim.index', compact('klaims'));
    }

    public function create(): View
    {
        return view('klaim.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Klaim::create($request->validate($this->rules()));

        return redirect()->route('klaim.index')
            ->with('success', 'Data klaim berhasil ditambahkan.');
    }

    public function edit(Klaim $klaim): View
    {
        return view('klaim.edit', compact('klaim'));
    }

    public function update(Request $request, Klaim $klaim): RedirectResponse
    {
        $klaim->update($request->validate($this->rules()));

        return redirect()->route('klaim.index')
            ->with('success', 'Data klaim berhasil diperbarui.');
    }

    public function destroy(Klaim $klaim): RedirectResponse
    {
        $klaim->delete();

        return redirect()->route('klaim.index')
            ->with('success', 'Data klaim berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'no_klaim' => ['required', 'string', 'max:50'],
            'no_polis' => ['required', 'string', 'max:50'],
            'nama_tertanggung' => ['required', 'string', 'max:255'],
            'penyebab_klaim' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'total_nilai_klaim' => ['required', 'numeric', 'min:0'],
            'up_utama' => ['required', 'numeric', 'min:0'],
            'recovery' => ['required', 'numeric', 'min:0'],
            'up_ceded' => ['required', 'numeric', 'min:0'],
            'status_klaim' => ['required', 'string', 'max:50'],
        ];
    }
}