<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeAdminRoute extends Command
{
    protected $signature = 'make:admin-route';

    protected $description = 'Create routes/admin.php file with default structure';

    public function handle()
    {
        $path = base_path('routes/admin.php');

        if (File::exists($path)) {
            $this->error('⚠️ routes/admin.php already exists!');
            return Command::FAILURE;
        }

        $content = <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->as('admin.')
    ->middleware(['web', 'auth', 'admin'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return "Welcome to Admin Dashboard!";
        })->name('dashboard');
    });

PHP;

        File::put($path, $content);

        $this->info('✅ routes/admin.php created successfully!');
        return Command::SUCCESS;
    }
}
