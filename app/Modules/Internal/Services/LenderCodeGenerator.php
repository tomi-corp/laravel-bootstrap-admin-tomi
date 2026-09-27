<?php

namespace App\Modules\Internal\Services;

use App\Modules\Internal\Repositories\LenderRepository;

class LenderCodeGenerator
{
    public function __construct(private readonly LenderRepository $lenders)
    {
    }

    public function lenderCode(): string
    {
        do {
            $code = '#TomiKspLender-'.now()->format('dmY').'-'.random_int(1000, 9999);
        } while ($this->lenders->lenderCodeExists($code));

        return $code;
    }
}
