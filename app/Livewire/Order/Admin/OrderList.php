<?php

namespace App\Livewire\Order\Admin;

use App\Models\Empresa;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url; // <-- Ya lo tenías, ¡genial!

class OrderList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Propiedades para los filtros
    
    // ***** CAMBIO 1: APLICAR EL ATRIBUTO #[Url] *****
    // Sincroniza esta propiedad con el parámetro 'status' de la URL.
    // 'keep: true' hace que el parámetro persista en la URL incluso si se vacía.
    #[Url(as: 'status', keep: true)]
    public $estado = ''; 
    
    public $cliente_nombre;

    // Propiedades para el buscador de empresa
    public $empresa_id;
    public $empresaSearch = '';
    public $selectedEmpresaName = '';

    // El método `updating` es más eficiente que `updated` para resetear la página.
    public function updating($property, $value)
    {
        // Resetea la página si CUALQUIER propiedad de filtro cambia.
        if (in_array($property, ['estado', 'cliente_nombre', 'empresa_id'])) {
            $this->resetPage();
        }
    }

    public function updatedEmpresaSearch()
    {
        $this->resetPage('empresaFilterPage');
    }

    // Métodos para el buscador de empresa
    public function selectEmpresaFilter($id, $name)
    {
        $this->empresa_id = $id;
        $this->selectedEmpresaName = $name;
        $this->empresaSearch = $name;
        $this->resetPage(); // Resetea la paginación principal al seleccionar empresa
    }

    public function clearEmpresaFilter()
    {
        $this->reset(['empresa_id', 'empresaSearch', 'selectedEmpresaName']);
        $this->resetPage();
    }

    public function listAllEmpresas()
    {
        $this->empresaSearch = ' ';
        $this->resetPage('empresaFilterPage');
    }
    
    public function render()
    {
        $user = Auth::user();

        // Búsqueda para filtro de empresas
        $empresas_for_filter_paginator = null;
        if ($user->hasRole('super_admin') && !$this->selectedEmpresaName) {
            $queryRaw = $this->empresaSearch;
            $queryTrimmed = trim($queryRaw);
            if ($queryRaw === ' ' || !empty($queryTrimmed)) {
                $empresaQuery = Empresa::query();
                if ($queryRaw !== ' ') {
                    $empresaQuery->where('nombre', 'like', '%' . $queryTrimmed . '%');
                }
                $empresas_for_filter_paginator = $empresaQuery->latest()->paginate(5, ['*'], 'empresaFilterPage');
            }
        }

        // --- Consulta principal de pedidos ---
        $query = Pedido::with(['cliente.user', 'empresa'])->latest();

        // Aplicar filtros basados en rol y selecciones
        if ($user->hasRole('admin') || $user->hasRole('vendedor')) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($user->hasRole('super_admin')) {
            if ($this->empresa_id) {
                $query->where('empresa_id', $this->empresa_id);
            }
        }
        
        if ($this->estado) {
            $query->where('estado', $this->estado);
        }

        if (trim($this->cliente_nombre)) {
            // Asumiendo que el nombre está en la tabla users relacionada con cliente
            $query->whereHas('cliente.user', fn($q) => 
                $q->where('name', 'like', '%' . trim($this->cliente_nombre) . '%')
            );
        }
        
        // ***** CAMBIO 2: LÓGICA DE PAGINACIÓN MEJORADA *****
        // Ahora, si un super admin llega sin filtros, no se mostrará nada por defecto.
        // Pero si llega con un filtro (como el de estado), la consulta SÍ se ejecutará.
        $hasActiveFilter = $this->empresa_id || $this->estado || trim($this->cliente_nombre);
        
        if ($user->hasRole('super_admin') && !$hasActiveFilter) {
            // Si es super admin y no hay ningún filtro, mostramos una lista vacía para no cargar todo.
            $pedidos = Pedido::where('id', -1)->paginate(15);
        } else {
            $pedidos = $query->paginate(15);
        }
        
        $estados = ['pendiente', 'atendido', 'enviado', 'entregado', 'cancelado'];

        return view('livewire.order.admin.order-list', compact('pedidos', 'estados', 'empresas_for_filter_paginator'));
    }
}