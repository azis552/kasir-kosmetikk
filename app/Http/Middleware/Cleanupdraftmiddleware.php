<?php

namespace App\Http\Middleware;

use App\Models\Transaction;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CleanupDraftMiddleware
{
    /**
     * Cleanup transaksi DRAFT idle — jalan max sekali per 30 menit.
     * Cocok untuk environment tanpa cron (Windows/desktop).
     *
     * Cara kerja:
     * - Setiap request masuk, cek cache key 'cleanup_draft_last_run'
     * - Jika belum ada atau sudah > 30 menit → jalankan cleanup
     * - Tandai cache key baru → cleanup tidak jalan lagi selama 30 menit ke depan
     * - Tidak memblokir response (cleanup jalan setelah response dikirim)
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        // Hanya jalan jika belum ada flag dalam 30 menit terakhir
        if (Cache::has('cleanup_draft_last_run')) {
            return;
        }

        try {
            $idleMinutes = 60;
            $expired     = now()->subMinutes($idleMinutes);

            $drafts = Transaction::where('status', 'DRAFT')
                ->where('updated_at', '<', $expired)
                ->get();

            foreach ($drafts as $trx) {
                if ($trx->details()->count() > 0) {
                    $trx->update(['status' => 'VOID']);
                } else {
                    $trx->delete();
                }
            }

            // Tandai sudah jalan — tidak akan jalan lagi selama 30 menit
            Cache::put('cleanup_draft_last_run', true, now()->addMinutes(30));

        } catch (\Throwable $e) {
            // Jangan sampai cleanup error mempengaruhi response
            \Log::warning('CleanupDraft error: ' . $e->getMessage());
        }
    }
}