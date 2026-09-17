<?php

namespace Tests\Feature;

use App\Models\Klaim;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KlaimCrudTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'no_klaim' => 'KLM-000123',
            'no_polis' => 'AJ-000123',
            'nama_tertanggung' => 'Siti Aminah',
            'penyebab_klaim' => 'Sakit',
            'tanggal_lahir' => '1985-03-20',
            'total_nilai_klaim' => 1500000000,
            'up_utama' => 1000000000,
            'recovery' => 700000000,
            'up_ceded' => 800000000,
            'status_klaim' => 'Dalam Investigasi',
        ], $overrides);
    }

    public function test_index_lists_klaim(): void
    {
        Klaim::create($this->validPayload());

        $response = $this->get(route('klaim.index'));

        $response->assertOk();
        $response->assertSee('KLM-000123');
        $response->assertSee('Siti Aminah');
    }

    public function test_create_shows_form(): void
    {
        $this->get(route('klaim.create'))->assertOk();
    }

    public function test_store_persists_valid_data(): void
    {
        $response = $this->post(route('klaim.store'), $this->validPayload());

        $response->assertRedirect(route('klaim.index'));
        $this->assertDatabaseHas('klaims', ['no_klaim' => 'KLM-000123']);
        $this->assertEquals(1, Klaim::count());
    }

    public function test_store_rejects_invalid_data(): void
    {
        $response = $this->post(route('klaim.store'), $this->validPayload([
            'no_klaim' => '',
            'penyebab_klaim' => '',
            'tanggal_lahir' => 'not-a-date',
            'recovery' => -50,
            'status_klaim' => '',
        ]));

        $response->assertSessionHasErrors(['no_klaim', 'penyebab_klaim', 'tanggal_lahir', 'recovery', 'status_klaim']);
        $this->assertEquals(0, Klaim::count());
    }

    public function test_store_rejects_future_birth_date(): void
    {
        $response = $this->post(route('klaim.store'), $this->validPayload([
            'tanggal_lahir' => now()->addDay()->format('Y-m-d'),
        ]));

        $response->assertSessionHasErrors('tanggal_lahir');
        $this->assertEquals(0, Klaim::count());
    }

    public function test_update_modifies_data(): void
    {
        $klaim = Klaim::create($this->validPayload());

        $response = $this->put(
            route('klaim.update', $klaim),
            $this->validPayload(['status_klaim' => 'Approved/Siap Dibayarkan', 'recovery' => 750000000])
        );

        $response->assertRedirect(route('klaim.index'));
        $this->assertDatabaseHas('klaims', [
            'id' => $klaim->id,
            'status_klaim' => 'Approved/Siap Dibayarkan',
            'recovery' => 750000000,
        ]);
    }

    public function test_update_rejects_invalid_data(): void
    {
        $klaim = Klaim::create($this->validPayload());

        $this->put(route('klaim.update', $klaim), $this->validPayload(['up_ceded' => -5]))
            ->assertSessionHasErrors('up_ceded');

        $this->assertEquals(800000000, (float) $klaim->fresh()->up_ceded);
    }

    public function test_destroy_removes_data(): void
    {
        $klaim = Klaim::create($this->validPayload());

        $response = $this->delete(route('klaim.destroy', $klaim));

        $response->assertRedirect(route('klaim.index'));
        $this->assertDatabaseMissing('klaims', ['id' => $klaim->id]);
    }
}
