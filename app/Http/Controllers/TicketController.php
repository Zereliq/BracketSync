<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Notifications\TicketReplyNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Ticket::class);

        $query = Ticket::query()->with(['user', 'assignedTo', 'resolvedBy']);

        if (auth()->user()->isAdmin() || auth()->user()->isMod()) {
            $tickets = $query->latest()->paginate(15);
        } else {
            $tickets = $query->where('user_id', auth()->id())->latest()->paginate(15);
        }

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $this->authorize('create', Ticket::class);

        return view('tickets.create');
    }

    public function store(StoreTicketRequest $request)
    {
        $this->authorize('create', Ticket::class);

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'priority' => $request->priority ?? 'medium',
        ]);

        return redirect()
            ->route('dashboard.tickets.show', $ticket)
            ->with('success', 'Support ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['user', 'assignedTo', 'resolvedBy', 'replies.user']);

        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        return view('tickets.edit', compact('ticket'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $data = $request->only(['status', 'priority', 'assigned_to']);

        if ($request->status === 'resolved' || $request->status === 'closed') {
            if (! $ticket->resolved_at) {
                $data['resolved_at'] = now();
                $data['resolved_by'] = auth()->id();
            }
        }

        $ticket->update($data);

        return redirect()
            ->route('dashboard.tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()
            ->route('dashboard.tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }

    public function storeReply(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $user = auth()->user();

        // Only allow admins/mods to reply, or the ticket owner to reply to their own ticket
        if (! $user->isAdmin() && ! $user->isMod() && $ticket->user_id !== $user->id) {
            abort(403, 'You do not have permission to reply to this ticket.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $validated['message'],
            'is_staff_reply' => $user->isAdmin() || $user->isMod(),
        ]);

        // Send notification to the ticket owner if a staff member replied
        if ($reply->is_staff_reply && $ticket->user_id !== $user->id) {
            $ticket->user->notify(new TicketReplyNotification($reply));
        }

        // Send notification to all staff members who have replied if the ticket owner replied
        if (! $reply->is_staff_reply) {
            $staffReplies = $ticket->replies()
                ->where('is_staff_reply', true)
                ->where('user_id', '!=', $user->id)
                ->get();

            foreach ($staffReplies->unique('user_id') as $staffReply) {
                $staffReply->user->notify(new TicketReplyNotification($reply));
            }
        }

        return redirect()
            ->route('dashboard.tickets.show', $ticket)
            ->with('success', 'Reply added successfully.');
    }
}
