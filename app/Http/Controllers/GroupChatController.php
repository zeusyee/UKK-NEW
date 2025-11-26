<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupChatController extends Controller
{
    /**
     * Show group chat room page
     */
    public function showRoom($projectId)
    {
        try {
            $project = Project::with('members')->findOrFail($projectId);
            
            // Verify user is member of the project
            $member = $this->verifyProjectAccess($projectId);
            
            // Determine which view to show based on role
            if (in_array($member->role, ['admin', 'leader'])) {
                return view('leader.group-chat-room', compact('project'));
            }
            
            return view('group-chat-room', compact('project'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get all group chat messages for a project
     */
    public function getMessages($projectId)
    {
        try {
            // Verify user is member of the project
            $this->verifyProjectAccess($projectId);
            
            // Get all messages (no role filter)
            $messages = Comment::where('project_id', $projectId)
                ->where('comment_type', 'group_chat')
                ->with(['user:user_id,username,full_name,email'])
                ->orderBy('created_at', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $messages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Send a message to project group chat
     */
    public function sendMessage(Request $request, $projectId)
    {
        $request->validate([
            'comment_text' => 'required|string|max:5000'
        ]);

        try {
            $project = Project::findOrFail($projectId);
            
            // Verify user is member of the project
            $this->verifyProjectAccess($projectId);
            
            $message = Comment::create([
                'project_id' => $projectId,
                'user_id' => Auth::id(),
                'comment_text' => $request->comment_text,
                'comment_type' => 'group_chat'
            ]);

            $message->load(['user:user_id,username,full_name,email']);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete a group chat message
     */
    public function deleteMessage($messageId)
    {
        try {
            $message = Comment::findOrFail($messageId);
            
            // Verify it's a group chat message
            if ($message->comment_type !== 'group_chat') {
                throw new \Exception('Not a group chat message');
            }
            
            // Verify project access
            $member = $this->verifyProjectAccess($message->project_id);
            
            // Only message owner or admin/leader can delete
            if ($message->user_id !== Auth::id() && !in_array($member->role, ['admin', 'leader'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete this message'
                ], 403);
            }

            $message->delete();

            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully'
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

