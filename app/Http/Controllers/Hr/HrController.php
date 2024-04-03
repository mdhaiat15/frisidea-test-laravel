<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\CutiHistory;
use App\Models\CutiRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class HrController extends Controller
{
    public function tambahKaryawan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|string|min:8',
            'sisa_cuti' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'message' => 'Validation Error',
                'status' => 400,
            ], 400);
        }

        $karyawan = new Employee();
        $karyawan->name = $request->name;
        $karyawan->email = $request->email;
        $karyawan->password = Hash::make($request->password);
        $karyawan->sisa_cuti = $request->sisa_cuti;
        $karyawan->save();

        return response()->json([
            'data' => $karyawan,
            'message' => 'Karyawan berhasil ditambahkan',
            'status' => 201,
        ], 201);
    }

    public function ubahKaryawan(Request $request, $id)
    {
        $karyawan = Employee::find($id);
        if (!$karyawan) {
            return response()->json([
                'data' => [],
                'message' => 'Karyawan tidak ditemukan',
                'status' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'email' => 'email|unique:employees,email,'.$id,
            'sisa_cuti' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'message' => 'Validation Error',
                'status' => 400,
            ], 400);
        }

        $karyawan->name = $request->input('name', $karyawan->name);
        $karyawan->email = $request->input('email', $karyawan->email);
        $karyawan->sisa_cuti = $request->input('sisa_cuti', $karyawan->sisa_cuti);
        $karyawan->save();

        return response()->json([
            'data' => $karyawan,
            'message' => 'Karyawan berhasil diubah',
            'status' => 200,
        ], 200);
    }

    public function hapusKaryawan($id)
    {
        $karyawan = Employee::find($id);
        if (!$karyawan) {
            return response()->json([
                'data' => [],
                'message' => 'Karyawan tidak ditemukan',
                'status' => 404,
            ], 404);
        }

        $karyawan->delete();

        return response()->json([
            'data' => [],
            'message' => 'Karyawan berhasil dihapus',
            'status' => 200,
        ], 200);
    }

    public function listPengajuanCuti()
    {
        $pengajuanCuti = CutiRequest::all();

        return response()->json([
            'status' => 'success',
            'pengajuan_cuti' => $pengajuanCuti,
        ], 200);
    }

    public function approveRejectPengajuanCuti(Request $request, $id)
    {
        $pengajuanCuti = CutiRequest::find($id);
        if (!$pengajuanCuti) {
            return response()->json([
                'data' => [],
                'message' => 'Pengajuan cuti tidak ditemukan',
                'status' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,rejected',
            'alasan' => $request->status == 'rejected' ? 'required|string' : 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'message' => 'Validation Error',
                'status' => 400,
            ], 400);
        }

        $pengajuanCuti->status = $request->status;
        $pengajuanCuti->save();

        $historyRequestCuti = CutiHistory::updateOrCreate(
            ['cuti_request_id' => $id],
            [
                'action' => $request->status,
                'alasan' => $request->alasan
            ]
        );

        return response()->json([
            'data' => $pengajuanCuti,
            'message' => 'Pengajuan cuti berhasil di'.$request->status,
            'status' => 200,
        ], 200);
    }

    public function listHistoryPengajuanCuti()
    {
        $history = CutiHistory::all();

        if (empty($history)) {
            return response()->json([
                'data' => [],
                'message' => 'List History tidak ditemukan.',
                'status' => 404,
            ], 404);
        }

        return response()->json([
            'data' => $history,
            'message' => 'List History ditemukan.',
            'status' => 200,
        ], 200);
    }
}
