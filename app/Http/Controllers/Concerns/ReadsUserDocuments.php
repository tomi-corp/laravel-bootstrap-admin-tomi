<?php

namespace App\Http\Controllers\Concerns;

use App\Models\pinjaman;
use App\Models\PrivyDigitalIdSession;
use Illuminate\Support\Str;

/**
 * Resolusi path dokumen pengguna (PK, KTP, selfie) di disk "local".
 * Semua path divalidasi agar tidak bisa keluar dari folder penyimpanan.
 */
trait ReadsUserDocuments
{
    /** Path dokumen PK: assets/pk/{nama file}. */
    protected function pkDocumentPath(?string $fileName): ?string
    {
        return $this->hasInvalidFileName($fileName) ? null : 'assets/pk/'.$fileName;
    }

    /** Path foto pengguna lama: assets/users/{nama file}. */
    protected function userImagePath(?string $fileName): ?string
    {
        return $this->hasInvalidFileName($fileName) ? null : 'assets/users/'.$fileName;
    }

    /** Path foto KTP/selfie dari sesi Privy (local_ktp_path / local_selfie_path). */
    protected function privySessionImagePath(?PrivyDigitalIdSession $session, string $imageType): ?string
    {
        $path = $session?->{$imageType === 'ktp' ? 'local_ktp_path' : 'local_selfie_path'};

        return $this->hasInvalidStoragePath($path) ? null : $path;
    }

    /**
     * Foto KTP/selfie milik sebuah pinjaman.
     * Pinjaman dengan KYC memakai foto KYC; tanpa KYC memakai sesi Privy terbaru yang punya foto tersebut.
     */
    protected function loanImagePath(pinjaman $pinjaman, string $imageType): ?string
    {
        if (! is_null($pinjaman->id_kyc)) {
            return $this->userImagePath($pinjaman->kyc?->{$imageType});
        }

        $session = PrivyDigitalIdSession::where('user_id', $pinjaman->user_id)
            ->where(fn ($q) => $q->whereNotNull($imageType)->orWhereNotNull($imageType === 'ktp' ? 'local_ktp_path' : 'local_selfie_path'))
            ->latest('id')
            ->first();

        return $this->privySessionImagePath($session, $imageType)
            ?? $this->userImagePath($session?->{$imageType});
    }

    protected function hasInvalidStoragePath(?string $path): bool
    {
        return blank($path) || Str::contains($path, ['..', '\\']) || Str::startsWith($path, '/');
    }

    protected function hasInvalidFileName(?string $fileName): bool
    {
        return blank($fileName) || Str::contains($fileName, ['..', '/', '\\']);
    }
}
