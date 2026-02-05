<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\YatimPiatu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class YatimPiatuController extends Controller
{
    // Definisikan fileFields sebagai property class
    protected $fileFields = [
        'imageskartukeluarga',
        'imagesktpwali',
        'imagesketerangansiswaaktif',
        'imagessuratkematian',
        'imagessurattidakmenerimabeasiswa',
        'imagesuratsktm'
    ];

    public function index(Request $request)
    {
        try {
            $query = YatimPiatu::with('user');

            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%")
                        ->orWhere('asal_sekolah', 'like', "%{$search}%");
                });
            }

            if ($request->has('jenjang') && $request->jenjang != '') {
                $query->where('jenjang', $request->jenjang);
            }

            $sortField = $request->get('sort_field', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortField, $sortOrder);

            $perPage = $request->get('per_page', 10);
            $yatimPiatu = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $yatimPiatu,
                'message' => 'Data yatim berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data yatim: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'nik' => 'required|string|max:16|unique:yatim_piatus,nik',
                'nisn' => 'required|string|max:10|unique:yatim_piatus,nisn',
                'npsn' => 'nullable|string|max:8',
                'jenjang' => 'required|in:SD,SMP,SMA,SMK',
                'name' => 'required|string|max:255',
                'asal_sekolah' => 'required|string|max:255',
                'alamat' => 'required|string',
                'tempat_lahir' => 'required|string|max:255',
                'tanggal_lahir' => 'required|date',
                'imageskartukeluarga' => 'required|file|mimes:pdf|max:5120',
                // 'imagesktpwali' => 'required|file|mimes:pdf|max:5120',
                'imagesketerangansiswaaktif' => 'required|file|mimes:pdf|max:5120',
                'imagessuratkematian' => 'required|file|mimes:pdf|max:5120',
                'imagessurattidakmenerimabeasiswa' => 'required|file|mimes:pdf|max:5120',
                'imagesuratsktm' => 'required|file|mimes:pdf|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Upload file-file PDF
            $data = $request->except($this->fileFields);

            foreach ($this->fileFields as $field) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $filename = time() . '_' . $field . '_' . uniqid() . '.pdf';
                    $path = $file->storeAs('sertifikat/yatim', $filename, 'public');
                    $data[$field] = $filename; // Simpan hanya nama file, bukan full URL
                }
            }

            $yatimPiatu = YatimPiatu::create($data);

            // Load relationship untuk response
            $yatimPiatu->load('user');

            return response()->json([
                'success' => true,
                'data' => $yatimPiatu,
                'message' => 'Data yatim berhasil disimpan'
            ], 201);
        } catch (\Exception $e) {
            // Hapus file yang sudah diupload jika terjadi error
            if (isset($data)) {
                foreach ($this->fileFields as $field) {
                    if (isset($data[$field])) {
                        Storage::disk('public')->delete('sertifikat/yatim/' . $data[$field]);
                    }
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data yatim: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $yatimPiatu = YatimPiatu::with('user')->find($id);

            if (!$yatimPiatu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $yatimPiatu,
                'message' => 'Data yatim berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data yatim: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $yatim)
    {
        try {
            // Handle route model binding
            if (is_object($yatim)) {
                $yatimPiatu = $yatim;
            } else {
                $yatimPiatu = YatimPiatu::find($yatim);
            }

            if (!$yatimPiatu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim tidak ditemukan'
                ], 404);
            }

            // Data yang akan diupdate
            $updateData = [
                'user_id' => $request->user_id,
                'nik' => $request->nik,
                'nisn' => $request->nisn,
                'npsn' => $request->npsn,
                'jenjang' => $request->jenjang,
                'name' => $request->name,
                'asal_sekolah' => $request->asal_sekolah,
                'alamat' => $request->alamat,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
            ];

            // Handle file upload untuk setiap field file
            $fileFields = [
                'imageskartukeluarga',
                'imagesktpwali',
                'imagesketerangansiswaaktif',
                'imagessuratkematian',
                'imagessurattidakmenerimabeasiswa',
                'imagesuratsktm'
            ];

            foreach ($fileFields as $field) {
                if ($request->file($field)) {
                    //remove old file
                    Storage::disk('public')->delete('sertifikat/yatim/' . basename($yatimPiatu->$field));

                    //upload new file
                    $file = $request->file($field);
                    $file->storeAs('public/sertifikat/yatim', $file->hashName());
                    $updateData[$field] = $file->hashName();
                }
            }

            // Update data
            $yatimPiatu->update($updateData);

            // Refresh model untuk mendapatkan data terbaru dengan accessor
            $yatimPiatu->refresh();
            $yatimPiatu->load('user');

            if ($yatimPiatu) {
                return response()->json([
                    'success' => true,
                    'data' => $yatimPiatu,
                    'message' => 'Data yatim berhasil diperbarui!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Data yatim gagal diperbarui!'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data yatim: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $yatimPiatu = YatimPiatu::find($id);

            if (!$yatimPiatu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim tidak ditemukan'
                ], 404);
            }

            // Hapus file-file PDF yang terkait
            foreach ($this->fileFields as $field) {
                if ($yatimPiatu->$field) {
                    // Dapatkan nama file asli (tanpa URL) dari accessor
                    $originalFilename = $yatimPiatu->getRawOriginal($field);
                    Storage::disk('public')->delete('sertifikat/yatim/' . $originalFilename);
                }
            }

            $yatimPiatu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data yatim berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data yatim: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download file PDF
     */
    public function downloadFile($id, $field)
    {
        try {
            $yatimPiatu = YatimPiatu::find($id);

            if (!$yatimPiatu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim tidak ditemukan'
                ], 404);
            }

            // Validasi field
            if (!in_array($field, $this->fileFields)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Field file tidak valid'
                ], 400);
            }

            // Dapatkan nama file asli
            $filename = $yatimPiatu->getRawOriginal($field);

            if (!$filename) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            $filePath = 'sertifikat/yatim/' . $filename;

            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan di storage'
                ], 404);
            }

            return Storage::disk('public')->download($filePath, $filename);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getYatimPiatuByUserId($user_id)
    {
        try {
            $yatimPiatu = YatimPiatu::with('user')
                ->where('user_id', $user_id)
                ->orderBy('created_at', 'desc') // atau field lain yang sesuai
                ->get();

            if ($yatimPiatu->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim piatu tidak ditemukan untuk user ini'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $yatimPiatu,
                'count' => $yatimPiatu->count(),
                'message' => 'Data yatim piatu berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data yatim piatu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifikasi data yatim
     */
    public function verif($id)
    {
        try {
            $yatimPiatu = YatimPiatu::with('user')->find($id);

            if (!$yatimPiatu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim tidak ditemukan'
                ], 404);
            }

            // Update status_data menjadi 'verif'
            $yatimPiatu->update([
                'status_data' => 'verif'
            ]);

            // Refresh model untuk mendapatkan data terbaru
            $yatimPiatu->refresh();

            return response()->json([
                'success' => true,
                'data' => $yatimPiatu,
                'message' => 'Data yatim berhasil diverifikasi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi data yatim: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batalkan verifikasi data yatim
     */
    public function unverif($id)
    {
        try {
            $yatimPiatu = YatimPiatu::with('user')->find($id);

            if (!$yatimPiatu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim tidak ditemukan'
                ], 404);
            }

            // Update status_data menjadi null (belum terverifikasi)
            $yatimPiatu->update([
                'status_data' => null
            ]);

            // Refresh model untuk mendapatkan data terbaru
            $yatimPiatu->refresh();

            return response()->json([
                'success' => true,
                'data' => $yatimPiatu,
                'message' => 'Verifikasi data yatim berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan verifikasi data yatim: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tolak data yatim
     */
    public function reject($id)
    {
        try {
            $yatimPiatu = YatimPiatu::with('user')->find($id);

            if (!$yatimPiatu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim tidak ditemukan'
                ], 404);
            }

            // Update status_data menjadi 'ditolak'
            $yatimPiatu->update([
                'status_data' => 'ditolak'
            ]);

            // Refresh model untuk mendapatkan data terbaru
            $yatimPiatu->refresh();

            return response()->json([
                'success' => true,
                'data' => $yatimPiatu,
                'message' => 'Data yatim berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak data yatim: ' . $e->getMessage()
            ], 500);
        }
    }

    // alasan verif
    public function updateAlasanVerif(Request $request, $id)
    {
        try {
            $yatimPiatu = YatimPiatu::with('user')->find($id);

            if (!$yatimPiatu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'alasan_verif' => 'required|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $yatimPiatu->update([
                'alasan_verif' => $request->alasan_verif
            ]);

            $yatimPiatu->refresh();

            return response()->json([
                'success' => true,
                'data' => $yatimPiatu,
                'message' => 'Alasan verifikasi berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui alasan verifikasi: ' . $e->getMessage()
            ], 500);
        }
    }

     public function updateAlasanVerifKK(Request $request, $id)
    {
        try {
            $yatimPiatu = YatimPiatu::with('user')->find($id);

            if (!$yatimPiatu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'alasan_kk' => 'required|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $yatimPiatu->update([
                'alasan_kk' => $request->alasan_kk
            ]);

            $yatimPiatu->refresh();

            return response()->json([
                'success' => true,
                'data' => $yatimPiatu,
                'message' => 'Alasan verifikasi berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui alasan verifikasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifKartuKeluarga($id)
    {
        try {
            $yatimPiatu = YatimPiatu::with('user')->find($id);

            if (!$yatimPiatu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim tidak ditemukan'
                ], 404);
            }

            // Update verif_kk menjadi 'verif'
            $yatimPiatu->update([
                'verif_kk' => 'verif'
            ]);

            // Refresh model untuk mendapatkan data terbaru
            $yatimPiatu->refresh();

            return response()->json([
                'success' => true,
                'data' => $yatimPiatu,
                'message' => 'Kartu Keluarga berhasil diverifikasi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memverifikasi Kartu Keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Batalkan Verifikasi Kartu Keluarga
     */
    public function unverifKartuKeluarga($id)
    {
        try {
            $yatimPiatu = YatimPiatu::with('user')->find($id);

            if (!$yatimPiatu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim tidak ditemukan'
                ], 404);
            }

            // Update verif_kk menjadi null (belum terverifikasi)
            $yatimPiatu->update([
                'verif_kk' => null
            ]);

            // Refresh model untuk mendapatkan data terbaru
            $yatimPiatu->refresh();

            return response()->json([
                'success' => true,
                'data' => $yatimPiatu,
                'message' => 'Verifikasi Kartu Keluarga berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan verifikasi Kartu Keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tolak Kartu Keluarga
     */
    public function rejectKartuKeluarga($id)
    {
        try {
            $yatimPiatu = YatimPiatu::with('user')->find($id);

            if (!$yatimPiatu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data yatim tidak ditemukan'
                ], 404);
            }

            // Update verif_kk menjadi 'ditolak'
            $yatimPiatu->update([
                'verif_kk' => 'ditolak'
            ]);

            // Refresh model untuk mendapatkan data terbaru
            $yatimPiatu->refresh();

            return response()->json([
                'success' => true,
                'data' => $yatimPiatu,
                'message' => 'Kartu Keluarga berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menolak Kartu Keluarga: ' . $e->getMessage()
            ], 500);
        }
    }
}
