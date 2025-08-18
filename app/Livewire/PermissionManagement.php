<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class PermissionManagement extends Component
{
    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public $showModal = false;
    public $permissionId;
    public $isEditMode = false;
    public $name;

    public $showConfirmModal = false;
    public $permissionToDelete;

    protected $listeners = ['permissionUpdated' => '$refresh'];

    // --- ¡AQUÍ ESTÁ LA CORRECCIÓN! ---
    // Definimos las reglas de validación para las propiedades del componente.
    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions')->ignore($this->permissionId)
            ],
        ];
    }
    // ------------------------------------

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openModal($permissionId = null)
    {
        $this->resetInputFields();
        if ($permissionId) {
            $permission = Permission::findOrFail($permissionId);
            $this->permissionId = $permission->id;
            $this->name = $permission->name;
            $this->isEditMode = true;
        } else {
            $this->isEditMode = false;
        }
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->resetValidation();
        $this->permissionId = null;
        $this->name = '';
        $this->isEditMode = false;
    }

    public function savePermission()
    {
        $this->authorize($this->isEditMode ? 'permission-edit' : 'permission-create');

        // Ahora, esta llamada a validate() encontrará el método rules() y funcionará.
        $validatedData = $this->validate();

        Permission::updateOrCreate(
            ['id' => $this->permissionId],
            ['name' => $validatedData['name']]
        );

        $this->dispatch('alert', [
            'type' => 'success',
            'message' => 'Permiso ' . ($this->isEditMode ? 'actualizado' : 'creado') . ' correctamente.'
        ]);
        $this->closeModal();
    }

    public function openConfirmModal($permissionId)
    {
        $this->permissionToDelete = Permission::findOrFail($permissionId);
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->permissionToDelete = null;
    }

    public function deletePermission()
    {
        $this->authorize('permission-delete');

        if ($this->permissionToDelete) {
            $this->permissionToDelete->delete();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Permiso eliminado correctamente.']);
            $this->closeConfirmModal();
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Permission::query();
        $searchTerm = trim($this->search);

        // 1. Aplicamos la búsqueda primero
        if (!empty($searchTerm)) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }

        // 2. Aplicamos la paginación al final
        $registros = $query->orderBy('name', 'asc')->paginate(12);

        // 3. Comprobamos si el resultado paginado está vacío
        if ($registros->isEmpty() && !empty($searchTerm)) {
            $this->dispatch('alert', [
                'type' => 'info',
                'message' => "No se encontraron resultados para '{$searchTerm}'."
            ]);
        }

        return view('livewire.permission-management', compact('registros'));
    }
}
