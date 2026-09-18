<?php

namespace App\Policies;

use App\Enums\AssignmentStatus;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssignmentPolicy
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
    public function view(User $user, Assignment $assignment): bool
    {
        // if(($assignment->status != AssignmentStatus::SHOWN->value)||!($user->isLecturer())){
        //     return false;
        // }
        $forStudent = $assignment
        ->where('status', AssignmentStatus::SHOWN)
        ->exists();
        if($forStudent || $user->isLecturer()){
            return true;
        }
        return false;
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
    public function update(User $user, Assignment $assignment): bool
    {
        $lecturerCan = $assignment->courseOffering()
            ->whereHas('lecturer', function($query) use($user){
                $query->where('user_id', $user->id);
            })
            ->exists();
        if(!$lecturerCan){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Assignment $assignment): bool
    {
        $lecturerCan = $assignment->courseOffering()
            ->whereHas('lecturer', function($query) use($user){
                $query->where('user_id', $user->id);
            })
            ->exists();
        if(!$lecturerCan){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Assignment $assignment): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Assignment $assignment): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }
}
