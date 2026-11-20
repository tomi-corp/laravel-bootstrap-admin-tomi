<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Referral sebuah perusahaan. Satu perusahaan maksimal punya dua referral: Referal 1 dan Referal 2.
 */
class CompanyReferral extends Model
{
    use SoftDeletes;

    public const POSITIONS = [1 => 'Referal 1', 2 => 'Referal 2'];

    protected $table = 'company_referrals';

    protected $fillable = ['company_id', 'referral_id', 'position'];

    public function company()
    {
        return $this->belongsTo(ListPerusahaan::class, 'company_id');
    }

    public function referral()
    {
        return $this->belongsTo(Referral::class);
    }

    /**
     * Ganti referral perusahaan: [posisi => referral_id|null]. Baris lama dihapus permanen
     * agar tidak bentrok dengan unique index (company_id, position) dan (company_id, referral_id).
     */
    public static function syncForCompany(int $companyId, array $referralsByPosition): void
    {
        foreach ($referralsByPosition as $position => $referralId) {
            static::withTrashed()
                ->where('company_id', $companyId)
                ->where(fn ($q) => $q->where('position', $position)
                    ->when($referralId, fn ($w) => $w->orWhere('referral_id', $referralId)))
                ->forceDelete();
        }

        foreach ($referralsByPosition as $position => $referralId) {
            if ($referralId) {
                static::create(['company_id' => $companyId, 'referral_id' => $referralId, 'position' => $position]);
            }
        }
    }
}
