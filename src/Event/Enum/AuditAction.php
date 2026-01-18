<?php
namespace App\Event\Enum;

enum AuditAction: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case RESTORE = 'restore';
    
    case USER_ADDED = 'user.added';
    case USER_DELETED = 'user.deleted';
    case USER_ROLE_CHANGED = 'user.role_changed';

}