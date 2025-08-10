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
    use WithPagination;

    //--- ESTADO DEL COMPONENTE Y PROPIEDADES ---//
    public $search = '';
    public $perPage = 10;
    protected $paginationTheme = 'bootstrap';

    //--- CONTROL DE MODALES ---//
    public $showModal = false;
    public $userId = null;
    public $isEditMode = false;
    
    //--- CAMPOS DEL FORMULARIO ---//
    public $name, $email, $password, $password_confirmation, $role, $empresa_id;
    public $activo;
    public $empresaOption = '';
    public $empresa_nombre, $empresa_rubro, $empresa_telefono_whatsapp;

    //--- MODAL DE CONFIRMACIÓN ---//
    public $showConfirmModal = false;
    public $confirmModalType = 'delete';
    public $userToDeleteOrToggle;
    
    //--- GESTIÓN DE SUSCRIPCIÓN ---//
    public $subscription_status = '';
    public $current_subscription_status = '';

    public $searchEmpresa = ''; // El texto que el usuario escribe para buscar
    public $empresaSeleccionadaNombre = '';

    


    //--- LISTENERS ---//
    protected $listeners = ['userUpdated' => '$refresh'];

    //--- REGLAS DE VALIDACIÓN ---//
    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'role' => ['required', Rule::exists('roles', 'name')],
            'activo' => 'boolean',
        ];

        if (!$this->isEditMode || !empty($this->password)) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        if (auth()->user()->hasRole('super_admin')) {
            // Validación para el campo de selección de empresa.
            $rules['empresaOption'] = [
                Rule::requiredIf(
                    !$this->isEditMode && 
                    in_array($this->role, ['admin', 'vendedor', 'repartidor'])
                )
            ];

            // Validación para los campos de "crear nueva empresa".
            $rules['empresa_nombre'] = 'required_if:empresaOption,crear_nueva|string|max:255|unique:empresas,nombre';
            $rules['empresa_rubro'] = 'required_if:empresaOption,crear_nueva|string|max:255';
            $rules['empresa_telefono_whatsapp'] = 'required_if:empresaOption,crear_nueva|string|digits:9';
        }
        
        return $rules;
    }
    
    

    public function updatedSearch()
    {
        // Resetea la paginación principal cuando se busca en la tabla.
        $this->resetPage();
    }

    public function loadEmpresas()
    {
        $query = trim($this->searchEmpresa);
        $perPage = 2;

        // Ahora guardamos todo el objeto paginador en la propiedad pública
        $this->empresaResults = Empresa::where('nombre', 'like', '%' . $query . '%')
            ->latest()
            ->paginate($perPage, ['*'], 'empresaPage');
    }


    // --- NUEVO MÉTODO PARA CARGAR LA SIGUIENTE PÁGINA ---
    public function loadMoreEmpresas()
    {
        $this->empresaPage = 1; 
        $this->loadEmpresas();
    }

    public function updatedEmpresaPage()
    {
        $this->loadEmpresas();
    }
    
    public function selectEmpresa($empresaId, $empresaNombre)
    {
        $this->empresa_id = $empresaId;
        $this->empresaOption = $empresaId;
        $this->empresaSeleccionadaNombre = $empresaNombre; // Guardamos el nombre
        $this->searchEmpresa = '';
        $this->resetPage('empresaPage'); 
    }
    public function cambiarEmpresa()
    {
        $this->reset(['empresa_id', 'empresaOption', 'empresaSeleccionadaNombre', 'searchEmpresa']);
        $this->resetPage('empresaPage');
    }
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    //--- GESTIÓN DEL MODAL DE EDICIÓN/CREACIÓN ---//
    public function openModal($userId = null)
    {
        $this->resetValidation();
        $this->resetInputFields();

        if ($userId) {
            $user = User::with('empresa')->findOrFail($userId);
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->role = $user->roles->first()->name ?? '';
            $this->empresa_id = $user->empresa_id;
            $this->activo = $user->activo;
            $this->isEditMode = true;
            if ($user->empresa) {
                $this->current_subscription_status = $user->empresa->subscription_status;
                $this->subscription_status = $user->empresa->subscription_status;
            }
        } else {
            $this->isEditMode = false;
            $this->activo = true;
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
        $this->subscription_status = '';
        $this->searchEmpresa = '';
        $this->empresaSeleccionadaNombre = '';
        $this->empresaPage = 1;
        $this->empresaResults = null; 
    }

    //--- ACCIONES CRUD (CREAR Y ACTUALIZAR) ---//
    public function saveUser()
    {
        if(is_numeric($this->empresaOption)){
            $this->empresa_id = $this->empresaOption;
        }

        $validatedData = $this->validate();

        DB::beginTransaction();
        try {
            $empresaId = $this->empresa_id;

            if (auth()->user()->hasRole('super_admin') && in_array($this->role, ['admin', 'vendedor', 'repartidor'])) {
                if ($this->empresaOption === 'crear_nueva') {
                    $empresa = Empresa::create([
                        'nombre' => $validatedData['empresa_nombre'],
                        'slug' => Str::slug($validatedData['empresa_nombre']),
                        'rubro' => $this->empresa_rubro,
                        'telefono_whatsapp' => $this->empresa_telefono_whatsapp,
                        'trial_ends_at' => now()->addDays(7),
                        'subscription_status' => 'trialing',
                    ]);
                    $empresaId = $empresa->id;
                }
            } elseif(!auth()->user()->hasRole('super_admin')) {
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

            // --- INICIO DEL CAMBIO: Lógica de suscripción mejorada --- //
            if ($this->isEditMode && auth()->user()->hasRole('super_admin') && $user->empresa) {
                // Si el estado de la suscripción ha cambiado
                if ($this->subscription_status !== $this->current_subscription_status) {
                    $updateData = ['subscription_status' => $this->subscription_status];

                    // Lógica de negocio para manejar la fecha de prueba según el nuevo estado
                    switch ($this->subscription_status) {
                        case 'trialing':
                            // Si se vuelve a poner en prueba, se asignan 7 días más.
                            $updateData['trial_ends_at'] = now()->addDays(7);
                            break;
                        case 'active':
                        case 'past_due':
                        case 'canceled':
                            // Para cualquier otro estado, el período de prueba ya no aplica.
                            $updateData['trial_ends_at'] = null;
                            break;
                    }

                    $user->empresa->update($updateData);
                }
            }
            // --- FIN DEL CAMBIO --- //

            DB::commit();
            
            $this->dispatch('alert', [
                'type' => 'success',
                'message' => 'Usuario ' . ($this->isEditMode ? 'actualizado' : 'creado') . ' correctamente.'
            ]);

            $this->closeModal();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Ocurrió un error al guardar: ' . $e->getMessage()
            ]);
        }
    }
    
    //--- ACCIONES DE CONFIRMACIÓN (ELIMINAR Y CAMBIAR ESTADO) ---//
    public function openConfirmModal($type, $userId)
    {
        if ($type === 'delete' && auth()->id() == $userId) {
             $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'No puedes eliminar tu propia cuenta de usuario.'
            ]);
            return;
        }

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
            $user = $this->userToDeleteOrToggle;

            if (auth()->id() === $user->id) {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'No puedes eliminar tu propia cuenta de usuario.']);
                $this->closeConfirmModal();
                return;
            }

            if ($user->hasRole('super_admin')) {
                if (User::role('super_admin')->count() <= 1) {
                    $this->dispatch('alert', ['type' => 'error', 'message' => 'No se puede eliminar al último superadministrador del sistema.']);
                    $this->closeConfirmModal();
                    return;
                }
            }

            if ($user->empresa && $user->hasRole('admin')) {
                $otherAdminsCount = User::where('empresa_id', $user->empresa_id)
                                        ->where('id', '!=', $user->id)
                                        ->role('admin')->count();

                if ($otherAdminsCount === 0) {
                    $this->dispatch('alert', ['type' => 'warning', 'message' => 'No se puede eliminar al último administrador de la empresa.']);
                    $this->closeConfirmModal();
                    return; 
                }
            }

            $user->delete();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Usuario eliminado correctamente.']);
            $this->closeConfirmModal();
        }
    }

    private function toggleStatus()
    {
        if ($this->userToDeleteOrToggle) {
            $user = $this->userToDeleteOrToggle;

            if (auth()->id() === $user->id) {
                $this->dispatch('alert', ['type' => 'error', 'message' => 'No puedes cambiar tu propio estado de actividad.']);
                $this->closeConfirmModal();
                return; 
            }
            
            $user->activo = !$user->activo;
            $user->save();

            $message = $user->activo ? "El usuario {$user->name} ha sido activado." : "El usuario {$user->name} ha sido desactivado y no podrá iniciar sesión.";
            $type = $user->activo ? 'success' : 'warning';

            $this->dispatch('alert', ['type' => $type, 'message' => $message]);
            $this->closeConfirmModal();
        }
    }

    public function buscarTodo()
    {
        $this->resultados = Empresa::all();
    }

    public function updatedSearchEmpresa($value)
    {
        $this->resultados = Empresa::where('nombre', 'like', "%{$value}%")->get();
    }

    
    //--- MÉTODO DE RENDERIZACIÓN ---//
    public function render()
    {

        $empresaPaginator = null;
        if ($this->showModal && !$this->isEditMode && !$this->empresa_id) {
            $queryRaw = $this->searchEmpresa; // sin trim
            $query = trim($queryRaw);

            if ($queryRaw === ' ') {
                // Si es exactamente un espacio: mostrar todas las empresas
                $empresaPaginator = Empresa::latest()
                    ->paginate(2, ['*'], 'empresaPage');
            } elseif ($query !== '') {
                // Si hay texto real: buscar por nombre
                $empresaPaginator = Empresa::where('nombre', 'like', '%' . $query . '%')
                    ->latest()
                    ->paginate(2, ['*'], 'empresaPage');
            } else {
                // Si está vacío y no es espacio: no mostrar nada
                $empresaPaginator = null;
            }
        }

        $query = User::with('roles', 'empresa');

        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('empresa_id', auth()->user()->empresa_id);
        }

        $searchTerm = trim($this->search);
        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $registros = $query->orderBy('id', 'asc')->paginate($this->perPage);
        
        if ($registros->isEmpty() && !empty($searchTerm)) {
            $this->dispatch('alert', [
                'type' => 'info',
                'message' => "No se encontraron resultados para '{$searchTerm}'."
            ]);
        }
        
        $roles = Role::query()->when(!auth()->user()->hasRole('super_admin'), function ($q) {
            $q->where('name', '!=', 'super_admin');
        })->get();
        
        //dd($roles->pluck('name')); // Muestra solo los nombres de los roles
        $empresas = auth()->user()->hasRole('super_admin') ? Empresa::all() : collect();

        return view('livewire.user-management', [
            'registros' => $registros,
            'roles' => $roles,
            'empresas' => $empresas,
            'empresaPaginator' => $empresaPaginator
        ]);
    }
}