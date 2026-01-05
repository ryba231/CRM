<?php

namespace App\Security\Enum;

enum WorkspaceRoleType: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MEMBER = 'member';
}