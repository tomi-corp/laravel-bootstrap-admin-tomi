<?php

namespace App\Modules\Internal\Services;

use App\Models\Admin;
use App\Models\lender;
use App\Modules\Internal\Repositories\LenderRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LenderService
{
    public function __construct(
        private readonly LenderRepository $lenders,
        private readonly LenderCodeGenerator $codeGenerator,
    ) {
    }

    public function create(array $data): lender
    {
        $adminId = $this->adminId();
        $data['logo'] = $this->storeLogo($data['logo'] ?? null);
        $data['id_code_lender'] = $this->codeGenerator->lenderCode();
        $data['file_pks'] = $data['file_pks'] ?? '-';
        $data['last_update_file_pks'] = $data['last_update_file_pks'] ?? now()->toDateString();
        $data['created_by'] = $adminId;
        $data['updated_by'] = null;

        return $this->lenders->create($data);
    }

    public function update(lender $lender, array $data): bool
    {
        $logo = $data['logo'] ?? null;
        unset($data['logo']);

        if ($logo instanceof UploadedFile) {
            $data['logo'] = $this->storeLogo($logo, $lender->logo);
        }

        $data['updated_by'] = $this->adminId();
        $data['file_pks'] = $lender->file_pks ?: '-';
        $data['last_update_file_pks'] = $lender->last_update_file_pks ?: now()->toDateString();

        return $this->lenders->update($lender, $data);
    }

    public function delete(lender $lender): ?bool
    {
        return $this->lenders->delete($lender);
    }

    private function adminId(): int
    {
        return auth('admin')->id() ?? (int) Admin::query()->value('id');
    }

    private function storeLogo(mixed $logo, ?string $oldLogo = null): ?string
    {
        if (! $logo instanceof UploadedFile) {
            return null;
        }

        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }

        return $logo->store('lenders/logos', 'public');
    }
}
