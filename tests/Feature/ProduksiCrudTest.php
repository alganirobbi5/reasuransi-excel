<?php

namespace Tests\Feature;

use App\Models\Produksi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProduksiCrudTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'no_polis' => 'AJ-000123',
            'nama_tertanggung' => 'Budi Santoso',
            'tanggal_lahir' => '1990-05-10',
            'up_utama' => 1000000000,
            'retention' => 200000000,
            'up_ceded' => 800000000,
            'jenis_reasuransi' => 'Surplus',
            'premi_reasuransi' => 2500000,
        ], $overrides);
    }

    public function test_index_lists_produksi(): void
    {
        Produksi::create($this->validPayload());

        $response = $this->get(route('produksi.index'));

        $response->assertOk();
        $response->assertSee('AJ-000123');
        $response->assertSee('Budi Santoso');
    }

    public function test_create_shows_form(): void
    {
        $this->get(route('produksi.create'))->assertOk();
    }

    public function test_store_persists_valid_data(): void
    {
        $response = $this->post(route('produksi.store'), $this->validPayload());

        $response->assertRedirect(route('produksi.index'));
        $this->assertDatabaseHas('produksis', ['no_polis' => 'AJ-000123']);
        $this->assertEquals(1, Produksi::count());
    }

    public function test_store_rejects_invalid_data(): void
    {
        $response = $this->post(route('produksi.store'), $this->validPayload([
            'no_polis' => '',
            'tanggal_lahir' => 'not-a-date',
            'up_utama' => -100,
            'premi_reasuransi' => 'abc',
        ]));

        $response->assertSessionHasErrors(['no_polis', 'tanggal_lahir', 'up_utama', 'premi_reasuransi']);
        $this->assertEquals(0, Produksi::count());
    }

    public function test_store_rejects_future_birth_date(): void
    {
        $response = $this->post(route('produksi.store'), $this->validPayload([
            'tanggal_lahir' => now()->addDay()->format('Y-m-d'),
        ]));

        $response->assertSessionHasErrors('tanggal_lahir');
        $this->assertEquals(0, Produksi::count());
    }

    public function test_update_modifies_data(): void
    {
        $produksi = Produksi::create($this->validPayload());

        $response = $this->put(
            route('produksi.update', $produksi),
            $this->validPayload(['nama_tertanggung' => 'Andi Wijaya', 'premi_reasuransi' => 3000000])
        );

        $response->assertRedirect(route('produksi.index'));
        $this->assertDatabaseHas('produksis', [
            'id' => $produksi->id,
            'nama_tertanggung' => 'Andi Wijaya',
            'premi_reasuransi' => 3000000,
        ]);
    }

    public function test_update_rejects_invalid_data(): void
    {
        $produksi = Produksi::create($this->validPayload());

        $this->put(route('produksi.update', $produksi), $this->validPayload(['retention' => -5]))
            ->assertSessionHasErrors('retention');

        $this->assertEquals(200000000, (float) $produksi->fresh()->retention);
    }

    public function test_destroy_removes_data(): void
    {
        $produksi = Produksi::create($this->validPayload());

        $response = $this->delete(route('produksi.destroy', $produksi));

        $response->assertRedirect(route('produksi.index'));
        $this->assertDatabaseMissing('produksis', ['id' => $produksi->id]);
    }
}
