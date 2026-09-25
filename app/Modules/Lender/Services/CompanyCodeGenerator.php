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
            $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $numbers = '0123456789';
            $characters = $letters.$numbers;

            $code = $letters[random_int(0, strlen($letters) - 1)]
                .$numbers[random_int(0, strlen($numbers) - 1)];

            for ($i = 0; $i < 2; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }

            $code = str_shuffle($code);
        } while ($this->companies->uniqueCodeExists($code));

        return $code;
    }
}
