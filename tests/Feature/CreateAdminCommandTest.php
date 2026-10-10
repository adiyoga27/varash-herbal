<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_admin_with_options(): void
    {
        $this->artisan('create:admin', [
            '--name' => 'Wayan',
            '--email' => 'wayan@varashherbal.com',
            '--password' => 'rahasia123',
            '--role' => 'admin',
        ])->assertSuccessful();

        $user = User::where('email', 'wayan@varashherbal.com')->firstOrFail();
        $this->assertSame('admin', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
        $this->assertDatabaseHas('activity_logs', ['event' => 'created_via_console', 'subject_id' => $user->id]);
    }

    public function test_creates_admin_interactively(): void
    {
        $this->artisan('create:admin')
            ->expectsQuestion('Nama', 'Putu')
            ->expectsQuestion('Email', 'putu@varashherbal.com')
            ->expectsQuestion('Password', 'rahasia123')
            ->expectsQuestion('Role', 'superadmin')
            ->assertSuccessful();

        $this->assertSame('superadmin', User::where('email', 'putu@varashherbal.com')->value('role'));
    }

    public function test_rejects_invalid_input(): void
    {
        User::create(['name' => 'Ada', 'email' => 'ada@x.com', 'password' => 'password123']);

        $this->artisan('create:admin', ['--name' => 'X', '--email' => 'ada@x.com', '--password' => '123', '--role' => 'raja'])
            ->assertFailed();

        $this->assertSame(1, User::count());
    }
}
