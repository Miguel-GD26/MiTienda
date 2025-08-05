<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserManagement extends Component
{
    use WithPagination; // Importante para la paginación

    // Propiedades para la búsqueda y paginación
    public $search = '';
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';

    // Propiedades para el modal y el formulario
    public $showModal = false;
    public $userId = null;
    public $isEditMode = false;
    
    // Propiedades del modelo User
    public $name, $email, $password, $password_confirmation, $role, $empresa_id, $activo;

    // Propiedades para la creación de empresa
    public $empresaOption = '';
    public $empresa_nombre, $empresa_rubro, $empresa_telefono_whatsapp;

    // Propiedades para los modales de confirmación
    public $showConfirmModal = false;
    public $confirmModalType = 'delete'; // 'delete' o 'toggle'
    public $userToDeleteOrToggle;

    // Listener para actualizar la tabla desde otros componentes si fuera necesario
    protected $listeners = ['userUpdated' => '$refresh'];

    // Reglas de validación
    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'role' => ['required', Rule::exists('roles', 'name')],
            'empresaOption' => 'nullable',
            'activo' => 'boolean'
        ];

        // Reglas para la contraseña
        if (!$this->isEditMode || !empty($this->password)) {
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['password_confirmation'] = 'required';
        }

        // Reglas para la empresa
        if (auth()->user()->hasRole('super_admin') && in_array($this->role, ['admin', 'vendedor', 'repartidor'])) {
            if ($this->empresaOption === 'crear_nueva') {
                $rules['empresa_nombre'] = 'required|string|max:255|unique:empresas,nombre';
                $rules['empresa_rubro'] = 'nullable|string|max:255';
                $rules['empresa_telefono_whatsapp'] = 'nullable|string|max:20';
            } else {
                // En modo creación, si no es crear_nueva, se requiere una empresa existente
                if (!$this->isEditMode) {
                    $rules['empresa_id'] = 'required|exists:empresas,id';
                }
            }
        }
        
        return $rules;
    }
    
    // Mensajes de validación personalizados
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openModal($userId = null)
    {
        $this->resetValidation();
        $this->resetInputFields();

        if ($userId) {
            $user = User::findOrFail($userId);
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->roles->first()->name ?? '';
            $this->empresa_id = $user->empresa_id;
            $this->activo = $user->activo;
            $this->isEditMode = true;
        } else {
            $this->isEditMode = false;
            $this->activo = true; // Valor por defecto
        }
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = '';
        $this->empresa_id = null;
        $this->activo = true;
        $this->isEditMode = false;
        $this->empresaOption = '';
        $this->empresa_nombre = '';
        $this->empresa_rubro = '';
        $this->empresa_telefono_whatsapp = '';
    }

    public function saveUser()
    {
        // Asignar empresa_id desde empresaOption si es un número
        if(is_numeric($this->empresaOption)){
            $this->empresa_id = $this->empresaOption;
        }

        $validatedData = $this->validate();

        DB::beginTransaction();
        try {
            $empresaId = $this->empresa_id;

            // Lógica para Super Admin creando/asignando empresa
            if (auth()->user()->hasRole('super_admin')) {
                if ($this->empresaOption === 'crear_nueva') {
                    $empresa = Empresa::create([
                        'nombre' => $validatedData['empresa_nombre'],
                        'slug' => Str::slug($validatedData['empresa_nombre']),
                        'rubro' => $this->empresa_rubro,
                        'telefono_whatsapp' => $this->empresa_telefono_whatsapp,
                    ]);
                    $empresaId = $empresa->id;
                }
            } elseif(!auth()->user()->hasRole('super_admin')) {
                // Para otros roles, asignar la empresa del usuario logueado
                $empresaId = auth()->user()->empresa_id;
            }

            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'activo' => $this->activo,
                'empresa_id' => $empresaId
            ];

            if (!empty($this->password)) {
                $userData['password'] = Hash::make($this->password);
            }

            $user = User::updateOrCreate(['id' => $this->userId], $userData);
            $user->syncRoles($this->role);

            DB::commit();
            session()->flash('mensaje', 'Usuario ' . ($this->isEditMode ? 'actualizado' : 'creado') . ' correctamente.');
            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Ocurrió un error: ' . $e->getMessage());
        }
    }
    
    public function openConfirmModal($type, $userId)
    {
        $this->userToDeleteOrToggle = User::findOrFail($userId);
        $this->confirmModalType = $type;
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->userToDeleteOrToggle = null;
    }

    public function confirmAction()
    {
        if ($this->confirmModalType === 'delete') {
            $this->deleteUser();
        } elseif ($this->confirmModalType === 'toggle') {
            $this->toggleStatus();
        }
    }

    private function deleteUser()
    {
        if ($this->userToDeleteOrToggle) {
            // Aquí puedes añadir la lógica compleja de borrado que tenías en el controlador
            $this->userToDeleteOrToggle->delete();
            session()->flash('mensaje', 'Usuario eliminado correctamente.');
            $this->closeConfirmModal();
        }
    }

    private function toggleStatus()
    {
        if ($this->userToDeleteOrToggle) {
            $this->userToDeleteOrToggle->activo = !$this->userToDeleteOrToggle->activo;
            $this->userToDeleteOrToggle->save();
            session()->flash('mensaje', 'Estado del usuario actualizado correctamente.');
            $this->closeConfirmModal();
        }
    }

    public function render()
    {
        // Replicamos la lógica de búsqueda y paginación
        $query = User::with('roles', 'empresa');
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('empresa_id', auth()->user()->empresa_id);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }
        
        $registros = $query->orderBy('id', 'asc')->paginate($this->perPage);
        
        // Datos para los selects del formulario
        $roles = Role::query()->when(!auth()->user()->hasRole('super_admin'), function ($q) {
            $q->where('name', '!=', 'super_admin');
        })->get();
        
        $empresas = auth()->user()->hasRole('super_admin') ? Empresa::all() : collect();

        return view('livewire.user-management', [
            'registros' => $registros,
            'roles' => $roles,
            'empresas' => $empresas
        ]);
    }
}