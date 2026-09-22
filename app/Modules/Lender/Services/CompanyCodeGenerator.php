<?php

namespace App\Modules\Lender\Services;

use App\Modules\Lender\Repositories\CompanyRepository;
use Carbon\Carbon;

class CompanyCodeGenerator
{
    public function __construct(private readonly CompanyRepository $companies)
    {
    }

    public function companyCode(): string
    {
        do {
            $code = '#TomiLender-'.Carbon::now()->format('dmY').'-'.random_int(1000, 9999);
        } while ($this->companies->idCodeExists($code));

        return $code;
    }

    public function uniqueCode(): string
    {
        do {
            $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4);
        } while ($this->companies->uniqueCodeExists($code));

        return $code;
    }
}
