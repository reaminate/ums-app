<?php

namespace App\Policies;

use App\Models\AssignmentMark;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssignmentMarkPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if(!$user->isLecturer()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AssignmentMark $assignmentMark): bool
    {
        if(!$user->isLecturer()){
            return false;
        }
        if($user->id != $assignmentMark->assignmentSubmission->student->id){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if(!$user->isLecturer()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AssignmentMark $assignmentMark): bool
    {
        if(!$user->isLecturer()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AssignmentMark $assignmentMark): bool
    {
        if(!$user->isLecturer()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AssignmentMark $assignmentMark): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AssignmentMark $assignmentMark): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }
}
