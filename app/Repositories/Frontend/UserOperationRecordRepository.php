<?php
namespace App\Repositories\Frontend;

use App\Models\UserOperationRecord;

class UserOperationRecordRepository extends CommonRepository
{

    public function __construct(UserOperationRecord $userOperationRecord)
    {
        parent::__construct($userOperationRecord);
    }
}
