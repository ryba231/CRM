<?php

namespace App\Security\Enum;

enum PermissionType: string
{
    case CONTACT_VIEW = 'contact.view';
    case CONTACT_CREATE = 'contact.create';
    case CONTACT_EDIT = 'contact.edit';
    case CONTACT_DELETE = 'contact.delete';
    case CONTACT_RESTORE = 'contact.restore';

    case USER_VIEW = 'user.view';
    case USER_INVITE = 'user.invite';
    case USER_EDIT = 'user.edit';
    case USER_DELETE = 'user.delete';

    case WORKSPACE_MANAGE = 'workspace.manage';
    case WORKSPACE_VIEW = 'workspace.view';
    case WORKSPACE_CREATE = 'workspace.create';
    case WORKSPACE_EDIT = 'workspace.edit';
    case WORKSPACE_DELETE = 'workspace.delete';
}
