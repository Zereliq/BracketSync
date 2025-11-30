<?php

namespace App\Listeners;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\DB;

class LogJobHistory
{
    private array $jobStartTimes = [];

    public function handleProcessing(JobProcessing $event): void
    {
        $jobId = $event->job->getJobId();
        $this->jobStartTimes[$jobId] = microtime(true);
    }

    public function handleProcessed(JobProcessed $event): void
    {
        $jobId = $event->job->getJobId();
        $startTime = $this->jobStartTimes[$jobId] ?? microtime(true);
        $duration = (int) ((microtime(true) - $startTime) * 1000);

        $payload = json_decode($event->job->getRawBody(), true);
        $jobName = $payload['displayName'] ?? $event->job->resolveName();

        DB::table('job_history')->insert([
            'queue' => $event->job->getQueue(),
            'job_name' => $jobName,
            'payload' => $event->job->getRawBody(),
            'status' => 'completed',
            'exception' => null,
            'attempts' => $event->job->attempts(),
            'started_at' => now()->subMilliseconds($duration),
            'completed_at' => now(),
            'duration_ms' => $duration,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        unset($this->jobStartTimes[$jobId]);
    }

    public function handleFailed(JobFailed $event): void
    {
        $jobId = $event->job->getJobId();
        $startTime = $this->jobStartTimes[$jobId] ?? microtime(true);
        $duration = (int) ((microtime(true) - $startTime) * 1000);

        $payload = json_decode($event->job->getRawBody(), true);
        $jobName = $payload['displayName'] ?? $event->job->resolveName();

        DB::table('job_history')->insert([
            'queue' => $event->job->getQueue(),
            'job_name' => $jobName,
            'payload' => $event->job->getRawBody(),
            'status' => 'failed',
            'exception' => (string) $event->exception,
            'attempts' => $event->job->attempts(),
            'started_at' => now()->subMilliseconds($duration),
            'completed_at' => now(),
            'duration_ms' => $duration,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        unset($this->jobStartTimes[$jobId]);
    }
}
