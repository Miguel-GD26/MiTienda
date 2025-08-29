<?php

namespace App\Livewire;

use App\Models\Empresa;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // <-- 1. IMPORTAR TRAIT PARA SUBIR ARCHIVOS

class CompanyManagement extends Component
{
    use WithPagination, WithFileUploads; // <-- 2. USAR EL TRAIT

    protected $paginationTheme = 'bootstrap';

    public string $search = '';
    public int $perPage = 10;
    public bool $showModal = false;
    public bool $isEditMode = false;
    public ?int $companyId = null;

    // --- 3. AÑADIR NUEVAS PROPIEDADES PARA LOS CAMPOS DE LA EMPRESA ---
    public string $nombre = '';
    public string $rubro = '';
    public string $telefono_whatsapp = '';
    public $logo; // Para el nuevo archivo de logo
    public ?string $existingLogoUrl = null; // Para mostrar el logo actual

    public bool $showConfirmModal = false;
    public ?Empresa $companyToDelete = null;

    protected function rules()
    {
        return [
            'nombre' => ['required', 'string', 'max:100', Rule::unique('empresas')->ignore($this->companyId)],
            // --- 4. AÑADIR REGLAS DE VALIDACIÓN PARA LOS NUEVOS CAMPOS ---
            'rubro' => 'nullable|string|max:255',
            'telefono_whatsapp' => ['nullable', 'regex:/^\d{9}$/'],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    private function resetInputFields()
    {
        $this->resetErrorBag();
        $this->companyId = null;
        $this->nombre = '';
        $this->isEditMode = false;
        // --- 5. LIMPIAR LAS NUEVAS PROPIEDADES ---
        $this->rubro = '';
        $this->telefono_whatsapp = '';
        $this->logo = null;
        $this->existingLogoUrl = null;
    }

    public function openModal(int $companyId = null)
    {
        $this->resetInputFields();

        if ($companyId) {
            $company = Empresa::findOrFail($companyId);
            $this->companyId = $company->id;
            $this->isEditMode = true;
            // --- 6. POBLAR TODOS LOS CAMPOS AL EDITAR ---
            $this->nombre = $company->nombre;
            $this->rubro = $company->rubro;
            $this->telefono_whatsapp = $company->telefono_whatsapp;
            if ($company->logo_url) {
                $this->existingLogoUrl = cloudinary()->image($company->logo_url)->toUrl();
            }
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function saveCompany()
    {
        $this->validate();

        $data = [
            'nombre' => $this->nombre,
            'slug' => Str::slug($this->nombre),
            // --- 7. INCLUIR NUEVOS CAMPOS EN LOS DATOS A GUARDAR ---
            'rubro' => $this->rubro,
            'telefono_whatsapp' => $this->telefono_whatsapp,
        ];

        // --- 8. LÓGICA PARA MANEJAR LA SUBIDA DEL LOGO ---
        if ($this->logo) {
            if ($this->companyId) {
                $company = Empresa::find($this->companyId);
                if ($company && $company->logo_url) {
                    cloudinary()->uploadApi()->destroy($company->logo_url);
                }
            }
            $uploadedFile = cloudinary()->uploadApi()->upload($this->logo->getRealPath(), ['folder' => 'logos_empresa']);
            $data['logo_url'] = $uploadedFile['public_id'];
        }

        Empresa::updateOrCreate(['id' => $this->companyId], $data);

        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'Empresa ' . ($this->isEditMode ? 'actualizada' : 'creada') . ' correctamente.'
        ]);
        
        // Si la empresa que se editó es la del usuario actual, refrescar el sidebar
        if ($this->isEditMode && auth()->user()->empresa_id == $this->companyId) {
            $this->dispatch('profileUpdated');
        }

        $this->closeModal();
    }

    public function openConfirmModal(int $companyId)
    {
        $this->companyToDelete = Empresa::findOrFail($companyId);
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->companyToDelete = null;
    }

    public function deleteCompany()
    {
        if ($this->companyToDelete) {
            // Opcional: Eliminar el logo de Cloudinary antes de borrar la empresa
            if ($this->companyToDelete->logo_url) {
                cloudinary()->uploadApi()->destroy($this->companyToDelete->logo_url);
            }
            $this->companyToDelete->delete();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Empresa eliminada correctamente.']);
        }
        $this->closeConfirmModal();
    }

    public function render()
    {
        $companies = Empresa::query()
            ->when($this->search, function ($query, $search) {
                return $query->where('nombre', 'LIKE', '%' . $search . '%')
                             ->orWhere('slug', 'LIKE', '%' . $search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.company-management', [
            'empresas' => $companies,
        ]);
    }
}