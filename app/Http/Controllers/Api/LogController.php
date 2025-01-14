<?php

namespace App\Http\Controllers\Api;

use App\Models\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogController extends Controller
{
    /**
     * Allows downloading all logs in CSV format.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadLogs()
    {
        // Optional: you could check if the user is 'admin' before allowing the download
        // if (!auth()->user()->isAdmin()) {
        //     return response()->json(['error' => 'You do not have permission to download logs'], 403);
        // }

        // Create a streamed response to generate the CSV dynamically
        $fileName = 'logs_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () {
            // 1. Get all logs (you can paginate, filter, etc. if necessary)
            $logs = Log::with('user')->orderBy('created_at', 'desc')->get();

            // 2. CSV headers
            $headers = ['id', 'user_id', 'user_email', 'endpoint', 'method', 'ip_address', 'created_at'];

            // 3. Print the header line
            echo implode(',', $headers) . "\n";

            // 4. Iterate through logs and print fields
            foreach ($logs as $log) {
                echo implode(',', [
                    $log->id,
                    $log->user_id ?: '',
                    $log->user ? $log->user->email : '',
                    $log->endpoint,
                    $log->method,
                    $log->ip_address ?: '',
                    $log->created_at
                ]) . "\n";
            }

            // In a real case, consider replacing commas with quotes if there are fields containing commas,
            // and handle line breaks for robust CSV formatting.
        }, $fileName, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ]);
    }
}
