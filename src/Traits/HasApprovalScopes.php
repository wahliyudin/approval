<?php

namespace Tbu\Approval\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Tbu\Approval\Enums\LastAction;

/**
 * @method Builder currentApproval($nik)
 * @method static Builder currentApproval($nik)
 */
trait HasApprovalScopes
{
    public function scopeCurrentApproval($query, $nik)
    {
        return $query->whereHas('workflow', function ($query) use ($nik) {
            $query->where('nik', $nik)
                ->where('last_action', LastAction::NOTTING);
        });
    }
}
