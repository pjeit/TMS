<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JOTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCanShowJOPage()
    {
        // php artisan make:test JOTest
        // php artisan test --filter CanShowJOPage
        // $user = User::role('Super Admin')->get()->random();
        $user = User::role('Super Admin')->get()->first();
        $this->actingAs($user);
        $this->get('/job_order')->assertOk();
    }

    public function testCantShowJOPage()
    {
        // php artisan test --filter CantShowJOPage
        $user = User::role('Admin')->get()->first();
        // dd( $user );
        $this->actingAs($user)
        ->get('job_order')
        ->assertStatus(302) // salah harusnya 403
        ->assertSeeText('localhost'); // buat cek teks errornya, buat di match in
    }
}
