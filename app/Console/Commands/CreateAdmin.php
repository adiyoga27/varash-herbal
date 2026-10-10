<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CreateAdmin extends Command
{
    protected $signature = 'create:admin
                            {--name= : Nama admin}
                            {--email= : Email untuk login}
                            {--password= : Password (min. 8 karakter)}
                            {--role= : superadmin, admin, atau editor}';

    protected $description = 'Membuat akun admin baru untuk panel /admin';

    public function handle(): int
    {
        $this->components->info('Buat akun admin '.setting('site_name', config('app.name')));

        $data = [
            'name' => $this->option('name') ?? text(
                label: 'Nama',
                placeholder: 'Budi Santoso',
                required: true,
            ),
            'email' => $this->option('email') ?? text(
                label: 'Email',
                placeholder: 'admin@varashherbal.com',
                required: true,
                validate: fn (string $value) => $this->validateField('email', $value),
            ),
            'password' => $this->option('password') ?? password(
                label: 'Password',
                placeholder: 'Minimal 8 karakter',
                required: true,
                validate: fn (string $value) => $this->validateField('password', $value),
            ),
            'role' => $this->option('role') ?? select(
                label: 'Role',
                options: User::ROLES,
                default: 'superadmin',
                hint: 'Super Admin dapat mengelola pengguna, pengaturan, dan system log.',
            ),
        ];

        // Validasi ulang semua field (penting saat dijalankan non-interaktif via --option)
        $validator = Validator::make($data, $this->rules());
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $user = User::create($data + ['is_active' => true]);

        ActivityLogger::log('created_via_console', "Akun admin {$user->email} dibuat melalui artisan create:admin", $user, ['role' => $user->role], 'auth', 'warning');

        $this->components->info('Akun admin berhasil dibuat.');
        $this->components->twoColumnDetail('Nama', $user->name);
        $this->components->twoColumnDetail('Email', $user->email);
        $this->components->twoColumnDetail('Role', User::ROLES[$user->role]);
        $this->components->twoColumnDetail('Login', route('admin.login'));

        return self::SUCCESS;
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')],
            'password' => ['required', Password::min(8)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
        ];
    }

    /** Validasi satu field untuk prompt interaktif; mengembalikan pesan error atau null. */
    private function validateField(string $field, string $value): ?string
    {
        $validator = Validator::make([$field => $value], [$field => $this->rules()[$field]]);

        return $validator->fails() ? $validator->errors()->first($field) : null;
    }
}
