<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class QueueManagementController extends Controller
{
    public function index()
    {
        $connection = config('queue.default');
        $jobs = [];
        $failedJobs = [];
        $jobHistory = [];
        $commandHistory = [];

        if ($connection === 'database') {
            $jobs = DB::table('jobs')
                ->orderBy('id', 'desc')
                ->paginate(20, ['*'], 'pending_page');

            $failedJobs = DB::table('failed_jobs')
                ->orderBy('failed_at', 'desc')
                ->paginate(20, ['*'], 'failed_page');
        }

        $jobHistory = DB::table('job_history')
            ->orderBy('completed_at', 'desc')
            ->paginate(20, ['*'], 'history_page');

        $commandHistory = DB::table('command_history')
            ->orderBy('completed_at', 'desc')
            ->paginate(10, ['*'], 'command_page');

        $stats = [
            'total_jobs' => DB::table('jobs')->count(),
            'failed_jobs' => DB::table('failed_jobs')->count(),
            'completed_jobs' => DB::table('job_history')->where('status', 'completed')->count(),
            'total_history' => DB::table('job_history')->count(),
            'total_commands' => DB::table('command_history')->count(),
            'queue_paused' => Cache::get('queue:paused', false),
            'connection' => $connection,
        ];

        return view('dashboard.admin.queue.index', [
            'jobs' => $jobs,
            'failedJobs' => $failedJobs,
            'jobHistory' => $jobHistory,
            'commandHistory' => $commandHistory,
            'stats' => $stats,
        ]);
    }

    public function pause()
    {
        Cache::put('queue:paused', true);

        return back()->with('success', 'Queue has been paused. New jobs will not be processed.');
    }

    public function resume()
    {
        Cache::forget('queue:paused');

        return back()->with('success', 'Queue has been resumed. Jobs will be processed normally.');
    }

    public function retry(string $id)
    {
        Artisan::call('queue:retry', ['id' => [$id]]);

        return back()->with('success', 'Failed job has been queued for retry.');
    }

    public function retryAll()
    {
        Artisan::call('queue:retry', ['id' => ['all']]);

        return back()->with('success', 'All failed jobs have been queued for retry.');
    }

    public function deleteJob(string $id)
    {
        DB::table('jobs')->where('id', $id)->delete();

        return back()->with('success', 'Job has been deleted from the queue.');
    }

    public function deleteFailed(string $id)
    {
        Artisan::call('queue:forget', ['id' => $id]);

        return back()->with('success', 'Failed job has been deleted.');
    }

    public function flush()
    {
        Artisan::call('queue:flush');

        return back()->with('success', 'All failed jobs have been deleted.');
    }

    public function clear()
    {
        DB::table('jobs')->truncate();

        return back()->with('success', 'All pending jobs have been cleared from the queue.');
    }

    public function restart()
    {
        Artisan::call('queue:restart');

        return back()->with('success', 'Queue workers will gracefully restart after finishing their current jobs.');
    }

    public function work()
    {
        Artisan::call('queue:work', [
            '--once' => true,
            '--tries' => 3,
        ]);

        return back()->with('success', 'Processed one job from the queue.');
    }
}
