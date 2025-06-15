<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DoctorScopeTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleScopeTestSeeder::class);
    }

    public function test_asm_sees_only_doctors_in_their_area_headquarters()
    {
        $asm = User::where('name', 'ASM User')->first();
        $this->actingAs($asm);

        $doctors = Doctor::all();
        $doctorNames = $doctors->pluck('name')->toArray();

        // ASM should see Doctor 1 and Doctor 2 (both in area 1's HQs)
        $this->assertContains('Doctor 1', $doctorNames);
        $this->assertContains('Doctor 2', $doctorNames);
        $this->assertNotContains('Doctor 3', $doctorNames);
    }

    public function test_rsm_sees_all_doctors_in_their_region()
    {
        $rsm = User::where('name', 'RSM User')->first();
        $this->actingAs($rsm);

        $doctors = Doctor::all();
        $doctorNames = $doctors->pluck('name')->toArray();

        // RSM should see all doctors in their region (all 3)
        $this->assertContains('Doctor 1', $doctorNames);
        $this->assertContains('Doctor 2', $doctorNames);
        $this->assertContains('Doctor 3', $doctorNames);
    }

    public function test_dsa_sees_only_their_own_doctors()
    {
        $dsa = User::where('name', 'DSA User')->first();
        $this->actingAs($dsa);

        $doctors = Doctor::all();
        $doctorNames = $doctors->pluck('name')->toArray();

        // DSA should see only Doctor 1
        $this->assertContains('Doctor 1', $doctorNames);
        $this->assertNotContains('Doctor 2', $doctorNames);
        $this->assertNotContains('Doctor 3', $doctorNames);
    }
} 