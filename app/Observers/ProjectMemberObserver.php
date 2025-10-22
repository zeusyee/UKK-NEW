<?php

namespace App\Observers;

use App\Models\ProjectMember;

class ProjectMemberObserver
{
    /**
     * Handle the ProjectMember "created" event.
     */
    public function created(ProjectMember $projectMember): void
    {
        // Update user status to working when added to a project
        \App\Models\User::where('user_id', $projectMember->user_id)
            ->update(['current_task_status' => 'working']);
    }

    /**
     * Handle the ProjectMember "updated" event.
     */
    public function updated(ProjectMember $projectMember): void
    {
        // No need to handle updates
    }

    /**
     * Handle the ProjectMember "deleted" event.
     */
public function deleted(ProjectMember $projectMember): void
    {
        // Check if user has any other project memberships
        $otherMemberships = ProjectMember::where('user_id', $projectMember->user_id)
            ->where('member_id', '!=', $projectMember->member_id)
            ->exists();

        // If no other memberships exist, set status to idle
        if (!$otherMemberships) {
            \App\Models\User::where('user_id', $projectMember->user_id)
                ->update(['current_task_status' => 'idle']);
        }
    }

    /**
     * Handle the ProjectMember "restored" event.
     */
    public function restored(ProjectMember $projectMember): void
    {
        //
    }

    /**
     * Handle the ProjectMember "force deleted" event.
     */
    public function forceDeleted(ProjectMember $projectMember): void
    {
        //
    }
}
