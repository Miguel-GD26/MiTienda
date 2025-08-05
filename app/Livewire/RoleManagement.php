<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RoleManagement extends Component
{
    use WithPagination;

    // Propiedades para la vista principal
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    // Propiedades para el modal de creación/edición
    public $showModal = false;
    public $roleId;
    public $isEditMode = false;
    public $name;
    // Esta propiedad será sincronizada con AlpineJS
    public $selectedPermissions = [];

    // Propiedades para el modal de confirmación
    public $showConfirmModal = false;
    public $roleToDelete;

    // Listener para actualizar
    protected $listeners = ['roleUpdated' => '$refresh'];

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($this->roleId)],
            'selectedPermissions' => 'nullable|array',
            'selectedPermissions.*' => 'exists:permissions,name',
        ];
    }
    
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    public function openModal($roleId = null)
    {
        $this->resetInputFields();
        if ($roleId) {
            $role = Role::with('permissions')->findOrFail($roleId);
            if (!auth()->user()->hasRole('super_admin') && $role->name === 'super_admin') {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'No tienes permiso para editar este rol.']);
                return;
            }
            $this->roleId = $role->id;
            $this->name = $role->name;
            // Pasamos los permisos iniciales a la propiedad pública
            $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
            $this->isEditMode = true;
        } else {
            $this->isEditMode = false;
            $this->dispatch('alert', [
                'type' => 'info',
                'message' => 'Recuerda nombrar los roles en minúsculas y sin espacios.'
            ]);
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
        $this->roleId = null;
        $this->name = '';
        $this->selectedPermissions = [];
        $this->isEditMode = false;
    }
// En app/Livewire/RoleManagement.php -> saveRole()

    public function saveRole()
    {
        $validatedData = $this->validate();
        
        DB::beginTransaction();
        try {
            $role = Role::updateOrCreate(['id' => $this->roleId], ['name' => $this->name]);
            $role->syncPermissions($this->selectedPermissions);

            DB::commit();

            // VERIFICACIÓN DE ADVERTENCIA
            if (empty($this->selectedPermissions)) {
                $this->dispatch('alert', [
                    'type' => 'warning',
                    'message' => "El rol '{$role->name}' se guardó sin ningún permiso asignado."
                ]);
            } else {
                $this->dispatch('alert', [
                    'type' => 'success',
                    'message' => 'Rol ' . ($this->isEditMode ? 'actualizado' : 'creado') . ' correctamente.'
                ]);
            }

            $this->closeModal();
            // ... catch ...
        } catch (\Exception $e) {
                DB::rollBack();
                $this->dispatch('alert', [
                    'type' => 'error',
                    'message' => 'Ocurrió un error al guardar el rol.'
                ]);
        }
    }
   
    
    public function openConfirmModal($roleId)
    {
        $this->roleToDelete = Role::findOrFail($roleId);
        if ($this->roleToDelete->name === 'super_admin') {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'El rol Super Admin no puede ser eliminado.']);
            return;
        }
        $this->showConfirmModal = true;
    }
    
    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->roleToDelete = null;
    }

    // En app/Livewire/RoleManagement.php -> deleteRole()

    public function deleteRole()
    {
        if ($this->roleToDelete) {
            $role = $this->roleToDelete;

            // VERIFICACIÓN DE ADVERTENCIA
            if ($role->users()->count() > 0) {
                $this->dispatch('alert', [
                    'type' => 'warning',
                    'message' => "No se puede eliminar el rol '{$role->name}' porque está asignado a {$role->users()->count()} usuario(s)."
                ]);
                $this->closeConfirmModal();
                return; // Detenemos la ejecución
            }

            // Si pasa la verificación, se procede con el borrado.
            $role->delete();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Rol eliminado correctamente.']);
            $this->closeConfirmModal();
        }
    }

    public function render()
    {
        $user = Auth::user();
        $query = Role::with('permissions');

        if (!$user->hasRole('super_admin')) {
            $query->where('name', '!=', 'super_admin');
        }

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        

        $registros = $query->orderBy('id', 'asc')->paginate(9);
        
        $allPermissions = Permission::all()->sortBy('name');

        return view('livewire.role-management', [
            'registros' => $registros,
            'allPermissions' => $allPermissions,
        ]);
    }
}