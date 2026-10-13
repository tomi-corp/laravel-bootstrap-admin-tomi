<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ksp\Lender\AddLenderRequest;
use App\Http\Requests\Admin\Lender\UpdateInvoiceRequest;
use App\Http\Requests\Admin\Lender\UpdateLenderRequest;
use App\Http\Resources\Admin\Lender\DetailLenderResource;
use App\Http\Resources\Admin\Lender\Invocing\InvoicingUpdateDetailResource;
use App\Http\Resources\Admin\Lender\LenderDetailResource;
use App\Http\Resources\Admin\Lender\LenderHistoryUpdateResource;
use App\Models\Admin;
use App\Models\bank;
use App\Models\configurasi_lender;
use App\Models\disbursement;
use App\Models\history_invoicing_lender;
use App\Models\history_invoicing_lender_update;
use App\Models\kecamatan;
use App\Models\kelurahan;
use App\Models\kota;
use App\Models\lender;
use App\Models\lender_update;
use App\Models\pinjaman;
use App\Models\Project;
use App\Models\provinsi;
use App\Models\rekening_pandu;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class LenderController extends Controller
{
    public function indexLender()
    {
        $bank = bank::get();
        $provinsis = provinsi::get();
        $data = [
            'bank' => $bank,
            'provinsis' => $provinsis,
        ];

        return view('admin.ksp.lender.index', compact('data'));
    }

    public function addLender(AddLenderRequest $req)
    {
        $lender = new lender;
        while (true) {
            $string = '#TomiKspLender'.'-'.Carbon::now()->format('dmY').'-'.rand(1000, 9999);
            $lenderlist = lender::where('id_code_lender', $string)->first();
            if (! isset($lenderlist)) {
                break;
            }
        }

        $lender->id_code_lender = $string;
        $lender->pic_lender = $req->pic_lender;
        $lender->pic_internal = $req->pic_internal;
        $lender->nama = $req->nama_lender;
        $lender->email_lender = $req->email_lender;
        $lender->alamat_lengkap = $req->alamat_lengkap;
        $lender->telp = $req->telepon;
        $lender->nomor_pks = $req->nomor_pks;
        $lender->color = $req->colorLender;
        // file pks
        $file = $req->file('file_pks');
        $ext = '.pdf';
        $filename = "LENDER_PKS_{$string}_{$lender->nama}_".time().".{$ext}";
        $path = $file->storeAs('pks_lender', $filename);

        $imageName = time().'_'.$req->nama_lender.'.'.$req->logoLender->extension();
        $req->logoLender->move(public_path('assets/images'), $imageName);
        $lender->logo = $imageName;

        $provinsi = provinsi::where('provinsi', $req->provinsi)->first();
        $kabupaten = kota::where('kabupaten', $req->kabupaten)->first();
        $kecamatan = kecamatan::where('kecamatan', $req->kecamatan)->first();
        $desaaa = explode('-', $req->desa);
        $desa = kelurahan::where('desa', $desaaa[0])
            ->where('kodepos', $desaaa[1])
            ->first();

        $lender->id_provinsi = $provinsi->id;
        $lender->id_kabupaten = $kabupaten->id;
        $lender->id_kecamatan = $kecamatan->id;
        $lender->id_desa = $desa->id;
        $lender->file_pks = $filename;
        $lender->last_update_file_pks = Carbon::now()->format('Y-m-d');
        // bank
        $lender->id_bank = $req->bank;
        $lender->status = 'nonactive';
        $lender->nama_rekening = $req->an_rekening;
        $lender->no_rekening = $req->no_rekening;
        $lender->created_by = auth('admin')->user()->id;

        $lender->save();
        $this->syncLenderPicAdmins($lender, $req);

        return response()->json([
            'status' => 200,
            'message' => 'Lender Berhasil Ditambahkan',
        ], 200);
    }

    public function lenderTable()
    {
        $lender = lender::with(['provinsi', 'kabupaten', 'kecamatan', 'desa', 'bank'])->get();
        $configurasi = configurasi_lender::first();

        return DataTables::of($lender)
            ->addIndexColumn()

            ->addColumn('bank', function ($row) {
                return $row->bank?->code_bank ?? '-';
            })

            ->addColumn('last_updated', function ($row) {
                return Carbon::parse($row->updated_at)->format('Y-m-d H:i:s');
            })

            ->addColumn('pks', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="filePks('.$row->id.')"><i class="mdi mdi-file-document-outline"></i> </button>';
            })

            ->addColumn('logo', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="imageLogo('.$row->id.')"><i class="mdi mdi-image"></i> </button>';
            })

            ->addColumn('expired_updated', function ($row) use ($configurasi) {
                $expiredDays = (int) ($configurasi?->expired_days ?? 0);

                return Carbon::parse($row->updated_at)->addDays($expiredDays)->format('Y-m-d H:i:s');
            })

            ->addColumn('provinsi', function ($row) {
                return $row->provinsi?->provinsi ?? '-';
            })

            ->addColumn('kabupaten', function ($row) {
                return $row->kabupaten?->kabupaten ?? '-';
            })

            ->addColumn('kecamatan', function ($row) {
                return $row->kecamatan?->kecamatan ?? '-';
            })

            ->addColumn('desa', function ($row) {
                if (! $row->desa) {
                    return '-';
                }

                return $row->desa->desa.' - '.$row->desa->kodepos;
            })

            ->addColumn('alamat_lengkap', function ($row) {
                return $row->alamat_lengkap;
            })

            ->addColumn('status', function ($row) {
                if ($row->status == 'active') {
                    $status = '<span class="badge badge-success">ACTIVE</span>';
                } elseif ($row->status == 'nonactive') {
                    $status = '<span class="badge badge-warning">INACTIVE</span>';
                }

                return $status;
            })
            ->addColumn('action', function ($row) {
                $action = '<button class="btn btn-sm btn-primary mr-2" onclick="editLender('.$row->id.')"><i class="mdi mdi-pencil"></i> </button>
                    <button class="btn btn-sm btn-danger mr-2" onclick="deleteLender('.$row->id.')"><i class=" mdi mdi-trash-can"></i> </button>
                    ';

                return $action;
            })

            ->rawColumns(['action', 'status', 'pks', 'logo'])
            ->only([
                'DT_RowIndex',
                'id_code_lender',
                'pks',
                'pic_lender',
                'pic_internal',
                'nama',
                'email_lender',
                'telp',
                'alamat_lengkap',
                'nomor_pks',
                'last_updated',
                'expired_updated',
                'provinsi',
                'kabupaten',
                'kecamatan',
                'desa',
                'status',
                'action',
                'bank',
                'nama_rekening',
                'no_rekening',
                'logo',
            ])
            ->toJson();
    }

    public function pksShow(Request $req)
    {
        $lender = lender::where('id', $req->id)->first();
        $disk = Storage::disk('local');
        $file = $disk->get('pks_lender/'.$lender->file_pks);

        return Response::make($file, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="PKS Lender"',
        ]);
    }

    public function imageShow(Request $req)
    {
        $lender = lender::where('id', $req->id)->first();
        $imageUrl = asset('assets/images/'.$lender->logo);

        return response()->json([
            'url' => $imageUrl,
            'success' => true,
        ]);
    }

    public function lenderDetail(Request $req)
    {
        $lender = lender::where('id', $req->id)->with([
            'provinsi',
            'kabupaten',
            'kecamatan',
            'desa',
        ])->first();

        return response()->json([
            'status' => 200,
            'data' => new DetailLenderResource($lender),
        ], 200);
    }

    public function updateLender(UpdateLenderRequest $req)
    {
        $lender = lender::where('id', $req->id)->first();

        // Lakukan perubahan pada model (sebelum disimpan ke database)
        $lender->pic_lender = $req->pic_lender;
        $lender->pic_internal = $req->pic_internal;
        $lender->nama = $req->nama_lender;
        $lender->email_lender = $req->email_lender;
        $lender->alamat_lengkap = $req->alamat_lengkap;
        $lender->telp = $req->telepon;
        $lender->nomor_pks = $req->nomor_pks;
        // file pks
        if ($req->file('file_pks') !== null) {
            $file = $req->file('file_pks');
            $ext = '.pdf';
            $filename = "LENDER_PKS_{$lender->id_code_lender}_{$lender->nama}_".time().".{$ext}";
            $path = $file->storeAs('pks_lender', $filename);
            $lender->file_pks = $filename;
            $lender->last_update_file_pks = Carbon::now()->format('Y-m-d');
        }

        if ($req->file('logoLender') !== null) {
            $imageName = time().'_'.$req->nama_lender.'.'.$req->logoLender->extension();
            $req->logoLender->move(public_path('assets/images'), $imageName);
            $lender->logo = $imageName;
        }

        $provinsi = provinsi::where('provinsi', $req->provinsi)->first();
        $kabupaten = kota::where('kabupaten', $req->kabupaten)->first();
        $kecamatan = kecamatan::where('kecamatan', $req->kecamatan)->first();
        $desaaa = explode('-', $req->desa);
        $desa = kelurahan::where('desa', $desaaa[0])
            ->where('kodepos', $desaaa[1])
            ->first();

        $lender->id_provinsi = $provinsi->id;
        $lender->id_kabupaten = $kabupaten->id;
        $lender->id_kecamatan = $kecamatan->id;
        $lender->id_desa = $desa->id;
        // bank
        $lender->id_bank = $req->bank;
        $lender->nama_rekening = $req->an_rekening;
        $lender->no_rekening = $req->no_rekening;
        $lender->status = $req->status;
        $lender->color = $req->colorLender;
        $lender->update_by = auth('admin')->user()->id;
        $lender->save();
        $this->syncLenderPicAdmins($lender, $req);

        return response()->json([
            'status' => 200,
            'message' => 'Lender berhasil diperbarui',
        ], 200);
    }

    public function deleteLender(Request $req)
    {
        $lender = lender::where('id', $req->id)->firstOrFail();
        $lender->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Lender berhasil dihapus',
        ], 200);
    }

    private function syncLenderPicAdmins(lender $lender, Request $request): void
    {
        foreach ([
            'pic_lender' => 'PIC Lender',
            'pic_internal' => 'PIC HC / HRD',
        ] as $prefix => $label) {
            if (! $request->filled($prefix)) {
                continue;
            }

            Admin::withoutEvents(function () use ($lender, $request, $prefix, $label) {
                $admin = Admin::query()
                    ->where('lender_id', $lender->id)
                    ->where('username', $this->lenderPicUsername($prefix, $lender->id))
                    ->first();

                if (! $admin && $request->filled("{$prefix}_email")) {
                    $admin = Admin::query()
                        ->where('lender_id', $lender->id)
                        ->where('email', $request->input("{$prefix}_email"))
                        ->first();
                }

                $admin ??= new Admin();

                foreach ($this->filterAdminColumns($this->lenderPicAdminPayload($lender, $request, $prefix, $label, $admin->exists)) as $field => $value) {
                    $admin->{$field} = $value;
                }

                $admin->save();
            });
        }
    }

    private function lenderPicAdminPayload(lender $lender, Request $request, string $prefix, string $label, bool $exists): array
    {
        [$firstName, $lastName] = $this->splitAdminName($request->input($prefix, ''));

        $payload = [
            'project_id' => $this->lenderProjectId(),
            'lender_id' => $lender->id,
            'nik' => $request->input("{$prefix}_national_id_number"),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'birth_place' => $request->input("{$prefix}_birth_place"),
            'birth_date' => $request->input("{$prefix}_birth_date"),
            'gender' => $request->input("{$prefix}_gender"),
            'blood_type' => $request->input("{$prefix}_blood_type"),
            'address' => $request->input("{$prefix}_address"),
            'rt' => $request->input("{$prefix}_rt"),
            'rw' => $request->input("{$prefix}_rw"),
            'wilayah_provinsi_net_id' => $request->input("{$prefix}_province_id"),
            'wilayah_kabupaten_net_id' => $request->input("{$prefix}_city_id"),
            'wilayah_kecamatan_net_id' => $request->input("{$prefix}_district_id"),
            'wilayah_desa_net_id' => $request->input("{$prefix}_village_id"),
            'postal_code' => $request->input("{$prefix}_postal_code"),
            'religion' => $this->adminReligion($request->input("{$prefix}_religion")),
            'marital_status' => $request->input("{$prefix}_marital_status"),
            'occupation' => $request->input("{$prefix}_occupation") ?: $label,
            'nationality' => $request->input("{$prefix}_nationality") ?: 'WNI',
            'phone' => $request->input("{$prefix}_phone_number"),
            'username' => $this->lenderPicUsername($prefix, $lender->id),
            'email' => $request->input("{$prefix}_email") ?: $this->lenderPicFallbackEmail($prefix, $lender),
        ];

        if (Schema::hasColumn('admins', 'type')) {
            $payload['type'] = 'pic';
        }

        if (Schema::hasColumn('admins', 'role')) {
            $payload['role'] = 'pic';
        }

        if (Schema::hasColumn('admins', 'role_id')) {
            $payload['role_id'] = $this->picRoleId($payload['project_id']);
        }

        if (! $exists || $request->filled("{$prefix}_password")) {
            $payload['password'] = Hash::make(
                $request->input("{$prefix}_password") ?: Str::random(16)
            );
        }

        return $payload;
    }

    private function splitAdminName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2);

        return [$parts[0] ?? null, $parts[1] ?? null];
    }

    private function lenderPicUsername(string $prefix, int $lenderId): string
    {
        return $prefix . '_lender_' . $lenderId;
    }

    private function lenderPicFallbackEmail(string $prefix, lender $lender): string
    {
        return $prefix . '_lender_' . $lender->id . '@tomi.local';
    }

    private function lenderProjectId(): ?int
    {
        return Project::query()
            ->where('code', 'lender')
            ->orWhere('name', 'Lender')
            ->value('id');
    }

    private function picRoleId(?int $projectId): ?int
    {
        if (! $projectId || ! class_exists(\App\Models\Role::class)) {
            return null;
        }

        return \App\Models\Role::query()
            ->where('project_id', $projectId)
            ->whereRaw('LOWER(name) = ?', ['pic'])
            ->value('id');
    }

    private function adminReligion(?string $religion): ?string
    {
        return match ($religion) {
            'christian' => 'kristen',
            'catholic' => 'katolik',
            'buddhist' => 'buddha',
            'confucian' => 'konghucu',
            default => $religion,
        };
    }

    private function filterAdminColumns(array $payload): array
    {
        $columns = Schema::getColumnListing('admins');

        return array_intersect_key($payload, array_flip($columns));
    }

    public function approvalTableLender()
    {
        $lenderUpdate = lender_update::where(function ($query) {
            $query->where('first_approver_id', '!=', auth('admin')->user()->id)
                ->orWhereNull('first_approver_id');
        })
            ->where(function ($query) {
                $query->where('second_approver_id', '!=', auth('admin')->user()->id)
                    ->orWhereNull('second_approver_id');
            })
            ->where(function ($query) {
                $query->where('second_approver_id', '!=', auth('admin')->user()->id)
                    ->orWhereNull('second_approver_id');
            })
            ->with(['lender', 'requester', 'first_approver', 'second_approver'])
            ->get();

        return DataTables::of($lenderUpdate)
            ->addIndexColumn()

            ->addColumn('waktu', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
            })

            ->addColumn('requester', function ($row) {
                return $row->requester?->username ?? '-';
            })

            ->addColumn('status_pertama', function ($row) {
                if ($row->status_pertama == 'pending') {
                    $badge = '<span class="badge badge-warning">PENDING</span>';
                } elseif ($row->status_pertama == 'reject') {
                    $badge = '<span class="badge badge-danger">REJECT</span>';
                } else {
                    $badge = '<span class="badge badge-success">APPROVE</span>';
                }

                return $badge;
            })

            ->addColumn('status_kedua', function ($row) {
                if ($row->status_kedua == 'pending') {
                    $badge = '<span class="badge badge-warning">PENDING</span>';
                } elseif ($row->status_kedua == 'reject') {
                    $badge = '<span class="badge badge-danger">REJECT</span>';
                } else {
                    $badge = '<span class="badge badge-success">APPROVE</span>';
                }

                return $badge;
            })

            ->addColumn('first_approver_id', function ($row) {
                return $row->first_approver == null ? '-' : $row->first_approver->username;
            })

            ->addColumn('second_approver_id', function ($row) {
                return $row->second_approver == null ? '-' : $row->second_approver->username;
            })

            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="editApproval('.$row->id.')"><i class="mdi mdi-pencil"></i> </button>';
            })

            ->rawColumns(['action', 'status_kedua', 'status_pertama'])
            ->only([
                'DT_RowIndex',
                'waktu',
                'requester',
                'status_pertama',
                'status_kedua',
                'first_approver_id',
                'second_approver_id',
                'action',
            ])
            ->toJson();
    }

    public function approvalDetail(Request $req)
    {
        $lenderUpdate = lender_update::where(function ($query) {
            $query->where('first_approver_id', '!=', auth('admin')->user()->id)
                ->orWhereNull('first_approver_id');
        })
            ->where(function ($query) {
                $query->where('second_approver_id', '!=', auth('admin')->user()->id)
                    ->orWhereNull('second_approver_id');
            })
            ->where(function ($query) {
                $query->where('second_approver_id', '!=', auth('admin')->user()->id)
                    ->orWhereNull('second_approver_id');
            })
            ->where(function ($query) use ($req) {
                $query->where('id', $req->id);
            })
            ->with(['lender', 'requester', 'first_approver', 'second_approver'])
            ->first();

        return response()->json([
            'status' => 200,
            'data' => new LenderDetailResource($lenderUpdate),
        ], 200);
    }

    public function approvalAction(Request $req)
    {
        $lender_updates = lender_update::where('id', $req->id)->first();
        // cek status pertama
        if ($lender_updates->status_pertama == 'pending' || $lender_updates->first_approver_id == null) {
            $lender_updates->status_pertama = $req->action;
            $lender_updates->first_approver_id = auth('admin')->user()->id;
        } elseif ($lender_updates->status_kedua == 'pending' || $lender_updates->second_approver_id == null) {
            $lender_updates->status_kedua = $req->action;
            $lender_updates->second_approver_id = auth('admin')->user()->id;
        }
        $lender_updates->save();

        if ($lender_updates->status_pertama == 'approve' && $lender_updates->status_kedua == 'approve') {
            $lender = lender::where('id', $lender_updates->lender_id)->first();
            if ($lender) {
                // $newData = json_decode($lender_updates->new_data, true);
                $lender->update($lender_updates->new_data['new_data']);
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengupdate data approval',
        ], 200);
    }

    public function updateHistoryLender(Request $req)
    {
        $perubahan = lender_update::where('lender_id', $req->id)
            ->where('status_pertama', 'approve')
            ->where('status_kedua', 'approve')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 200,
            'data' => LenderHistoryUpdateResource::collection($perubahan),
        ], 200);
    }

    public function indexInvoicingLender()
    {
        $bulanIni = Carbon::now()->isoFormat('MMMM Y');

        return view('admin.ksp.lender.invoicing.index', compact('bulanIni'));
    }

    public function invoicingSementaraTable()
    {
        $lenderTable = (new lender)->getTable();
        $pinjamanTable = (new pinjaman)->getTable();
        $disbursementTable = (new disbursement)->getTable();

        $lender = lender::query()
            ->join($pinjamanTable, "{$pinjamanTable}.id_lender", '=', "{$lenderTable}.id")
            ->join($disbursementTable, function ($join) use ($pinjamanTable, $disbursementTable) {
                $join->on("{$disbursementTable}.pinjaman_id", '=', "{$pinjamanTable}.id")
                    ->whereYear("{$disbursementTable}.created_at", Carbon::now()->year)
                    ->whereMonth("{$disbursementTable}.created_at", Carbon::now()->month);
            })
            ->select([
                "{$lenderTable}.id",
                "{$lenderTable}.id_code_lender",
                "{$lenderTable}.nama",
            ])
            ->selectRaw("COUNT(DISTINCT {$pinjamanTable}.id) as total_pinjaman")
            ->selectRaw("COALESCE(SUM({$pinjamanTable}.biaya_admin_pandu), 0) + COALESCE(SUM({$pinjamanTable}.biaya_layanan), 0) as revenue")
            ->groupBy("{$lenderTable}.id", "{$lenderTable}.id_code_lender", "{$lenderTable}.nama");

        return DataTables::of($lender)
            ->addIndexColumn()

            ->addColumn('total_pinjaman', function ($row) {
                return '<span class="badge badge-success">'.$row->total_pinjaman.'</span>';
            })

            ->addColumn('revenue', function ($row) {
                return 'Rp  '.number_format($row->revenue);
            })

            ->rawColumns(['total_pinjaman'])
            ->only([
                'DT_RowIndex',
                'id_code_lender',
                'nama',
                'total_pinjaman',
                'revenue',
            ])
            ->toJson();
    }

    public function invoicingLenderPending()
    {
        $historyInvoicingLenderPending = $this->invoiceLenderTableQuery('belum_lunas');

        return DataTables::of($historyInvoicingLenderPending)
            ->addIndexColumn()

            ->addColumn('pendingApproval', function ($row) {
                if ($row->has_pending_approval) {
                    $pendingApproval = '<span class="badge badge-warning">Locked</span>';
                } else {
                    $pendingApproval = '<span class="badge badge-success">Available to Edit</span>';
                }

                return $pendingApproval;
            })

            ->addColumn('period', function ($row) {
                return Carbon::parse($row->period)->format('Y-m-d');
            })

            ->addColumn('id_code_lender', function ($row) {
                return $row->id_code_lender;
            })

            ->addColumn('nama_lender', function ($row) {
                return $row->nama_lender;
            })

            ->addColumn('total_pinjaman', function ($row) {
                return '<span class="badge badge-success">'.$row->total_pinjaman.'</span>';
            })

            ->addColumn('revenue', function ($row) {
                return 'Rp  '.number_format($row->revenue);
            })

            // ->addColumn('download', function ($row) {
            //     $url = route('admin.lender.invoicing.download', encryptSecure($row->id));
            //     return '<a href="' . $url . '" target="_blank"><i class="mdi mdi-download"></i></a>';
            // })

            ->addColumn('download', function ($row) {
                return '
                        <a href="javascript:void(0)"
                        class="btn-download-invoice"
                        data-id="'.encryptSecure($row->id).'">
                        <i class="mdi mdi-download"></i>
                        </a>
                    ';
            })

            ->addColumn('action', function ($row) {
                // return '<button class="btn btn-sm btn-primary mr-2" onclick="editInvocingLender(' . $row->id . ')"><i class="mdi mdi-pencil"></i> </button>';
                if ($row->has_pending_approval) {
                    $action = '<button class="btn btn-sm btn-primary mr-2 disabled" ><i class="mdi mdi-pencil"></i> </button>';
                } else {
                    $action = '<button class="btn btn-sm btn-primary mr-2" onclick="editInvocingLender('.$row->id.')"><i class="mdi mdi-pencil"></i> </button>';
                }

                return $action;
            })

            ->rawColumns(['total_pinjaman', 'download', 'action', 'pendingApproval'])
            ->only([
                'DT_RowIndex',
                'no_invoice',
                'pendingApproval',
                'period',
                'id_code_lender',
                'nama_lender',
                'total_pinjaman',
                'revenue',
                'download',
                'action',
            ])
            ->toJson();
    }

    public function invoicingLenderDone()
    {
        $historyInvoicingLenderPending = $this->invoiceLenderTableQuery('lunas');

        return DataTables::of($historyInvoicingLenderPending)
            ->addIndexColumn()

            ->addColumn('period', function ($row) {
                return Carbon::parse($row->period)->format('Y-m-d');
            })

            ->addColumn('id_code_lender', function ($row) {
                return $row->id_code_lender;
            })

            ->addColumn('nama_lender', function ($row) {
                return $row->nama_lender;
            })

            ->addColumn('total_pinjaman', function ($row) {
                return '<span class="badge badge-success">'.$row->total_pinjaman.'</span>';
            })

            ->addColumn('revenue', function ($row) {
                return 'Rp  '.number_format($row->revenue);
            })

            ->addColumn('download', function ($row) {
                $url = route('admin.lender.invoicing.download', encryptSecure($row->id));

                return '<a href="'.$url.'" target="_blank"><i class="mdi mdi-download"></i></a>';
            })

            ->rawColumns(['total_pinjaman', 'download', 'action', 'pendingApproval'])
            ->only([
                'DT_RowIndex',
                'no_invoice',
                'pendingApproval',
                'period',
                'id_code_lender',
                'nama_lender',
                'total_pinjaman',
                'revenue',
                'download',
            ])
            ->toJson();
    }

    private function invoiceLenderTableQuery(string $status): Builder
    {
        $invoiceTable = (new history_invoicing_lender)->getTable();
        $lenderTable = (new lender)->getTable();
        $pinjamanTable = (new pinjaman)->getTable();
        $disbursementTable = (new disbursement)->getTable();

        $matchingPeriodSql = "DATE_FORMAT({$disbursementTable}.created_at, '%Y-%m') = DATE_FORMAT({$invoiceTable}.period, '%Y-%m')";

        return history_invoicing_lender::query()
            ->where("{$invoiceTable}.status", $status)
            ->leftJoin($lenderTable, "{$lenderTable}.id", '=', "{$invoiceTable}.id_lender")
            ->select([
                "{$invoiceTable}.*",
                "{$lenderTable}.id_code_lender as id_code_lender",
                "{$lenderTable}.nama as nama_lender",
            ])
            ->withExists(['pendingApproval as has_pending_approval'])
            ->selectSub(function ($query) use ($invoiceTable, $pinjamanTable, $disbursementTable, $matchingPeriodSql) {
                $query->from($pinjamanTable)
                    ->join($disbursementTable, "{$disbursementTable}.pinjaman_id", '=', "{$pinjamanTable}.id")
                    ->whereColumn("{$pinjamanTable}.id_lender", "{$invoiceTable}.id_lender")
                    ->whereIn("{$pinjamanTable}.status", ['approve', 'done'])
                    ->whereNull("{$pinjamanTable}.deleted_at")
                    ->whereRaw($matchingPeriodSql)
                    ->selectRaw("COUNT(DISTINCT {$pinjamanTable}.id)");
            }, 'total_pinjaman')
            ->selectSub(function ($query) use ($invoiceTable, $pinjamanTable, $disbursementTable, $matchingPeriodSql) {
                $query->from($pinjamanTable)
                    ->join($disbursementTable, "{$disbursementTable}.pinjaman_id", '=', "{$pinjamanTable}.id")
                    ->whereColumn("{$pinjamanTable}.id_lender", "{$invoiceTable}.id_lender")
                    ->whereIn("{$pinjamanTable}.status", ['approve', 'done'])
                    ->whereNull("{$pinjamanTable}.deleted_at")
                    ->whereRaw($matchingPeriodSql)
                    ->selectRaw("COALESCE(SUM({$pinjamanTable}.biaya_admin_pandu), 0) + COALESCE(SUM({$pinjamanTable}.biaya_layanan), 0)");
            }, 'revenue');
    }

    public function downloadInvoice(Request $request, $id)
    {
        $id = decryptSecure($id);

        /*
    |--------------------------------------------------------------------------
    | Selected Columns
    |--------------------------------------------------------------------------
    | Ambil kolom dari modal download.
    | Kalau kosong, pakai default agar layout tidak terlalu melebar.
    */
        $allowedColumns = [
            'tanggal_pinjaman',
            'nip',
            'nominal_pinjaman',
            'rate',
            'fee_tomi',
            'biaya_layanan',
            'biaya_admin',
            'total',
        ];

        $selectedColumns = $request->get('columns', []);

        if (! is_array($selectedColumns)) {
            $selectedColumns = [];
        }

        $selectedColumns = array_values(array_intersect($selectedColumns, $allowedColumns));

        if (empty($selectedColumns)) {
            $selectedColumns = [
                'tanggal_pinjaman',
                'nip',
                'nominal_pinjaman',
                'rate',
                'fee_tomi',
                'biaya_layanan',
                'biaya_admin',
                'total',
            ];
        }

        /*
    |--------------------------------------------------------------------------
    | Data Invoice
    |--------------------------------------------------------------------------
    */
        $historyInvoicingLenderPending = history_invoicing_lender::where('id', $id)
            ->with([
                'lender.pinjaman.disbursement',
                'lender.pinjaman.data_karyawan',
                'lender.pinjaman.perusahaan',
                'pendingApproval',
            ])
            ->first();

        if (! $historyInvoicingLenderPending) {
            abort(404, 'Data invoice tidak ditemukan.');
        }

        /*
    |--------------------------------------------------------------------------
    | Filter Pinjaman Berdasarkan Periode Invoice
    |--------------------------------------------------------------------------
    */
        if ($historyInvoicingLenderPending->lender) {
            $filteredPinjaman = $historyInvoicingLenderPending->lender->pinjaman
                ->filter(function ($pinjaman) use ($historyInvoicingLenderPending) {
                    return $pinjaman->disbursement &&
                        Carbon::parse($pinjaman->disbursement->created_at)->format('Y-m') ===
                        Carbon::parse($historyInvoicingLenderPending->period)->format('Y-m');
                })
                ->values();

            /*
        |--------------------------------------------------------------------------
        | Perhitungan Total
        |--------------------------------------------------------------------------
        | Rate = 1,5% dari nominal pinjaman
        | Biaya Layanan = Rp 10.000 per pinjaman
        | Biaya Admin = 1% dari nominal pinjaman
        */
            $ratePersen = 1.5;
            $biayaLayananPerPinjaman = 10000;

            $totalRate = $filteredPinjaman->sum(function ($pinjaman) use ($ratePersen) {
                return ($pinjaman->nominal ?? 0) * ($ratePersen / 100);
            });

            $totalBiayaLayanan = $filteredPinjaman->count() * $biayaLayananPerPinjaman;

            $totalBiayaAdmin = $filteredPinjaman->sum(function ($pinjaman) {
                return ($pinjaman->nominal ?? 0) * 0.01;
            });

            /*
        |--------------------------------------------------------------------------
        | Karena Fee TOMI mengikuti Rate 1,5%
        |--------------------------------------------------------------------------
        */
            $totalFeeTomi = $totalRate;

            $totalKeseluruhan = $totalFeeTomi + $totalBiayaLayanan + $totalBiayaAdmin;

            $historyInvoicingLenderPending->lender->total_pinjaman = $filteredPinjaman->count();
            $historyInvoicingLenderPending->lender->total_rate = $totalRate;
            $historyInvoicingLenderPending->lender->total_fee_tomi = $totalFeeTomi;
            $historyInvoicingLenderPending->lender->total_biaya_layanan = $totalBiayaLayanan;
            $historyInvoicingLenderPending->lender->total_biaya_admin = $totalBiayaAdmin;
            $historyInvoicingLenderPending->lender->total_keseluruhan = $totalKeseluruhan;

            $historyInvoicingLenderPending->lender->setRelation('pinjaman', $filteredPinjaman);
        }

        $rekening = rekening_pandu::with('bank')->first();

        /*
    |--------------------------------------------------------------------------
    | Generate PDF
    |--------------------------------------------------------------------------
    | Gunakan legal landscape supaya kolom tidak keluar layout.
    */
        $pdf = Pdf::loadView(
            'admin.ksp.lender.invoicing.invoicing',
            compact(
                'historyInvoicingLenderPending',
                'rekening',
                'selectedColumns'
            )
        )
            ->setPaper('legal', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
            ]);

        return $pdf->stream(
            'invoice-lender-'.$historyInvoicingLenderPending->no_invoice.'.pdf'
        );
    }

    // public function downloadInvoice($id)
    // {
    //     $id = decryptSecure($id);
    //     $historyInvoicingLenderPending = history_invoicing_lender::where('id', $id)
    //         ->with([
    //             'lender.pinjaman.disbursement',
    //             'pendingApproval'
    //         ])->first();

    //     if ($historyInvoicingLenderPending && $historyInvoicingLenderPending->lender) {
    //         $filteredPinjaman = $historyInvoicingLenderPending->lender->pinjaman->filter(function ($pinjaman) use ($historyInvoicingLenderPending) {
    //             return $pinjaman->disbursement &&
    //                 Carbon::parse($pinjaman->disbursement->created_at)->format('Y-m') === Carbon::parse($historyInvoicingLenderPending->period)->format('Y-m');
    //         });

    //         $historyInvoicingLenderPending->lender->total_pinjaman = $filteredPinjaman->count();
    //         $historyInvoicingLenderPending->lender->total_biaya_admin = $filteredPinjaman->sum('biaya_admin_pandu');
    //         $historyInvoicingLenderPending->lender->total_biaya_layanan = $filteredPinjaman->sum('biaya_layanan');
    //         $historyInvoicingLenderPending->lender->setRelation('pinjaman', $filteredPinjaman);
    //     }

    //     // $historyInvoicingLenderPending = history_invoicing_lender::where('id', $id)->with([
    //     //     'lender' => function ($q) {
    //     //         $q->withWhereHas('pinjaman', function ($sub) {
    //     //             $sub->whereBetween('created_at', [
    //     //                 // Carbon::now()->startOfMonth(),
    //     //                 // Carbon::now()->endOfMonth()
    //     //                 Carbon::parse('2025-09-01'),
    //     //                 Carbon::parse('2025-09-30'),
    //     //             ])
    //     //                 ->whereIn('status', ['approve', 'done']);
    //     //         })
    //     //             ->withCount(['pinjaman as total_pinjaman' => function ($sub) {
    //     //                 $sub->whereBetween('created_at', [
    //     //                     // Carbon::now()->startOfMonth(),
    //     //                     // Carbon::now()->endOfMonth()
    //     //                     Carbon::parse('2025-09-01'),
    //     //                     Carbon::parse('2025-09-30'),
    //     //                 ])
    //     //                     ->whereIn('status', ['approve', 'done']);
    //     //             }])
    //     //             ->withSum(['pinjaman as total_biaya_admin' => function ($sub) {
    //     //                 $sub->whereBetween('created_at', [
    //     //                     // Carbon::now()->startOfMonth(),
    //     //                     // Carbon::now()->endOfMonth()
    //     //                     Carbon::parse('2025-09-01'),
    //     //                     Carbon::parse('2025-09-30'),
    //     //                 ])
    //     //                     ->whereIn('status', ['approve', 'done']);
    //     //             }], 'biaya_admin_pandu')
    //     //             ->withSum(['pinjaman as total_biaya_layanan' => function ($sub) {
    //     //                 $sub->whereBetween('created_at', [
    //     //                     // Carbon::now()->startOfMonth(),
    //     //                     // Carbon::now()->endOfMonth()
    //     //                     Carbon::parse('2025-09-01'),
    //     //                     Carbon::parse('2025-09-30'),
    //     //                 ])
    //     //                     ->whereIn('status', ['approve', 'done']);
    //     //             }], 'biaya_layanan');
    //     //     }
    //     // ])->first();

    //     $rekening = rekening_pandu::with('bank')->first();
    //     return view("admin.ksp.lender.invoicing.invoicing", compact('historyInvoicingLenderPending', 'rekening'));
    // }

    public function approvePendingInvoice()
    {
        $lenderUpdate = history_invoicing_lender_update::query()
            ->select('history_invoicing_lender_updates.*')
            ->where(function ($query) {
                $query->where('first_approver_id', '!=', auth('admin')->id())
                    ->orWhereNull('first_approver_id');
            })
            ->where(function ($query) {
                $query->where('second_approver_id', '!=', auth('admin')->id())
                    ->orWhereNull('second_approver_id');
            })
            ->with([
                'history_invoicing.lender',
                'requester',
                'first_approver',
                'second_approver',
            ]);

        return DataTables::of($lenderUpdate)
            ->addIndexColumn()

            ->addColumn('waktu', function ($row) {
                return Carbon::parse($row->created_at)->format('Y-m-d H:i:s');
            })

            ->addColumn('requester', function ($row) {
                return $row->requester?->username ?? '-';
            })

            ->addColumn('status_pertama', function ($row) {
                if ($row->status_pertama == 'pending') {
                    $badge = '<span class="badge badge-warning">PENDING</span>';
                } elseif ($row->status_pertama == 'reject') {
                    $badge = '<span class="badge badge-danger">REJECT</span>';
                } else {
                    $badge = '<span class="badge badge-success">APPROVE</span>';
                }

                return $badge;
            })

            ->addColumn('status_kedua', function ($row) {
                if ($row->status_kedua == 'pending') {
                    $badge = '<span class="badge badge-warning">PENDING</span>';
                } elseif ($row->status_kedua == 'reject') {
                    $badge = '<span class="badge badge-danger">REJECT</span>';
                } else {
                    $badge = '<span class="badge badge-success">APPROVE</span>';
                }

                return $badge;
            })

            ->addColumn('first_approver_id', function ($row) {
                return $row->first_approver == null ? '-' : $row->first_approver->username;
            })

            ->addColumn('second_approver_id', function ($row) {
                return $row->second_approver == null ? '-' : $row->second_approver->username;
            })

            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="editApproval('.$row->id.')"><i class="mdi mdi-pencil"></i> </button>';
            })

            ->rawColumns(['action', 'status_kedua', 'status_pertama'])
            ->only([
                'DT_RowIndex',
                'waktu',
                'requester',
                'status_pertama',
                'status_kedua',
                'first_approver_id',
                'second_approver_id',
                'action',
            ])
            ->toJson();
    }

    public function invoicingLenderPendingUpdate(UpdateInvoiceRequest $req)
    {
        $invocingLender = history_invoicing_lender::where('id', $req->id)->first();
        $oldData = $invocingLender->getOriginal();

        $invocingLender->status = 'lunas';
        $invocingLender->update_by = auth('admin')->user()->id;
        $invocingLender->tanggal_pembayaran = $req->tanggal;

        $changedData = $invocingLender->getDirty();

        $invocingLender->update_by = auth('admin')->user()->id;

        $logData = [
            'old_data' => array_intersect_key($oldData, $changedData), // Ambil data lama hanya untuk kolom yang berubah
            'new_data' => $changedData,
        ];

        $lender_updates = new history_invoicing_lender_update;
        $lender_updates->invoicing_lender_id = $invocingLender->id;
        $lender_updates->new_data = json_encode($logData);
        $lender_updates->requester_id = auth('admin')->user()->id;
        $lender_updates->status_pertama = 'pending';
        $lender_updates->status_kedua = 'pending';
        $lender_updates->save();

        return response()->json([
            'status' => 200,
            'message' => 'Request perubahan transaksi invoicing Lender Berhasil, Harap menunggu approval',
        ], 200);
    }

    public function approvalDetailInvocing(Request $req)
    {
        $history_invoicing_lender = history_invoicing_lender_update::query()
            ->where('id', $req->id)
            ->where(function ($query) {
                $query->where('first_approver_id', '!=', auth('admin')->id())
                    ->orWhereNull('first_approver_id');
            })
            ->where(function ($query) {
                $query->where('second_approver_id', '!=', auth('admin')->id())
                    ->orWhereNull('second_approver_id');
            })
            ->with([
                'history_invoicing.lender',
                'requester',
                'first_approver',
                'second_approver',
            ])
            ->first();

        return response()->json([
            'status' => 200,
            'data' => new InvoicingUpdateDetailResource($history_invoicing_lender),
        ], 200);
    }

    public function approvalInvoicingAction(Request $req)
    {
        $lender_updates = history_invoicing_lender_update::where('id', $req->id)->first();
        // cek status pertama
        if ($lender_updates->status_pertama == 'pending' || $lender_updates->first_approver_id == null) {
            $lender_updates->status_pertama = $req->action;
            $lender_updates->first_approver_id = auth('admin')->user()->id;
        } elseif ($lender_updates->status_kedua == 'pending' || $lender_updates->second_approver_id == null) {
            $lender_updates->status_kedua = $req->action;
            $lender_updates->second_approver_id = auth('admin')->user()->id;
        }
        $lender_updates->save();

        if ($lender_updates->status_pertama == 'approve' && $lender_updates->status_kedua == 'approve') {
            $lender_invocing = history_invoicing_lender::where('id', $lender_updates->invoicing_lender_id)->first();

            if ($lender_invocing) {
                $newData = json_decode($lender_updates->new_data, true);
                $lender_invocing->update($newData['new_data']);
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengupdate invocing approval lender',
        ], 200);
    }
}
