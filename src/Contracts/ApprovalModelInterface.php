<?php

namespace Tbu\Approval\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface ApprovalModelInterface
{
    public function workflows(): HasMany;
}
