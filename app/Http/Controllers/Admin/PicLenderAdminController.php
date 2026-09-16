<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AddPicLenderAdminRequest;
use App\Http\Requests\Admin\Auth\UpdatePicLenderAdminRequest;
use App\Models\ksp_admin;
use App\Models\lender;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class PicLenderAdminController extends Controller
{
    public function index()
    {
        $lenders = lender::orderBy('nama')->get();

        return view('admin.auth.pic-lender.index', compact('lenders'));
    }

    public function table()
    {
        $picLenders = ksp_admin::with('lender')->orderByDesc('id')->get();

        return DataTables::of($picLenders)
            ->addIndexColumn()
            ->addColumn('lender', function ($row) {
                return $row->lender?->nama ?? '-';
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at ? Carbon::parse($row->created_at)->format('Y-m-d H:i:s') : '-';
            })
            ->addColumn('updated_at', function ($row) {
                return $row->updated_at ? Carbon::parse($row->updated_at)->format('Y-m-d H:i:s') : '-';
            })
            ->addColumn('action', function ($row) {
                return '<button class="btn btn-sm btn-primary mr-2" onclick="editPicLender('.$row->id.')"><i class="mdi mdi-pencil"></i></button>
                    <button class="btn btn-sm btn-danger mr-2" onclick="deletePicLender('.$row->id.')"><i class="mdi mdi-trash-can"></i></button>';
            })
            ->rawColumns(['action'])
            ->only([
                'DT_RowIndex',
                'created_at',
                'updated_at',
                'lender',
                'nama',
                'username',
                'email',
                'action',
            ])
            ->toJson();
    }

    public function store(AddPicLenderAdminRequest $request)
    {
        ksp_admin::create([
            'id_lender' => $request->id_lender,
            'nama' => $request->nama,
            'username' => $request->username,
            'remember_token' => Str::random(60),
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'PIC Lender Berhasil Ditambahkan',
        ], 200);
    }

    public function detail(Request $request)
    {
        $picLender = ksp_admin::where('id', $request->id)->firstOrFail();

        return response()->json([
            'status' => 200,
            'data' => [
                'id' => $picLender->id,
                'id_lender' => $picLender->id_lender,
                'nama' => $picLender->nama,
                'username' => $picLender->username,
                'email' => $picLender->email,
            ],
        ], 200);
    }

    public function update(UpdatePicLenderAdminRequest $request)
    {
        $picLender = ksp_admin::where('id', $request->id)->firstOrFail();
        $picLender->id_lender = $request->id_lender;
        $picLender->nama = $request->nama;
        $picLender->username = $request->username;
        $picLender->email = $request->email;

        if ($request->filled('password')) {
            $picLender->password = Hash::make($request->password);
        }

        $picLender->save();

        return response()->json([
            'status' => 200,
            'message' => 'PIC Lender Berhasil Diperbarui',
        ], 200);
    }

    public function destroy(Request $request)
    {
        $picLender = ksp_admin::where('id', $request->id)->firstOrFail();
        $picLender->delete();

        return response()->json([
            'status' => 200,
            'message' => 'PIC Lender Berhasil Dihapus',
        ], 200);
    }
}
