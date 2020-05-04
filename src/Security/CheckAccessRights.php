<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\UserException;
use Symfony\Component\HttpFoundation\Response;

class CheckAccessRights
{
    public static function hasAdminOrSuperAdminRole(User $user) {
        if ($user->hasRole('ROLE_TEACHER') || $user->hasRole('ROLE_STUDENT')) {
            throw new UserException('You need permissions to perform this action. Contact an admin.', 403);
        }

        return true;
    }

    public static function hasTeacherRole(User $user){
        if (!$user->hasRole('ROLE_TEACHER')) {
            throw new UserException('You need permissions to perform this action. Contact an admin.', 403);
        }

        return true;
    }

    public static function hasStudentRole(User $user){
        if (!$user->hasRole('ROLE_STUDENT')) {
            throw new UserException('You need permissions to perform this action. Contact an admin.', 403);
        }

        return true;
    }

    public static function hasSuperAdminRole(User $user)
    {
        if ($user->hasRole('ROLE_SUPER_ADMIN')) {
            throw new UserException('You need permissions to perform this action. Contact an admin.', 403);
        }

        return true;
    }
}