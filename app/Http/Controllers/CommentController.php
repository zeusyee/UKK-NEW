<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Card;
use App\Models\Subtask;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Get all comments for a card
     */
    public function getCardComments($cardId)
    {
        try {
            $card = Card::findOrFail($cardId);
            
            // Verify user is member of the project
            $this->verifyProjectAccess($card->board->project_id);
            
            $comments = Comment::where('card_id', $cardId)
                ->whereNull('subtask_id')
                ->with(['user:user_id,username,full_name,email'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $comments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get all comments for a subtask
     */
    public function getSubtaskComments($subtaskId)
    {
        try {
            $subtask = Subtask::with('card.board')->findOrFail($subtaskId);
            
            // Verify user is member of the project
            $this->verifyProjectAccess($subtask->card->board->project_id);
            
            $comments = Comment::where('subtask_id', $subtaskId)
                ->with(['user:user_id,username,full_name,email'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $comments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Add comment to a card
     */
    public function addCardComment(Request $request, $cardId)
    {
        $request->validate([
            'comment_text' => 'required|string|max:5000',
            'comment_type' => 'in:card,subtask,group_chat'
        ]);

        try {
            $card = Card::with('board')->findOrFail($cardId);
            
            // Verify user is member of the project
            $member = $this->verifyProjectAccess($card->board->project_id);
            
            $comment = Comment::create([
                'card_id' => $cardId,
                'user_id' => Auth::id(),
                'comment_text' => $request->comment_text,
                'comment_type' => $request->comment_type ?? 'card'
            ]);

            $comment->load(['user:user_id,username,full_name,email']);

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => $comment
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Add comment to a subtask
     */
    public function addSubtaskComment(Request $request, $subtaskId)
    {
        $request->validate([
            'comment_text' => 'required|string|max:5000',
            'comment_type' => 'in:card,subtask,group_chat'
        ]);

        try {
            $subtask = Subtask::with('card.board')->findOrFail($subtaskId);
            
            // Verify user is member of the project
            $member = $this->verifyProjectAccess($subtask->card->board->project_id);
            
            $comment = Comment::create([
                'card_id' => $subtask->card_id,
                'subtask_id' => $subtaskId,
                'user_id' => Auth::id(),
                'comment_text' => $request->comment_text,
                'comment_type' => $request->comment_type ?? 'subtask'
            ]);

            $comment->load(['user:user_id,username,full_name,email']);

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'data' => $comment
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update a comment
     */
    public function updateComment(Request $request, $commentId)
    {
        $request->validate([
            'comment_text' => 'required|string|max:5000'
        ]);

        try {
            $comment = Comment::findOrFail($commentId);
            
            // Only comment owner can update
            if ($comment->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only update your own comments'
                ], 403);
            }

            // Verify project access
            if ($comment->card_id) {
                $card = Card::with('board')->findOrFail($comment->card_id);
                $this->verifyProjectAccess($card->board->project_id);
            } elseif ($comment->subtask_id) {
                $subtask = Subtask::with('card.board')->findOrFail($comment->subtask_id);
                $this->verifyProjectAccess($subtask->card->board->project_id);
            }

            $comment->update([
                'comment_text' => $request->comment_text
            ]);

            $comment->load(['user:user_id,username,full_name,email']);

            return response()->json([
                'success' => true,
                'message' => 'Comment updated successfully',
                'data' => $comment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete a comment
     */
    public function deleteComment($commentId)
    {
        try {
            $comment = Comment::findOrFail($commentId);
            
            // Get project info first
            if ($comment->card_id) {
                $card = Card::with('board')->findOrFail($comment->card_id);
                $projectId = $card->board->project_id;
            } elseif ($comment->subtask_id) {
                $subtask = Subtask::with('card.board')->findOrFail($comment->subtask_id);
                $projectId = $subtask->card->board->project_id;
            } else {
                throw new \Exception('Invalid comment reference');
            }

            $member = $this->verifyProjectAccess($projectId);
            
            // Only comment owner or admin/leader can delete
            if ($comment->user_id !== Auth::id() && !in_array($member->role, ['admin', 'leader'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this comment'
                ], 403);
            }

            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get all comments for a project (across all cards and subtasks)
     */
    public function getProjectComments($projectId)
    {
        try {
            // Verify user is member of the project
            $this->verifyProjectAccess($projectId);
            
            $comments = Comment::whereHas('card.board', function($query) use ($projectId) {
                    $query->where('project_id', $projectId);
                })
                ->with([
                    'user:user_id,username,full_name,email',
                    'card:card_id,card_title,board_id',
                    'subtask:subtask_id,subtask_title,card_id'
                ])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $comments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get comment statistics for a project
     */
    public function getCommentStats($projectId)
    {
        try {
            // Verify user is member of the project
            $this->verifyProjectAccess($projectId);
            
            $stats = [
                'total_comments' => Comment::whereHas('card.board', function($query) use ($projectId) {
                    $query->where('project_id', $projectId);
                })->count(),
                
                'card_comments' => Comment::whereHas('card.board', function($query) use ($projectId) {
                    $query->where('project_id', $projectId);
                })->whereNull('subtask_id')->count(),
                
                'subtask_comments' => Comment::whereHas('card.board', function($query) use ($projectId) {
                    $query->where('project_id', $projectId);
                })->whereNotNull('subtask_id')->count(),
                
                'comments_by_type' => Comment::whereHas('card.board', function($query) use ($projectId) {
                    $query->where('project_id', $projectId);
                })
                ->select('comment_type', DB::raw('count(*) as count'))
                ->groupBy('comment_type')
                ->get(),
                
                'top_commenters' => Comment::whereHas('card.board', function($query) use ($projectId) {
                    $query->where('project_id', $projectId);
                })
                ->select('user_id', DB::raw('count(*) as comment_count'))
                ->with('user:user_id,username,full_name')
                ->groupBy('user_id')
                ->orderBy('comment_count', 'desc')
                ->limit(5)
                ->get(),
                
                'recent_activity' => Comment::whereHas('card.board', function($query) use ($projectId) {
                    $query->where('project_id', $projectId);
                })
                ->with([
                    'user:user_id,username,full_name',
                    'card:card_id,card_title',
                    'subtask:subtask_id,subtask_title'
                ])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verify user has access to the project
     */
    private function verifyProjectAccess($projectId)
    {
        $member = ProjectMember::where('project_id', $projectId)
            ->where('user_id', Auth::id())
            ->first();
            
        if (!$member) {
            throw new \Exception('You are not a member of this project');
        }
        
        return $member;
    }
}
