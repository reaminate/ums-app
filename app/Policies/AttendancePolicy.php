<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
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
    public function view(User $user, Attendance $attendance): bool
    {
        // if($user->isAdmin()){
        //     return true;
        // }
        // if(!$user->isLecturer() || $user->lecturer->id != $attendance->classSchedule->courseOffering->lecturer_id){
        //     return false;
        // }
        $lecturerCan = $attendance->classSchedule()
        ->whereHas('courseOffering.lecturer', function($query) use($user){
            $query->where('user_id', $user->id);
        })
        ->exists();
        $studentCan = $attendance->classSchedule()
        ->whereHas('courseOffering.students', function($query) use($user){
            $query->where('user_id', $user->id);
        })
        ->exists();
        if($user->isAdmin() || $lecturerCan || $studentCan){
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
    public function update(User $user, Attendance $attendance): bool
    {
        $lecturerCan = $attendance->classSchedule()
        ->whereHas('courseOffering.lecturer', function($query) use($user){
            $query->where('user_id', $user->id);
        })
        ->exists();
        if($user->isAdmin()||$lecturerCan ){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        $lecturerCan = $attendance->classSchedule()
        ->whereHas('courseOffering.lecturer', function($query) use($user){
            $query->where('user_id', $user->id);
        })
        ->exists();
        if($user->isAdmin()||$lecturerCan ){
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attendance $attendance): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attendance $attendance): bool
    {
        if(!$user->isAdmin()){
            return false;
        }
        return true;
    }
}
