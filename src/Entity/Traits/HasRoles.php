<?php

namespace App\Entity\Traits;

/**
 * Contains all roles started in the HasRoles.
 */
final class HasRoles
{
    // Role Super Admin
    public const SUPERADMIN = 'ROLE_SUPER_ADMIN';

    // Role Admin Application
    public const ADMINAPPLICATION = 'ROLE_ADMIN_APPLICATION';

    // Role Application
    public const APPLICATION = 'ROLE_APPLICATION';

    // Role Admin
    public const ADMIN = 'ROLE_ADMIN';

    // Role Editor product
    public const EDITOR = 'ROLE_EDITOR';

    // Role Team
    public const TEAM = 'ROLE_TEAM';

    // Role Moderator editor the article
    public const MODERATOR = 'ROLE_MODERATOR';

    // Role User
    public const DEFAULT = 'ROLE_USER';

    // Role isVerified
    public const VERIFIED = 'ROLE_VERIFIED';

    // New Roles 10 Juin 24

    // Role Manager
    public const MANAGER = 'ROLE_MANAGER';

    // Role Sales
    public const SALES = 'ROLE_SALES_PERSON';

    // Role Collabarator
    public const COLLABORATOR = 'ROLE_COLLABORATOR';

    // Role Customer (User)
    public const CUSTOMER = 'ROLE_CUSTOMER';

    // Role Shop
    public const SHOP = 'ROLE_SHOP';

    // Role Client Access
    public const CLIENTACCESS = 'ROLE_CLIENT_ACCESS';

    // Role Client Access Create
    public const CLIENTACCESSCREATE = 'ROLE_CLIENT_ACCESS_CREATE';

    // Role Client Company
    public const CLIENTCOMPANY = 'ROLE_CLIENT_COMPANY';

    // Role Data (Key) Transfer
    public const DATATRANSFER = 'ROLE_DATA_TRANSFER';

    // Role Data (Key) Purchase
    public const DATAPURCHASE = 'ROLE_DATA_PURCHASE';
}
