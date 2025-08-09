<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductoController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('producto-list');
        $user = Auth::user();
           
        $query = Producto::with('categoria.empresa');

        if ($user->hasRole('super_admin')) {
            if ($request->filled('empresa_id')) {
                $query->where('empresa_id', $request->empresa_id);
            }
        } else {
            $query->where('empresa_id', $user->empresa_id);
        }
        
        if ($request->filled('texto')) {
            $query->where('nombre', 'like', '%' . $request->texto . '%');
        }

        $productos = $query->orderBy('id', 'desc')->paginate(10);
        $empresas = $user->hasRole('super_admin') ? Empresa::orderBy('nombre')->get() : collect();

        return view('producto.index', compact('productos', 'empresas'));
    }

    public function create()
    {
        $this->authorize('producto-create');
        $user = Auth::user();

        $categorias = collect();
        $empresas = collect();

        if ($user->hasRole('super_admin')) {
            $empresas = Empresa::orderBy('nombre')->get();
            $categorias = Categoria::with('empresa')->orderBy('nombre')->get();

            if ($empresas->isEmpty()) {
                return redirect()->route('usuarios.create')
                    ->with('warning', '¡Atención! Para crear un producto, primero debe registrar al menos una empresa.');
            }
        } else {
            if (!$user->empresa_id) {
                return redirect()->route('productos.index')
                    ->with('error', 'No tienes una empresa asignada para crear productos.');
            }

            $categorias = Categoria::where('empresa_id', $user->empresa_id)
                ->orderBy('nombre')->get();

            if ($categorias->isEmpty()) {
                return redirect()->route('categorias.create')
                    ->with('warning', '¡Atención! Para crear un producto, primero debe registrar al menos una categoría.');
            }
        }

        return view('producto.action', compact('categorias', 'empresas'));
    }


    public function store(Request $request)
    {
        $this->authorize('producto-create');
        $user = Auth::user();

        $empresa_id = $user->hasRole('super_admin') ? $request->empresa_id : $user->empresa_id;

        if ($user->hasRole('super_admin')) {
            $request->validate([
                'empresa_id' => 'required|exists:empresas,id'
            ], [
                'empresa_id.required' => 'Debe seleccionar una empresa.',
                'empresa_id.exists' => 'La empresa seleccionada no existe.',
            ]);
        }
        
        if ($request->input('precio_oferta') === '') {
            $request->merge(['precio_oferta' => null]);
        }

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('productos')->where(fn ($query) => $query->where('empresa_id', $empresa_id)),
            ],
            'precio' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0|lt:precio',
            'stock' => 'nullable|integer|min:0',
            'categoria_id' => [
                'required',
                Rule::exists('categorias', 'id')->where('empresa_id', $empresa_id),
            ],
            'imagen_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.unique' => 'Ya existe un producto con este nombre en la empresa.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio_oferta.numeric' => 'El precio de oferta debe ser un número.',
            'precio_oferta.lt' => 'El precio de oferta debe ser menor que el precio normal.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'categoria_id.required' => 'Debe seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría no es válida o no pertenece a esta empresa.',
            'imagen_url.image' => 'El archivo debe ser una imagen.',
            'imagen_url.mimes' => 'Solo se permiten imágenes JPEG, PNG, JPG, GIF o WEBP.',
            'imagen_url.max' => 'La imagen no debe superar los 2MB.',
        ]);

        $productData = $request->except('imagen_url');
        $productData['empresa_id'] = $empresa_id;

        if ($request->hasFile('imagen_url')) {
            $uploadedFile = cloudinary()->uploadApi()->upload($request->file('imagen_url')->getRealPath(), [
                'folder' => 'productos'
            ]);
            $productData['imagen_url'] = $uploadedFile['public_id'];
        }

        Producto::create($productData);

        return redirect()->route('productos.index')->with('mensaje', 'Producto creado con éxito.');
    }

    public function edit(Producto $producto)
    {
        $this->authorize('producto-edit', $producto);
        $user = Auth::user();
        
        $categorias = Categoria::where('empresa_id', $producto->empresa_id)->orderBy('nombre')->get();
        $empresas = collect();

        if ($user->hasRole('super_admin')) {
            $empresas = Empresa::orderBy('nombre')->get();
        }

        return view('producto.action', compact('producto', 'categorias', 'empresas'));
    }


    public function update(Request $request, Producto $producto)
    {
        $this->authorize('producto-edit', $producto);
        $empresa_id = $producto->empresa_id;
        
        if ($request->input('precio_oferta') === '') {
            $request->merge(['precio_oferta' => null]);
        }

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('productos')->where(fn ($query) => $query->where('empresa_id', $empresa_id))->ignore($producto->id),
            ],
            'precio' => 'required|numeric|min:0',
            'precio_oferta' => 'nullable|numeric|min:0|lt:precio',
            'stock' => 'required|integer|min:0',
            'categoria_id' => [
                'required',
                Rule::exists('categorias', 'id')->where('empresa_id', $empresa_id),
            ],
            'imagen_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.unique' => 'Ya existe otro producto con ese nombre en la empresa.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio_oferta.numeric' => 'El precio de oferta debe ser un número.',
            'precio_oferta.lt' => 'El precio de oferta debe ser menor que el precio normal.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'categoria_id.required' => 'Debe seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría no es válida o no pertenece a esta empresa.',
            'imagen_url.image' => 'El archivo debe ser una imagen.',
            'imagen_url.mimes' => 'Solo se permiten imágenes JPEG, PNG, JPG, GIF o WEBP.',
            'imagen_url.max' => 'La imagen no debe superar los 2MB.',
        ]);

        $productData = $request->except('imagen_url');

        if ($request->hasFile('imagen_url')) {
            if ($producto->imagen_url) {
                cloudinary()->uploadApi()->destroy($producto->imagen_url);
            }

            $uploadedFile = cloudinary()->uploadApi()->upload($request->file('imagen_url')->getRealPath(), [
                'folder' => 'productos'
            ]);
            $productData['imagen_url'] = $uploadedFile['public_id'];
        }
        
        if (!isset($productData['precio_oferta'])) {
            $productData['precio_oferta'] = null;
        }

        $producto->update($productData);

        return redirect()->route('productos.index')->with('mensaje', 'Producto actualizado con éxito.');
    }


    public function destroy(Producto $producto)
    {
        $this->authorize('producto-delete', $producto);
        
        if ($producto->imagen_url) {
            cloudinary()->uploadApi()->destroy($producto->imagen_url);
        }

        $producto->delete();
        return redirect()->route('productos.index')->with('mensaje', 'Producto eliminado con éxito.');
    }

    private function getCartItemsForView()
    {
        $user = Auth::user();

        if ($user && $user->cart) {
            return $user->cart->items->keyBy('producto_id');
        }

        return collect();
    }

     public function mostrarTienda(Empresa $empresa)
    {
        $productos = $empresa->productos()->with('categoria')->paginate(8);
        $categorias = $empresa->categorias()->whereHas('productos')->get();
        
        $cartItems = $this->getCartItemsForView();

        return view('tienda.index', [
            'tienda' => $empresa,
            'productos' => $productos,
            'categorias' => $categorias,
            'cartItems' => $cartItems,
        ]);
    }

    public function filtrarPorCategoria(Empresa $empresa, Categoria $categoria)
    {
        if ($categoria->empresa_id !== $empresa->id) {
            abort(404);
        }
        
        $productos = $categoria->productos()->paginate(8);
        $categorias = $empresa->categorias()->withCount('productos')->whereHas('productos')->get();
        
        $cartItems = $this->getCartItemsForView();

        return view('tienda.index', [
            'tienda' => $empresa,
            'productos' => $productos,
            'categorias' => $categorias,
            'categoriaActual' => $categoria,
            'cartItems' => $cartItems, 
        ]);
    }

// En tu ProductoController.php

public function buscarPublicoAjax(Request $request, Empresa $empresa)
{
    $query = $empresa->productos()->with('categoria');

    if ($request->filled('categoria_id')) {
        $query->where('categoria_id', $request->categoria_id);
    }
    if ($request->filled('q')) {
        $query->where('nombre', 'like', '%' . $request->q . '%');
    }

    // Pagina los resultados
    $productos = $query->paginate(8);

    // --- LÍNEAS CLAVE DE LA SOLUCIÓN ---
    // 1. Le decimos al paginador qué ruta usar, pasando los parámetros necesarios (el slug de la empresa).
    $productos->withPath(route('tienda.productos.buscar_ajax', $empresa));
    
    // 2. Le decimos que conserve los parámetros de la búsqueda actual (q, categoria_id).
    $productos->appends($request->query());
    // --- FIN DE LA SOLUCIÓN ---

    $cartItems = $this->getCartItemsForView();

    // Asegúrate de que el nombre del parcial es 'tienda.producto'
    $productsHtml = view('tienda.producto', [
        'productos' => $productos,
        'tienda' => $empresa,
        'cartItems' => $cartItems,
    ])->render();

    $categoriasParaFiltro = $empresa->categorias()->whereHas('productos')->get();

    return response()->json([
        'products_html' => $productsHtml,
        'categories' => $categoriasParaFiltro
    ]);
}
}