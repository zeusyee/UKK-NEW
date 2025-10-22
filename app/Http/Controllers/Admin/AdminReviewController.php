<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminReviewController extends Controller
{
    public function index()
    {
        // Get all cards that are in review status
        $cardsInReview = Card::with(['board.project', 'creator', 'assignments.user'])
            ->where('status', 'review')
            ->orderBy('updated_at', 'desc')
            ->get();
        
        return view('admin.review.index', compact('cardsInReview'));
    }

    public function show(Card $card)
    {
        if ($card->status !== 'review') {
            return redirect()->route('admin.review.index')
                ->with('error', 'This card is not in review status.');
        }

        $card->load(['board.project', 'creator', 'subtasks', 'assignments.user', 'reviewer']);
        
        return view('admin.review.show', compact('card'));
    }

    public function approve(Request $request, Card $card)
    {
        $request->validate([
            'admin_notes' => 'nullable|string'
        ]);

        if ($card->status !== 'review') {
            return back()->with('error', 'This card is not in review status.');
        }

        DB::beginTransaction();
        try {
            // Update card status to done
            $card->update([
                'status' => 'done',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $request->admin_notes
            ]);

            // Update all assignments for this card to completed
            CardAssignment::where('card_id', $card->card_id)
                ->update(['assignment_status' => 'completed']);

            DB::commit();
            
            return redirect()->route('admin.review.index')
                ->with('success', 'Task approved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve task: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Card $card)
    {
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        if ($card->status !== 'review') {
            return back()->with('error', 'This card is not in review status.');
        }

        DB::beginTransaction();
        try {
            // Update card status back to in_progress
            $card->update([
                'status' => 'in_progress',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'review_notes' => $request->rejection_reason
            ]);

            // Update all assignments for this card back to in_progress
            CardAssignment::where('card_id', $card->card_id)
                ->update([
                    'assignment_status' => 'in_progress',
                    'completed_at' => null
                ]);

            DB::commit();
            
            return redirect()->route('admin.review.index')
                ->with('success', 'Task returned for revision.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject task: ' . $e->getMessage());
        }
    }
}