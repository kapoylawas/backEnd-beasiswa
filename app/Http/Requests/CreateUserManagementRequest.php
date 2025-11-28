<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserManagementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Pastikan ini true
    }

    public function rules(): array
    {
        return [
            'nik' => 'required|string|unique:users,nik',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required|in:male,female',
            'alamat' => 'required|string',
            'nokk' => 'nullable|string|max:16',
            'nohp' => 'nullable|string|max:15',
            'id_kecamatan' => 'nullable|integer',
            'id_kelurahan' => 'nullable|integer',
            'codepos' => 'nullable|string|max:5',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
        ];
    }

    public function messages(): array
    {
        return [
            'nik.required' => 'NIK wajib diisi',
            'nik.unique' => 'NIK sudah terdaftar',
            'email.required' => 'Email wajib diisi',
            'email.unique' => 'Email sudah terdaftar',
            'name.required' => 'Nama wajib diisi',
            'gender.required' => 'Jenis kelamin wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
        ];
    }
}