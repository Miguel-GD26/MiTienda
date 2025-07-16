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

// Si estás usando un Facade específico para Cloudinary, impórtalo.
// Ejemplo: use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductoController extends Controller
{
    use AuthorizesRequests;

    /**
     * Muestra la lista de productos del panel de administración.
     */
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

    /**
     * Muestra el formulario para crear un nuevo producto.
     */
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
                return redirect()->route('usuarios.create')->with('warning', '¡Atención! Para crear un producto, primero debe registrar al menos una empresa.');
            }
        } else { // Si es Admin de Empresa
            if (!$user->empresa_id) {
                return redirect()->route('productos.index')->with('error', 'No tienes una empresa asignada para crear productos.');
            }
            $categorias = Categoria::where('empresa_id', $user->empresa_id)->orderBy('nombre')->get();
            
            if ($categorias->isEmpty()) {
                return redirect()->route('categorias.create')->with('warning', '¡Atención! Para crear un producto, primero debe registrar al menos una categoría.');
            }
        }
        
        return view('producto.action', compact('categorias', 'empresas'));
    }

    /**
     * Almacena un nuevo producto en la base de datos, incluyendo la imagen en Cloudinary.
     */
    public function store(Request $request)
    {
        $this->authorize('producto-create');
        $user = Auth::user();

        $empresa_id = $user->hasRole('super_admin') ? $request->empresa_id : $user->empresa_id;
        if ($user->hasRole('super_admin')) {
            $request->validate(['empresa_id' => 'required|exists:empresas,id']);
        }
        
        $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('productos')->where('empresa_id', $empresa_id)],
            'precio' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'categoria_id' => ['required', Rule::exists('categorias', 'id')->where('empresa_id', $empresa_id)],
            'imagen_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

    /**
     * Muestra el formulario para editar un producto.
     */
    public function edit(Producto $producto)
    {
        $this->authorize('producto-edit', $producto);
        $user = Auth::user();
        
        // Obtenemos las categorías que pertenecen a la misma empresa que el producto.
        $categorias = Categoria::where('empresa_id', $producto->empresa_id)->orderBy('nombre')->get();
        
        // Inicializamos la variable de empresas como una colección vacía.
        $empresas = collect();

        // SI el usuario es Super Admin, obtenemos TODAS las empresas para el select.
        if ($user->hasRole('super_admin')) {
            $empresas = Empresa::orderBy('nombre')->get();
        }

        // Ahora pasamos TODAS las variables necesarias a la vista.
        return view('producto.action', compact('producto', 'categorias', 'empresas'));
    }

    /**
     * Actualiza un producto existente, gestionando el cambio de imagen en Cloudinary.
     */
    public function update(Request $request, Producto $producto)
    {
        $this->authorize('producto-edit', $producto);
        $empresa_id = $producto->empresa_id;

        $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('productos')->where('empresa_id', $empresa_id)->ignore($producto->id)],
            'precio' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'categoria_id' => ['required', Rule::exists('categorias', 'id')->where('empresa_id', $empresa_id)],
            'imagen_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

        $producto->update($productData);

        return redirect()->route('productos.index')->with('mensaje', 'Producto actualizado con éxito.');
    }

    /**
     * Elimina un producto y su imagen asociada en Cloudinary.
     */

    public function destroy(Producto $producto)
    {
        $this->authorize('producto-delete', $producto);
        
        if ($producto->imagen_url) {
            cloudinary()->uploadApi()->destroy($producto->imagen_url);
        }

        $producto->delete();
        return redirect()->route('productos.index')->with('mensaje', 'Producto eliminado con éxito.');
    }

    // --- MÉTODOS PÚBLICOS (TIENDA) ---

    public function mostrarTienda(Empresa $empresa)
    {
        $productos = $empresa->productos()->with('categoria')->paginate(9);
        $categorias = $empresa->categorias()->whereHas('productos')->get();
        
        return view('tienda.index', [
            'tienda' => $empresa,
            'productos' => $productos,
            'categorias' => $categorias,
        ]);
    }

    public function filtrarPorCategoria(Empresa $empresa, Categoria $categoria)
    {
        if ($categoria->empresa_id !== $empresa->id) {
            abort(404);
        }
        
        $productos = $categoria->productos()->paginate(9);
        $categorias = $empresa->categorias()
            ->withCount('productos') 
            ->whereHas('productos')
            ->get();
        
        return view('tienda.index', [
            'tienda' => $empresa,
            'productos' => $productos,
            'categorias' => $categorias,
            'categoriaActual' => $categoria,
        ]);
    }

     public function buscarPublicoAjax(Request $request, Empresa $empresa)
    {
        // --- Lógica de consulta (sin cambios) ---
        $query = $empresa->productos()->with('categoria');
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        if ($request->filled('q')) {
            $query->where('nombre', 'like', '%' . $request->q . '%');
        }
        $productos = $query->paginate(12)->appends($request->query());

        // --- Lógica de categorías (ahora también se ejecuta aquí) ---
        $categoriasParaFiltro = $empresa->categorias()->whereHas('productos')->get();

        // --- Renderizar la vista parcial de productos ---
        $productsHtml = view('tienda.producto', [
            'productos' => $productos,
            'tienda' => $empresa
        ])->render();

        // --- Devolver una respuesta JSON con ambos datos ---
        return response()->json([
            'products_html' => $productsHtml,
            'categories' => $categoriasParaFiltro
        ]);
    }
}