<?php

if (! function_exists('log_activity')) {
    /**
     * Mencatat aktivitas user ke database.
     *
     * @param string $action      Aksi yang dilakukan (e.g., 'update_product', 'login')
     * @param string $description Detail dari aksi
     */
    function log_activity(string $action, string $description = '')
    {
        try {
            $logModel = new \App\Models\ActivityLogModel();
            $userId = session()->get('user_id') ?? null;
            
            $logModel->save([
                'user_id'     => $userId,
                'action'      => $action,
                'description' => $description,
                'created_at'  => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            // Gagal logging, jangan hentikan aplikasi
            log_message('error', 'Gagal mencatat log aktivitas: ' . $e->getMessage());
        }
    }
}