<?php

namespace App\Http\Requests\User\Pinjaman;

use App\Models\ListPerusahaan;
use Illuminate\Foundation\Http\FormRequest;

class GetTenorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nominal' => 'required|numeric',
            'nip' => 'required'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->cekNipAdaAtauTidak() == false) {
                $validator->errors()->add('nip', 'Data pegawai tidak ditemukan');
            }

            $nominal = $this->cekMinimalNominal();
            if (isset($nominal['status'])) {
                $validator->errors()->add('nominal', 'Minimal nominal pinjaman Rp  ' . number_format($nominal['minimal']));
            }

            $maximal = $this->cekMaximalNominal();
            if (isset($maximal['status'])) {
                $validator->errors()->add('nominal', 'Maksimal nominal pinjaman Rp  ' . number_format($maximal['maximal']));
            }
        });
    }

    private function cekMinimalNominal()
    {
        $nip = explode("-", $this->nip);
        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                $q->where('nip', '=', $nip[1]);
                $q->first();
            }, 'amount_pinjaman'])->first();

        if ($this->nominal < $perusahaan->amount_pinjaman->minimal) {
            return [
                'status' => true,
                'minimal' => $perusahaan->amount_pinjaman->minimal
            ];;
        } else {
            return false;
        }
    }

    private function cekMaximalNominal()
    {
        $nip = explode("-", $this->nip);
        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                $q->where('nip', '=', $nip[1]);
                $q->first();
            }, 'amount_pinjaman'])->first();

        $pendapatan = 70 / 100 * $perusahaan->karyawanSingle->penghasilan;

        if ($this->nominal > $perusahaan->amount_pinjaman->maximal) {
            return [
                'status' => true,
                'maximal' => $perusahaan->amount_pinjaman->maximal
            ];
        } elseif ($this->nominal > $pendapatan) {
            return [
                'status' => true,
                'maximal' => $pendapatan
            ];
        } else {
            return false;
        }
    }

    private function cekNipAdaAtauTidak()
    {
        $nip = explode("-", $this->nip);
        $perusahaan = ListPerusahaan::where('unique_code', $nip[0])
            ->with(['karyawanSingle' => function ($q) use ($nip) {
                $q->where('nip', '=', $nip[1]);
                $q->first();
            }])->first();
        if (isset($perusahaan)) {
            return true;
        } else {
            return false;
        }
    }
}
