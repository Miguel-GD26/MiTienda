<?php

namespace App\Livewire;

use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductManagement extends Component
{
    use WithPagination, WithFileUploads;

    // --- PROPIEDADES PARA FILTROS Y PAGINACIÓN ---
    public $search = '';
    protected $paginationTheme = 'bootstrap';
    public $empresa_id_filter, $empresaSearch = '', $selectedEmpresaName = '';
    public $categoria_id_filter, $categoriaSearch = '', $selectedCategoriaName = '';

    // --- PROPIEDADES DEL MODAL Y FORMULARIO ---
    public $showModal = false;
    public $productoId, $isEditMode = false;
    public $nombre, $precio, $precio_oferta, $stock, $descripcion, $categoria_id, $empresa_id;
    public $imagen_url, $new_imagen_url;

    // Propiedades para búsqueda en MODAL
    public $empresaSearchModal = '', $selectedEmpresaNameInModal = '';
    public $categoriaSearchModal = '', $selectedCategoriaNameInModal = '';

    // --- PROPIEDADES DEL MODAL DE CONFIRMACIÓN ---
    public $showConfirmModal = false;
    public $productToDelete;

    protected function rules()
    {
        $user = Auth::user();
        $empresaId = $user->hasRole('super_admin') ? $this->empresa_id : $user->empresa_id;
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('productos')->where('empresa_id', $empresaId)->ignore($this->productoId)
            ],
            'precio' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0|lt:precio',
            'stock' => 'required|integer|min:0',
            'descripcion' => 'nullable|string',
            'categoria_id' => ['required', Rule::exists('categorias', 'id')->where('empresa_id', $empresaId)],
            'new_imagen_url' => 'nullable|image|max:10240', // 10 MB
            'empresa_id' => [
                Rule::requiredIf(fn() => !$this->isEditMode && $user->hasRole('super_admin')),
                'nullable', 
                'exists:empresas,id'
            ],
        ];
    }

    // --- MÉTODOS DEL CICLO DE VIDA Y ACTUALIZACIÓN ---
    public function mount()
    {
        if ($this->empresa_id_filter) {
            $this->selectedEmpresaName = Empresa::find($this->empresa_id_filter)?->nombre ?? '';
        }
    }
    public function updated($propertyName)
    {
        if (!in_array($propertyName, ['new_imagen_url'])) {
            $this->validateOnly($propertyName);
        }
    }
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedEmpresaSearch()
    {
        $this->resetPage('empresaFilterPage');
    }
    public function updatedCategoriaSearch()
    {
        $this->resetPage('categoriaFilterPage');
    }
    public function updatedEmpresaSearchModal()
    {
        $this->resetPage('empresasForModalPage');
    }
    public function updatedCategoriaSearchModal()
    {
        $this->resetPage('categoriasForModalPage');
    }

    // --- MÉTODOS PARA FILTROS PRINCIPALES ---
    public function selectEmpresaFilter($id, $name)
    {
        $this->empresa_id_filter = $id;
        $this->selectedEmpresaName = $name;
        $this->empresaSearch = $name;
        $this->clearCategoriaFilter();
        $this->resetPage();
    }
    public function clearEmpresaFilter()
    {
        $this->reset(['empresa_id_filter', 'empresaSearch', 'selectedEmpresaName']);
        $this->clearCategoriaFilter();
        $this->resetPage();
    }
    public function listAllEmpresas()
    {
        $this->empresaSearch = ' ';
        $this->resetPage('empresaFilterPage');
    }
    public function selectCategoriaFilter($id, $name)
    {
        $this->categoria_id_filter = $id;
        $this->selectedCategoriaName = $name;
        $this->categoriaSearch = $name;
        $this->resetPage();
    }
    public function clearCategoriaFilter()
    {
        $this->reset(['categoria_id_filter', 'categoriaSearch', 'selectedCategoriaName']);
        $this->resetPage();
    }
    public function listAllCategorias()
    {
        $this->categoriaSearch = ' ';
        $this->resetPage('categoriaFilterPage');
    }

    // --- MÉTODOS DEL MODAL DE CREACIÓN/EDICIÓN ---
    public function openModal($productoId = null)
    {
        $this->resetInputFields();
        if ($productoId) {
            $producto = Producto::with('categoria', 'empresa')->findOrFail($productoId);
            $this->authorize('producto-edit', $producto);
            $this->isEditMode = true;
            $this->productoId = $producto->id;
            $this->nombre = $producto->nombre;
            $this->precio = $producto->precio;
            $this->precio_oferta = $producto->precio_oferta;
            $this->stock = $producto->stock;
            $this->descripcion = $producto->descripcion;
            $this->empresa_id = $producto->empresa_id;
            $this->categoria_id = $producto->categoria_id;
            $this->imagen_url = $producto->imagen_url;
            $this->selectedEmpresaNameInModal = $producto->empresa->nombre ?? '';
            $this->selectedCategoriaNameInModal = $producto->categoria->nombre ?? '';
        } else {
            $this->authorize('producto-create');
            $this->isEditMode = false;
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
            'productoId',
            'isEditMode',
            'nombre',
            'precio',
            'precio_oferta',
            'stock',
            'descripcion',
            'categoria_id',
            'empresa_id',
            'imagen_url',
            'new_imagen_url',
            'empresaSearchModal',
            'selectedEmpresaNameInModal',
            'categoriaSearchModal',
            'selectedCategoriaNameInModal'
        ]);
        $this->resetValidation();
        $this->resetPage('empresasForModalPage');
        $this->resetPage('categoriasForModalPage');
        $this->dispatch('image-reset');
    }

    // Métodos para la búsqueda de Empresa en el Modal
    public function clearSelectedEmpresa()
    {
        $this->reset(['empresa_id', 'empresaSearchModal', 'selectedEmpresaNameInModal']);
        $this->clearSelectedCategoria();
        $this->validateOnly('empresa_id');
    }
    public function selectEmpresa($empresaId, $empresaName)
    {
        $this->empresa_id = $empresaId;
        $this->selectedEmpresaNameInModal = $empresaName;
        $this->empresaSearchModal = '';
        $this->clearSelectedCategoria();
        $this->validateOnly('empresa_id');
    }
    public function listAllEmpresasForModal()
    {
        $this->empresaSearchModal = ' ';
        $this->resetPage('empresasForModalPage');
    }

    // Métodos para la búsqueda de Categoría en el Modal
    public function clearSelectedCategoria()
    {
        $this->reset(['categoria_id', 'categoriaSearchModal', 'selectedCategoriaNameInModal']);
        $this->validateOnly('categoria_id');
    }
    public function selectCategoria($categoriaId, $categoriaName)
    {
        $this->categoria_id = $categoriaId;
        $this->selectedCategoriaNameInModal = $categoriaName;
        $this->categoriaSearchModal = '';
        $this->validateOnly('categoria_id');
    }
    public function listAllCategoriasForModal()
    {
        $this->categoriaSearchModal = ' ';
        $this->resetPage('categoriasForModalPage');
    }

    public function saveProduct()
    {
        $user = Auth::user();
        if (!$user->hasRole('super_admin')) {
            $this->empresa_id = $user->empresa_id;
        }

        $productInstance = $this->isEditMode ? Producto::find($this->productoId) : null;
        $this->authorize($this->isEditMode ? 'producto-edit' : 'producto-create', $productInstance ?? Producto::class);

        $this->validate();

        DB::beginTransaction();
        try {
            $productData = [
                'nombre' => $this->nombre,
                'precio' => $this->precio,
                'precio_oferta' => $this->precio_oferta ?: null,
                'stock' => $this->stock,
                'descripcion' => $this->descripcion,
                'categoria_id' => $this->categoria_id,
                'empresa_id' => $this->empresa_id,
            ];

            if ($this->new_imagen_url) {
                $uploadedFile = cloudinary()->uploadApi()->upload(
    $this->new_imagen_url->getRealPath(),
    [
        'folder' => 'productos',
        'transformation' => [
    'width' => 400,
    'height' => 300,
    'crop' => 'pad',
    'background' => 'auto', // relleno neutro
    'quality' => 'auto',
    'fetch_format' => 'auto'
]
    ]
);
                $productData['imagen_url'] = $uploadedFile['public_id'];

                if ($this->isEditMode && $this->imagen_url) {
                    try {
                        cloudinary()->uploadApi()->destroy($this->imagen_url);
                    } catch (Exception $e) { /* Log error if needed */
                    }
                }
            }
            Producto::updateOrCreate(['id' => $this->productoId], $productData);
            DB::commit();

            $this->dispatch('alert', ['type' => 'success', 'message' => 'Producto guardado con éxito.']);
            $this->closeModal();
        } catch (Exception $e) {
            DB::rollBack();
            $errorMessage = config('app.debug') ? $e->getMessage() : 'Ocurrió un error inesperado.';
            $this->dispatch('alert', ['type' => 'error', 'message' => $errorMessage]);
        }
    }

    public function openConfirmModal($productoId)
    {
        $this->productToDelete = Producto::findOrFail($productoId);
        $this->authorize('producto-delete', $this->productToDelete);
        $this->showConfirmModal = true;
    }
    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->productToDelete = null;
    }
    public function deleteProduct()
    {
        if ($this->productToDelete) {
            $this->authorize('producto-delete', $this->productToDelete);
            DB::beginTransaction();
            try {
                if ($this->productToDelete->imagen_url) {
                    cloudinary()->uploadApi()->destroy($this->productToDelete->imagen_url);
                }
                $this->productToDelete->delete();
                DB::commit();
                $this->dispatch('alert', ['type' => 'success', 'message' => 'Producto eliminado con éxito.']);
            } catch (Exception $e) {
                DB::rollBack();
                $this->dispatch('alert', ['type' => 'error', 'message' => 'Error al eliminar: ' . $e->getMessage()]);
            } finally {
                $this->closeConfirmModal();
            }
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

        // Búsqueda para filtro de categorías

        $categorias_for_filter_paginator = null;
        if (!$this->selectedCategoriaName) {
            $queryRaw = $this->categoriaSearch;
            $queryTrimmed = trim($queryRaw);
            if ($queryRaw === ' ' || !empty($queryTrimmed)) {
                // Usamos una variable de query diferente para no sobreescribir la principal
                $categoriaQuery = Categoria::query();

                $empresaIdToFilter = auth()->user()->hasRole('super_admin') ? $this->empresa_id_filter : auth()->user()->empresa_id;

                if ($empresaIdToFilter) {
                    $categoriaQuery->where('empresa_id', $empresaIdToFilter);
                } else if (auth()->user()->hasRole('super_admin')) {
                    $categoriaQuery->whereRaw('1=0');
                }

                if ($queryRaw !== ' ') {
                    $categoriaQuery->where('nombre', 'like', '%' . $queryTrimmed . '%');
                }

                // Asegúrate de que el nombre del paginador está aquí
                $categorias_for_filter_paginator = $categoriaQuery->latest()->paginate(3, ['*'], 'categoriaFilterPage');
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

        // Búsqueda para MODAL de Categorías
        $categoriasForModal = null;
        if ($this->showModal && $this->empresa_id && !$this->selectedCategoriaNameInModal) {
            $queryRaw = $this->categoriaSearchModal;
            $queryTrimmed = trim($queryRaw);
            if ($queryRaw === ' ' || !empty($queryTrimmed)) {
                $query = Categoria::where('empresa_id', $this->empresa_id);
                if ($queryRaw !== ' ') {
                    $query->where('nombre', 'like', '%' . $queryTrimmed . '%');
                }
                $categoriasForModal = $query->latest()->paginate(3, ['*'], 'categoriasForModalPage');
            }
        }

        // Consulta principal de productos
        $query = Producto::with('categoria.empresa');
        if ($user->hasRole('super_admin')) {
            if ($this->empresa_id_filter) {
                $query->where('empresa_id', $this->empresa_id_filter);
            }
        } else {
            $query->where('empresa_id', $user->empresa_id);
        }
        if ($this->categoria_id_filter) {
            $query->where('categoria_id', $this->categoria_id_filter);
        }
        if (trim($this->search)) {
            $query->where('nombre', 'like', '%' . trim($this->search) . '%');
        }

        $productos = $query->orderBy('id', 'desc')->paginate(10);

        return view('livewire.product-management', [
            'productos' => $productos,
            'empresas_for_filter_paginator' => $empresas_for_filter_paginator,
            'categorias_for_filter_paginator' => $categorias_for_filter_paginator,
            'empresasForModal' => $empresasForModal,
            'categoriasForModal' => $categoriasForModal,
        ]);
    }
}
