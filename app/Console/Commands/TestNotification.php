<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notifikasi;
use App\Models\User;

class TestNotification extends Command
{
    protected $signature = 'notification:test';
    protected $description = 'Test notification system';

    public function handle()
    {
        $this->info('=== TESTING NOTIFICATION SYSTEM ===');
        
        // Check if table has new columns
        $this->info("\n📊 CHECKING NOTIFICATION TABLE:");
        try {
            $notifications = Notifikasi::all();
            $this->info("Total notifications: " . $notifications->count());
            
            if ($notifications->count() > 0) {
                $sample = $notifications->first();
                $this->info("Sample notification structure:");
                $this->line("- ID: " . $sample->id);
                $this->line("- User ID: " . $sample->user_id);
                $this->line("- Judul: " . $sample->judul);
                $this->line("- Pesan: " . $sample->pesan);
                $this->line("- Tipe: " . ($sample->tipe ?? 'NULL'));
                $this->line("- Prioritas: " . ($sample->prioritas ?? 'NULL'));
                $this->line("- Action URL: " . ($sample->action_url ?? 'NULL'));
                $this->line("- Is Read: " . ($sample->is_read ? 'Yes' : 'No'));
            }
        } catch (\Exception $e) {
            $this->error("Error checking notifications: " . $e->getMessage());
        }
        
        // Check users
        $this->info("\n👥 CHECKING USERS:");
        $users = User::all();
        $this->info("Total users: " . $users->count());
        
        foreach ($users as $user) {
            $this->line("- ID: {$user->id}, Name: {$user->nama_lengkap}, Role: {$user->role}");
        }
        
        $this->info("\n✅ Notification system test completed!");
    }
}
