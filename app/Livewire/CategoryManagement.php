<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Exception;

class CategoryManagement extends Component
{
    use WithPagination;

    // --- PROPIEDADES PARA FILTROS Y PAGINACIÓN ---
    public $search = ''; // Búsqueda principal por nombre de categoría
    public $empresa_id_filter;
    public $empresaSearch = '';
    public $selectedEmpresaName = '';
    protected $paginationTheme = 'bootstrap';

    // --- PROPIEDADES DEL MODAL Y FORMULARIO ---
    public $showModal = false;
    public $categoryId;
    public $isEditMode = false;
    public $nombre, $descripcion, $empresa_id;
    public $empresaSearchModal = '', $selectedEmpresaNameInModal = '';

    // --- PROPIEDADES DEL MODAL DE CONFIRMACIÓN ---
    public $showConfirmModal = false;
    public $categoryToDelete;

    protected function rules()
    {
        $user = Auth::user();
        $empresaId = $user->hasRole('super_admin') ? $this->empresa_id : $user->empresa_id;

        $rules = [
            'nombre' => ['required', 'string', 'max:50', Rule::unique('categorias')->where('empresa_id', $empresaId)->ignore($this->categoryId)],
            'descripcion' => 'nullable|string|max:255',
        ];

        if ($user->hasRole('super_admin')) {
            $rules['empresa_id'] = 'required|exists:empresas,id';
        }
        return $rules;
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.unique' => 'Ya existe una categoría con este nombre en la empresa.',
        'empresa_id.required' => 'Debe seleccionar una empresa.',
    ];

    // --- MÉTODOS DEL CICLO DE VIDA Y ACTUALIZACIÓN ---
    public function updated($propertyName) { $this->validateOnly($propertyName); }
    public function updatedSearch() { $this->resetPage(); }
    public function updatedEmpresaSearch() { $this->resetPage('empresaFilterPage'); }
    public function updatedEmpresaSearchModal() { $this->resetPage('empresasForModalPage'); }

    // --- MÉTODOS PARA FILTROS PRINCIPALES ---
    public function selectEmpresaFilter($id, $name)
    {
        $this->empresa_id_filter = $id;
        $this->selectedEmpresaName = $name;
        $this->empresaSearch = $name;
        $this->resetPage();
    }
    public function clearEmpresaFilter()
    {
        $this->reset(['empresa_id_filter', 'empresaSearch', 'selectedEmpresaName']);
        $this->resetPage();
    }
    public function listAllEmpresas()
    {
        $this->empresaSearch = ' ';
        $this->resetPage('empresaFilterPage');
    }

    // --- MÉTODOS DEL MODAL Y CRUD ---
    public function openModal($categoryId = null)
    {
        $this->resetInputFields();
        if ($categoryId) {
            $categoria = Categoria::with('empresa')->findOrFail($categoryId);
            $this->authorize('categoria-edit', $categoria);
            $this->isEditMode = true;
            $this->categoryId = $categoria->id;
            $this->fill($categoria);
            $this->selectedEmpresaNameInModal = $categoria->empresa->nombre ?? '';
        } else {
            $this->authorize('categoria-create');
            if (!Auth::user()->hasRole('super_admin')) {
                $this->empresa_id = Auth::user()->empresa_id;
                $this->selectedEmpresaNameInModal = Auth::user()->empresa->nombre;
            }
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
        $this->reset([
            'categoryId', 'isEditMode', 'nombre', 'descripcion', 'empresa_id',
            'empresaSearchModal', 'selectedEmpresaNameInModal'
        ]);
        $this->resetValidation();
        $this->resetPage('empresasForModalPage');
    }

    // Métodos para la búsqueda de Empresa en el Modal
    public function selectEmpresaInModal($empresaId, $empresaName)
    {
        $this->empresa_id = $empresaId;
        $this->selectedEmpresaNameInModal = $empresaName;
        $this->empresaSearchModal = '';
        $this->validateOnly('empresa_id');
    }
    public function clearSelectedEmpresaInModal()
    {
        $this->reset(['empresa_id', 'empresaSearchModal', 'selectedEmpresaNameInModal']);
        $this->validateOnly('empresa_id');
    }
    public function listAllEmpresasForModal()
    {
        $this->empresaSearchModal = ' ';
        $this->resetPage('empresasForModalPage');
    }

    public function saveCategory()
    {
        if (!Auth::user()->hasRole('super_admin')) {
            $this->empresa_id = Auth::user()->empresa_id;
        }

        $categoryInstance = $this->isEditMode ? Categoria::find($this->categoryId) : null;
        $this->authorize($this->isEditMode ? 'categoria-edit' : 'categoria-create', $categoryInstance ?? Categoria::class);

        $validatedData = $this->validate();

        try {
            Categoria::updateOrCreate(['id' => $this->categoryId], $validatedData);
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Categoría guardada con éxito.']);
            $this->closeModal();
        } catch (Exception $e) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Ocurrió un error al guardar la categoría.']);
        }
    }

    public function openConfirmModal($categoryId)
    {
        $this->categoryToDelete = Categoria::withCount('productos')->findOrFail($categoryId);
        $this->authorize('categoria-delete', $this->categoryToDelete);
        $this->showConfirmModal = true;
    }
    
    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->categoryToDelete = null;
    }

    public function deleteCategory()
    {
        if ($this->categoryToDelete) {
            $this->authorize('categoria-delete', $this->categoryToDelete);
            
            if ($this->categoryToDelete->productos_count > 0) {
                $this->dispatch('alert', ['type' => 'warning', 'message' => 'No se puede eliminar, tiene productos asociados.']);
                $this->closeConfirmModal();
                return;
            }
            $this->categoryToDelete->delete();
            $this->dispatch('alert', ['type' => 'success', 'message' => 'Categoría eliminada con éxito.']);
            $this->closeConfirmModal();
        }
    }
    
    // --- MÉTODO DE RENDERIZACIÓN ---
    public function render()
    {
        $user = Auth::user();

        // Búsqueda para filtro de empresas
        $empresas_for_filter_paginator = null;
        if ($user->hasRole('super_admin') && !$this->selectedEmpresaName) {
            $queryRaw = $this->empresaSearch;
            $queryTrimmed = trim($queryRaw);
            if ($queryRaw === ' ' || !empty($queryTrimmed)) {
                $query = Empresa::query();
                if ($queryRaw !== ' ') {
                    $query->where('nombre', 'like', '%' . $queryTrimmed . '%');
                }
                $empresas_for_filter_paginator = $query->latest()->paginate(3, ['*'], 'empresaFilterPage');
            }
        }

        // Búsqueda para MODAL de Empresas
        $empresasForModal = null;
        if ($this->showModal && !$this->isEditMode && $user->hasRole('super_admin') && !$this->selectedEmpresaNameInModal) {
            $queryRaw = $this->empresaSearchModal;
            $queryTrimmed = trim($queryRaw);
            if ($queryRaw === ' ' || !empty($queryTrimmed)) {
                $query = Empresa::query();
                if ($queryRaw !== ' ') {
                    $query->where('nombre', 'like', '%' . $queryTrimmed . '%');
                }
                $empresasForModal = $query->latest()->paginate(3, ['*'], 'empresasForModalPage');
            }
        }

        // Consulta principal de categorías
        $query = Categoria::with('empresa');
        if ($user->hasRole('super_admin')) {
            if ($this->empresa_id_filter) {
                $query->where('empresa_id', $this->empresa_id_filter);
            }
        } else {
            $query->where('empresa_id', $user->empresa_id);
        }
        if (trim($this->search)) {
            $query->where('nombre', 'like', '%' . trim($this->search) . '%');
        }

        $registros = $query->orderBy('id', 'desc')->paginate(10);

        return view('livewire.category-management', [
            'registros' => $registros,
            'empresas_for_filter_paginator' => $empresas_for_filter_paginator,
            'empresasForModal' => $empresasForModal,
        ]);
    }
}