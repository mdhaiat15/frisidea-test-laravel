<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\CutiRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function counterSisaCuti(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('id', $user->id)->first();

        if (!$employee) {
            return response()->json([
                'data' => [],
                'message' => 'Data employee tidak ditemukan.',
                'status' => 404,
            ], 404);
        }

        return response()->json([
            'data' => 'Sisa Cuti = '.$employee->sisa_cuti,
            'message' => 'berhasil mengambil data sisa cuti',
            'status' => 200,
        ], 200);
    }

    public function pengajuanCuti(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
            'pesan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'data' => $validator->errors(),
                'message' => 'Validasi gagal',
                'status' => 400,
            ], 400);
        }

        $user = $request->user();

        $sisaCuti = Employee::where('id', $user->id)->first();

        if (!$sisaCuti || $sisaCuti->sisa_cuti <= 0) {
            return response()->json([
                'data' => [],
                'message' => 'Sisa cuti tidak mencukupi.',
                'status' => 404,
            ], 400);
        }

        $cutiRequest = new CutiRequest();
        $cutiRequest->employee_id = $user->id;
        $cutiRequest->tanggal_awal = $request->tanggal_awal;
        $cutiRequest->tanggal_akhir = $request->tanggal_akhir;
        $cutiRequest->pesan = $request->pesan;
        $cutiRequest->status = 'pending';
        $cutiRequest->save();

        return response()->json([
            'data' => $cutiRequest, 
            'message' => 'Pengajuan cuti berhasil diajukan.',
            'status' => 201,
        ], 201);
    }

    public function listHistoryPengajuanCuti(Request $request)
    {
        $user = $request->user();
        $history = $user->cutiRequests()->with('cutiHistories')->get();

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
