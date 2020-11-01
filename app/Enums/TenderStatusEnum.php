<?php

namespace App\Enums;

class TenderStatusEnum
{
    // public status
    const PUBLIC_STATUS = [
        1 => 'draft',
        2 => 'announced',
        3 => 'finish',
        4 => 'cancel',
        5 => 'changed',
    ];

    // action status
    const ACT_NEW = 1;
    const ACT_CHANGE = 2;
    const ACT_DELETE = 3;
}
