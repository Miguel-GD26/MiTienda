<?php

namespace App\Livewire;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Storefront extends Component
{
    use WithPagination;

    public Empresa $tienda;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    // Propiedades para el filtro interactivo de categoría
    public $categoria_id_filter;
    public $categoriaSearch = '';
    public $selectedCategoriaName = '';

    // --- PROPIEDADES PARA EL MODAL DE DETALLE DE PRODUCTO ---
    public $showProductModal = false;
    public ?Producto $selectedProduct = null;


    // Sincroniza las propiedades con la URL
    protected $queryString = [
        'search' => ['except' => ''],
        'categoria_id_filter' => ['except' => '', 'as' => 'categoria'],
    ];

    public function mount(Empresa $empresa)
    {
        $this->tienda = $empresa;
        if ($this->categoria_id_filter) {
            $this->selectedCategoriaName = Categoria::find($this->categoria_id_filter)?->nombre ?? '';
        }
    }

    public function updating($name, $value)
    {
        if (in_array($name, ['search', 'categoria_id_filter', 'categoriaSearch'])) {
            $this->resetPage();
        }
        if ($name === 'categoriaSearch') {
            $this->resetPage('categoriaFilterPage');
        }
    }

    // --- MÉTODOS PARA EL FILTRO DE CATEGORÍA ---
    public function selectCategoriaFilter($id, $name)
    {
        $this->categoria_id_filter = $id;
        $this->selectedCategoriaName = $name;
        $this->categoriaSearch = $name;
    }

    public function clearCategoriaFilter()
    {
        $this->reset(['categoria_id_filter', 'categoriaSearch', 'selectedCategoriaName']);
    }

    public function listAllCategorias()
    {
        $this->categoriaSearch = ' ';
        $this->resetPage('categoriaFilterPage');
    }

    // --- MÉTODOS PARA EL MODAL DE DETALLE DE PRODUCTO ---
    public function openProductModal($productId)
    {
        // Cargamos solo los datos necesarios para evitar objetos pesados
        $this->selectedProduct = Producto::find($productId);
        if ($this->selectedProduct) {
            $this->showProductModal = true;
        }
    }

    public function closeProductModal()
    {
        $this->showProductModal = false;
        $this->selectedProduct = null;
    }

    public function render()
    {
        // Búsqueda de categorías para el dropdown del filtro
        $categorias_for_filter_paginator = null;
        if (!$this->selectedCategoriaName) {
            $queryRaw = $this->categoriaSearch;
            $queryTrimmed = trim($queryRaw);
            if ($queryRaw === ' ' || !empty($queryTrimmed)) {
                $query = $this->tienda->categorias()->whereHas('productos');
                if ($queryRaw !== ' ') {
                    $query->where('nombre', 'like', '%' . $queryTrimmed . '%');
                }
                $categorias_for_filter_paginator = $query->latest()->paginate(2, ['*'], 'categoriaFilterPage');
            }
        }

        // Consulta principal de productos
        $productosQuery = $this->tienda->productos()->with('categoria');

        if ($this->categoria_id_filter) {
            $productosQuery->where('categoria_id', $this->categoria_id_filter);
        }
        if (!empty(trim($this->search))) {
            $productosQuery->where('nombre', 'like', '%' . trim($this->search) . '%');
        }

        $productos = $productosQuery->paginate(12);
        $cartItems = $this->getCartItems();

        return view('livewire.storefront', [
            'productos' => $productos,
            'cartItems' => $cartItems,
            'categorias_for_filter_paginator' => $categorias_for_filter_paginator,
        ]);
    }

    private function getCartItems()
    {
        $user = Auth::user();
        if ($user && $user->cart) {
            return $user->cart->items->keyBy('producto_id');
        }
        return collect();
    }

    public function addToCart($productoId, $quantity = 1)
    {
        if (!Auth::check() || !Auth::user()->hasRole('cliente')) {
            return redirect()->route('login');
        }
        
        $producto = Producto::find($productoId);
        if (!$producto) { return; }

        $user = Auth::user();
        $cart = $user->cart()->firstOrCreate();

        $firstItem = $cart->items()->with('producto')->first();
        if ($firstItem && $firstItem->producto->empresa_id !== $producto->empresa_id) {
            $this->dispatch('alert', ['type' => 'error', 'message' => 'Solo puedes comprar en una tienda a la vez.']);
            return;
        }

        $currentQuantityInCart = $cart->items()->where('producto_id', $producto->id)->sum('cantidad');
        if (($currentQuantityInCart + $quantity) > $producto->stock) {
            $this->dispatch('alert', ['type' => 'error', 'message' => "Stock insuficiente. No puedes añadir más de {$producto->stock} unidades."]);
            return;
        }

        $cart->items()->create([
            'producto_id' => $producto->id,
            'cantidad' => $quantity,
        ]);

        $this->dispatch('cartUpdated');
        $this->dispatch('alert', ['type' => 'success', 'message' => '"'.$producto->nombre.'" añadido al carrito.']);
    }

    public function updateCartItem($productoId, $newQuantity)
    {
        if (!Auth::check() || !Auth::user()->hasRole('cliente')) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        if (!$cart = $user->cart) { return; }

        $cartItem = $cart->items()->where('producto_id', $productoId)->first();
        if (!$cartItem) { return; }

        $newQuantity = (int)$newQuantity;
        $producto = Producto::find($productoId);
        
        if ($newQuantity > $producto->stock) {
            $this->dispatch('alert', ['type' => 'error', 'message' => "Stock insuficiente. Solo quedan {$producto->stock}."]);
            return;
        }

        if ($newQuantity <= 0) {
            $cartItem->delete();
            $this->dispatch('alert', ['type' => 'info', 'message' => 'Producto eliminado del carrito.']);
        } else {
            $cartItem->update(['cantidad' => $newQuantity]);
        }
        
        $this->dispatch('cartUpdated');
    }
}