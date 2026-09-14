<?php

namespace App\Policies;

use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LecturerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lecturer $lecturer): bool
    {
        // if($user->isAdmin()){
        //     return true;
        // }
        // if($user->id != $lecturer->user_id){
        //     return false;
        // }
        $lecturerCan  = $lecturer
        ->where('user_id', $user->id)
        ->exists();
        if($user->isAdmin()||$lecturerCan){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lecturer $lecturer): bool
    {
        $lecturerCan  = $lecturer
        ->where('user_id', $user->id)
        ->exists();
        if($user->isAdmin()||$lecturerCan){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lecturer $lecturer): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lecturer $lecturer): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lecturer $lecturer): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }
}
