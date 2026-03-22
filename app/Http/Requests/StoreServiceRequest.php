<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ¡Importante cambiar a true!
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'foto_persona' => [
                'required', 
                'string', 
                function ($attribute, $value, $fail) {
                    // Verificar cabecera base64
                    if (!preg_match('/^data:image\/(\w+);base64,/', $value)) {
                        $fail('El campo '.$attribute.' debe ser un string Base64 válido.');
                        return;
                    }

                    // Decodificar
                    $data = substr($value, strpos($value, ',') + 1);
                    $decoded = base64_decode($data, true);

                    if ($decoded === false) {
                        $fail('El Base64 no se pudo decodificar.');
                        return;
                    }

                    // Verificar tipo MIME real del archivo
                    $finfo = finfo_open();
                    $mime = finfo_buffer($finfo, $decoded, FILEINFO_MIME_TYPE);
                    finfo_close($finfo);

                    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                        $fail('El archivo decodificado no es una imagen válida (Detectado: '.$mime.').');
                    }
                }
            ],
        ];
    }
}
