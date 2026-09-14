<?php

namespace App\Policies;

use App\Models\ExamMark;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExamMarkPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExamMark $examMark): bool
    {
        // if($user->isLecturer()){
        //     return true;
        // }
        // if($user->id != $examMark->student->user_id){
        //     return false;
        // }
        $studentsMark = $examMark->student()
        ->where('user_id', $user->id)
        ->exists();
        if($user->isLecturer() || $studentsMark){
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
        return true;;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExamMark $examMark): bool
    {
        if(!$user->isLecturer()){
            return false;
        }
        return true;;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExamMark $examMark): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExamMark $examMark): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExamMark $examMark): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;;
    }
}
