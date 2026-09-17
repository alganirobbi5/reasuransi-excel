<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProduksiController extends Controller
{
    public function index(): View
    {
        $produksis = Produksi::latest()->get();

        return view('produksi.index', compact('produksis'));
    }

    public function create(): View
    {
        return view('produksi.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Produksi::create($request->validate($this->rules()));

        return redirect()->route('produksi.index')
            ->with('success', 'Data produksi berhasil ditambahkan.');
    }

    public function edit(Produksi $produksi): View
    {
        return view('produksi.edit', compact('produksi'));
    }

    public function update(Request $request, Produksi $produksi): RedirectResponse
    {
        $produksi->update($request->validate($this->rules()));

        return redirect()->route('produksi.index')
            ->with('success', 'Data produksi berhasil diperbarui.');
    }

    public function destroy(Produksi $produksi): RedirectResponse
    {
        $produksi->delete();

        return redirect()->route('produksi.index')
            ->with('success', 'Data produksi berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        return [
            'no_polis' => ['required', 'string', 'max:50'],
            'nama_tertanggung' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'up_utama' => ['required', 'numeric', 'min:0'],
            'retention' => ['required', 'numeric', 'min:0'],
            'up_ceded' => ['required', 'numeric', 'min:0'],
            'jenis_reasuransi' => ['required', 'string', 'max:50'],
            'premi_reasuransi' => ['required', 'numeric', 'min:0'],
        ];
    }
}