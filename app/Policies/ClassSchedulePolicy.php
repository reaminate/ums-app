<?php

namespace App\Policies;

use App\Enums\UserType;
use App\Models\ClassSchedule;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ClassSchedulePolicy
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
    public function view(User $user, ClassSchedule $classSchedule): bool
    {
        //only allow if your admin, or if its a class you teach as lecturer or if its a class you attend as a student
        if($user->isAdmin()){
            return true;
        }
        if($user->type == UserType::LECTURER->value){
            if($user->id != $classSchedule->courseOffering->lecturer->id){
                return false;
            }
            return true;
        }
        $attends = $classSchedule->attendances()
            ->whereHas('student', fn($query) => $query->where('user_id', $user->id))
            ->exists();
        if(!$attends){
            return false;
        }
        return true;
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
    public function update(User $user, ClassSchedule $classSchedule): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClassSchedule $classSchedule): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ClassSchedule $classSchedule): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ClassSchedule $classSchedule): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }
}
