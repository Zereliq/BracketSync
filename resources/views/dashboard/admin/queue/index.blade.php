@extends('layouts.dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Queue Management</h1>
            <p class="text-slate-400 mt-2">Monitor and manage background jobs</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center space-x-3">
                <div class="p-3 bg-blue-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-400">Pending Jobs</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['total_jobs']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center space-x-3">
                <div class="p-3 bg-red-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-400">Failed Jobs</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['failed_jobs']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center space-x-3">
                <div class="p-3 bg-green-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-400">Completed Jobs</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['completed_jobs']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center space-x-3">
                <div class="p-3 {{ $stats['queue_paused'] ? 'bg-yellow-500/20' : 'bg-green-500/20' }} rounded-lg">
                    <svg class="w-6 h-6 {{ $stats['queue_paused'] ? 'text-yellow-400' : 'text-green-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($stats['queue_paused'])
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        @endif
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-400">Queue Status</p>
                    <p class="text-2xl font-bold text-white">{{ $stats['queue_paused'] ? 'Paused' : 'Running' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <div class="flex items-center space-x-3">
                <div class="p-3 bg-purple-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-400">Connection</p>
                    <p class="text-2xl font-bold text-white">{{ ucfirst($stats['connection']) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        <h2 class="text-xl font-bold text-white mb-4">Queue Actions</h2>
        <div class="flex flex-wrap gap-3">
            @if($stats['queue_paused'])
                <form action="{{ route('dashboard.admin.queue.resume') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors inline-flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Resume Queue</span>
                    </button>
                </form>
            @else
                <form action="{{ route('dashboard.admin.queue.pause') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg transition-colors inline-flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Pause Queue</span>
                    </button>
                </form>
            @endif

            <form action="{{ route('dashboard.admin.queue.work') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors inline-flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Process One Job</span>
                </button>
            </form>

            <form action="{{ route('dashboard.admin.queue.restart') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg transition-colors inline-flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Restart Workers</span>
                </button>
            </form>

            @if($stats['failed_jobs'] > 0)
                <form action="{{ route('dashboard.admin.queue.retry-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors inline-flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Retry All Failed</span>
                    </button>
                </form>

                <form action="{{ route('dashboard.admin.queue.flush') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all failed jobs?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors inline-flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Flush Failed Jobs</span>
                    </button>
                </form>
            @endif

            @if($stats['total_jobs'] > 0)
                <form action="{{ route('dashboard.admin.queue.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all pending jobs?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-colors inline-flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Clear Pending Jobs</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Pending Jobs Table --}}
    @if($stats['connection'] === 'database' && $jobs->count() > 0)
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="p-6 border-b border-slate-800">
                <h2 class="text-xl font-bold text-white">Pending Jobs</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Queue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Payload</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Attempts</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Available At</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($jobs as $job)
                            @php
                                $payload = json_decode($job->payload, true);
                                $jobName = $payload['displayName'] ?? 'Unknown Job';
                            @endphp
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-mono">{{ $job->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ $job->queue }}</td>
                                <td class="px-6 py-4 text-sm text-slate-300">
                                    <div class="max-w-md truncate">{{ $jobName }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ $job->attempts }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                    {{ \Carbon\Carbon::createFromTimestamp($job->available_at)->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <form action="{{ route('dashboard.admin.queue.delete', $job->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this job?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($jobs->hasPages())
                <div class="p-6 border-t border-slate-800">
                    {{ $jobs->links() }}
                </div>
            @endif
        </div>
    @elseif($stats['connection'] === 'database')
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-xl font-bold text-slate-400 mb-2">No Pending Jobs</h3>
            <p class="text-slate-500">The queue is empty.</p>
        </div>
    @endif

    {{-- Failed Jobs Table --}}
    @if($stats['connection'] === 'database' && $failedJobs->count() > 0)
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="p-6 border-b border-slate-800">
                <h2 class="text-xl font-bold text-white">Failed Jobs</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Queue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Job</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Exception</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Failed At</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($failedJobs as $failedJob)
                            @php
                                $payload = json_decode($failedJob->payload, true);
                                $jobName = $payload['displayName'] ?? 'Unknown Job';
                                $exception = $failedJob->exception;
                                $exceptionPreview = substr($exception, 0, 100);
                            @endphp
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-mono">{{ $failedJob->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ $failedJob->queue }}</td>
                                <td class="px-6 py-4 text-sm text-slate-300">
                                    <div class="max-w-md truncate">{{ $jobName }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-red-400">
                                    <div class="max-w-md truncate" title="{{ $exception }}">{{ $exceptionPreview }}...</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                    {{ \Carbon\Carbon::parse($failedJob->failed_at)->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-3">
                                    <form action="{{ route('dashboard.admin.queue.retry', $failedJob->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-400 hover:text-green-300 transition-colors">
                                            Retry
                                        </button>
                                    </form>
                                    <form action="{{ route('dashboard.admin.queue.delete-failed', $failedJob->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this failed job?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($failedJobs->hasPages())
                <div class="p-6 border-t border-slate-800">
                    {{ $failedJobs->links() }}
                </div>
            @endif
        </div>
    @endif

    @if($stats['connection'] !== 'database')
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-6">
            <div class="flex items-start space-x-3">
                <svg class="w-6 h-6 text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <h3 class="text-yellow-400 font-semibold mb-2">Queue Connection Not Supported</h3>
                    <p class="text-yellow-300 text-sm">
                        Detailed job listing is only available when using the "database" queue connection.
                        Your current connection is <strong>{{ $stats['connection'] }}</strong>.
                        You can still use the action buttons above to manage the queue.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Job History Table --}}
    @if($jobHistory->count() > 0)
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="p-6 border-b border-slate-800">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Job History</h2>
                    <span class="text-sm text-slate-400">{{ number_format($stats['total_history']) }} total jobs processed</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Job Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Queue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Attempts</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Completed At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Exception</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($jobHistory as $historyItem)
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-300">
                                    <div class="max-w-md truncate">{{ $historyItem->job_name ?? 'Unknown Job' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ $historyItem->queue }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($historyItem->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Completed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                    @if($historyItem->duration_ms !== null)
                                        @if($historyItem->duration_ms < 1000)
                                            {{ $historyItem->duration_ms }}ms
                                        @else
                                            {{ number_format($historyItem->duration_ms / 1000, 2) }}s
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">{{ $historyItem->attempts }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                    {{ \Carbon\Carbon::parse($historyItem->completed_at)->format('M d, Y H:i:s') }}
                                    <span class="text-slate-500 text-xs block">{{ \Carbon\Carbon::parse($historyItem->completed_at)->diffForHumans() }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($historyItem->exception)
                                        <div class="max-w-xs truncate text-red-400 cursor-help" title="{{ $historyItem->exception }}">
                                            {{ substr($historyItem->exception, 0, 50) }}...
                                        </div>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($jobHistory->hasPages())
                <div class="p-6 border-t border-slate-800">
                    {{ $jobHistory->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="text-xl font-bold text-slate-400 mb-2">No Job History</h3>
            <p class="text-slate-500">Jobs that have been processed will appear here.</p>
        </div>
    @endif

    {{-- Command History Table --}}
    @if($commandHistory->count() > 0)
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
            <div class="p-6 border-b border-slate-800">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Scheduled Commands History</h2>
                    <span class="text-sm text-slate-400">{{ number_format($stats['total_commands']) }} total commands executed</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Command</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Exit Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Executed At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @foreach($commandHistory as $commandItem)
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-slate-300 font-mono">{{ $commandItem->command ?? 'Unknown Command' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($commandItem->exit_code === 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Success
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            Failed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                    @if($commandItem->duration_ms !== null)
                                        @if($commandItem->duration_ms < 1000)
                                            {{ $commandItem->duration_ms }}ms
                                        @else
                                            {{ number_format($commandItem->duration_ms / 1000, 2) }}s
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="font-mono {{ $commandItem->exit_code === 0 ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $commandItem->exit_code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-300">
                                    {{ \Carbon\Carbon::parse($commandItem->completed_at)->format('M d, Y H:i:s') }}
                                    <span class="text-slate-500 text-xs block">{{ \Carbon\Carbon::parse($commandItem->completed_at)->diffForHumans() }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($commandHistory->hasPages())
                <div class="p-6 border-t border-slate-800">
                    {{ $commandHistory->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-slate-700 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-xl font-bold text-slate-400 mb-2">No Command History</h3>
            <p class="text-slate-500">Scheduled commands that have been executed will appear here.</p>
        </div>
    @endif
</div>
@endsection
