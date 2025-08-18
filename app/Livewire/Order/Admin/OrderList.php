<?php

namespace App\Livewire\Order\Admin;

use App\Models\Empresa;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Propiedades para los filtros
    public $estado;
    public $cliente_nombre;

    // --- PROPIEDADES AÑADIDAS PARA EL BUSCADOR DE EMPRESA ---
    public $empresa_id; // Este reemplaza al $empresa_id_filter para ser consistente
    public $empresaSearch = '';
    public $selectedEmpresaName = '';

    public function updating($property) { $this->resetPage(); }
    public function updatedEmpresaSearch() { $this->resetPage('empresaFilterPage'); }

    // --- MÉTODOS AÑADIDOS PARA EL BUSCADOR DE EMPRESA ---
    public function selectEmpresaFilter($id, $name)
    {
        $this->empresa_id = $id;
        $this->selectedEmpresaName = $name;
        $this->empresaSearch = $name;
    }

    public function clearEmpresaFilter()
    {
        $this->reset(['empresa_id', 'empresaSearch', 'selectedEmpresaName']);
    }

    public function listAllEmpresas()
    {
        $this->empresaSearch = ' ';
    }
    
    public function render()
    {
        $user = Auth::user();

        // --- Búsqueda para filtro de empresas (lógica copiada de tu ProductManagement) ---
        $empresas_for_filter_paginator = null;
        if ($user->hasRole('super_admin') && !$this->selectedEmpresaName) {
            $queryRaw = $this->empresaSearch;
            $queryTrimmed = trim($queryRaw);
            if ($queryRaw === ' ' || !empty($queryTrimmed)) {
                $query = Empresa::query();
                if ($queryRaw !== ' ') {
                    $query->where('nombre', 'like', '%' . $queryTrimmed . '%');
                }
                $empresas_for_filter_paginator = $query->latest()->paginate(5, ['*'], 'empresaFilterPage');
            }
        }

        // --- Consulta principal de pedidos ---
        $query = Pedido::with(['cliente', 'empresa'])->latest();

        if ($user->hasRole('admin')) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($user->hasRole('super_admin')) {
            // Se usa la nueva propiedad $empresa_id
            if ($this->empresa_id) {
                $query->where('empresa_id', $this->empresa_id);
            }
        }
        if ($this->estado) $query->where('estado', $this->estado);
        if (trim($this->cliente_nombre)) {
            $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', '%' . trim($this->cliente_nombre) . '%'));
        }
        
        $pedidos = (auth()->user()->hasRole('super_admin') && !$this->empresa_id)
            ? Pedido::where('id', -1)->paginate(15) 
            : $query->paginate(15);
        
        $estados = ['pendiente', 'atendido', 'enviado', 'entregado', 'cancelado'];

        return view('livewire.order.admin.order-list', compact('pedidos', 'estados', 'empresas_for_filter_paginator'));
    }
}