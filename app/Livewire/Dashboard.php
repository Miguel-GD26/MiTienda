<?php

namespace App\Livewire;

use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    // --- PROPIEDADES PÚBLICAS PARA LA VISTA ---
    public $view_type;
    public $kpi = [];
    public $totalPedidos = 0;
    public $ingresosTotales = 0;
    public $totalEmpresas = 0;
    public $pedidosRecientes = [];
    public $statusBar = [];
    public $sparkline = ['labels' => [], 'series' => []];
    public $totalProductos = 0;
    public $totalCategorias = 0;
    public $totalClientes = 0;
    public $ultimasEmpresas = [];
    public $ultimosClientes = [];
    public $pedidosParaEntregar = [];
    public $pedidosEntregadosHoy = 0;
    public $topProductos = [];
    public $topCategorias = [];
    
    // --- NUEVAS PROPIEDADES PARA WIDGETS DE ACCIÓN ---
    public $pedidosAntiguos = [];
    public $productosBajoStock = [];

    private $adminStatuses = ['pendiente', 'atendido', 'enviado', 'entregado', 'cancelado'];

    public function mount()
    {
        $user = Auth::user();
        $data = [];

        if ($user->hasRole('super_admin')) {
            $data = $this->getSuperAdminData();
        } elseif ($user->hasRole('admin') || $user->hasRole('vendedor')) {
            $data = $this->getAdminData($user->empresa);
        } elseif ($user->hasRole('repartidor')) {
            $data = $this->getRepartidorData($user);
        } else {
            $this->view_type = 'guest'; 
            return;
        }

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
    
    private function getSuperAdminData()
    {
        $statusCounts = Pedido::whereIn('estado', $this->adminStatuses)
            ->select('estado', DB::raw('count(*) as total'))->groupBy('estado')->pluck('total', 'estado')->all();
        foreach ($this->adminStatuses as $status) { if (!isset($statusCounts[$status])) $statusCounts[$status] = 0; }
        
        // Excluimos los cancelados para el cálculo del total de la barra de progreso
        $totalRelevantPedidos = array_sum(array_diff_key($statusCounts, array_flip(['cancelado'])));
        $statusBarData = [];
        if ($totalRelevantPedidos > 0) {
            foreach ($statusCounts as $status => $count) {
                if ($status != 'cancelado') {
                    $statusBarData[$status] = round(($count / $totalRelevantPedidos) * 100, 2);
                }
            }
        }
        
        return [
            'view_type' => 'super_admin',
            'kpi' => $statusCounts,
            'totalPedidos' => Pedido::count(),
            'ingresosTotales' => Pedido::where('estado', 'entregado')->sum('total'),
            'totalEmpresas' => Empresa::count(),
            'pedidosRecientes' => Pedido::with('empresa', 'cliente.user')->latest()->take(5)->get(),
            'statusBar' => $statusBarData,
            'sparkline' => $this->getSparklineData(),
            'totalProductos' => Producto::count(),
            'totalCategorias' => Categoria::count(),
            'ultimasEmpresas' => Empresa::latest()->take(5)->get(),
            'ultimosClientes' => User::role('cliente')->latest()->take(5)->get(),
            'topProductos' => $this->getTopProductos(),
            'topCategorias' => $this->getTopCategorias(),
            'pedidosAntiguos' => $this->getOldestPendingOrders(),
            'productosBajoStock' => $this->getLowStockProducts(),
        ];
    }
    
    private function getAdminData($empresa)
    {
        if (!$empresa) {
            return ['view_type' => 'no_empresa']; 
        }

        $statusCounts = $empresa->pedidos()->whereIn('estado', $this->adminStatuses)
            ->select('estado', DB::raw('count(*) as total'))->groupBy('estado')->pluck('total', 'estado')->all();
        foreach ($this->adminStatuses as $status) { if (!isset($statusCounts[$status])) $statusCounts[$status] = 0; }
        
        $totalRelevantPedidos = array_sum(array_diff_key($statusCounts, array_flip(['cancelado'])));
        $statusBarData = [];
        if ($totalRelevantPedidos > 0) {
            foreach ($statusCounts as $status => $count) {
                if ($status != 'cancelado') {
                    $statusBarData[$status] = round(($count / $totalRelevantPedidos) * 100, 2);
                }
            }
        }
        
        $ultimosClientesModels = $empresa->clientes()->with('user') 
            ->latest('cliente_empresa.created_at')->take(5)->get();

        $ultimosClientes = $ultimosClientesModels->map(function ($cliente) {
            if ($cliente->user) {
                $cliente->user->asociado_hace = $cliente->pivot->created_at->diffForHumans();
                return $cliente->user;
            }
            return null;
        })->filter(); 
        
        return [
            'view_type' => 'admin',
            'kpi' => $statusCounts,
            'totalPedidos' => $empresa->pedidos()->count(),
            'ingresosTotales' => $empresa->pedidos()->where('estado', 'entregado')->sum('total'),
            'totalProductos' => $empresa->productos()->count(),
            'pedidosRecientes' => $empresa->pedidos()->with('cliente.user')->latest()->take(5)->get(),
            'statusBar' => $statusBarData,
            'sparkline' => $this->getSparklineData($empresa->id),
            'totalCategorias' => $empresa->categorias()->count(),
            'totalClientes' => $empresa->clientes()->count(),
            'ultimosClientes' => $ultimosClientes,
            'topProductos' => $this->getTopProductos($empresa->id),
            'topCategorias' => $this->getTopCategorias($empresa->id),
            'pedidosAntiguos' => $this->getOldestPendingOrders($empresa->id),
            'productosBajoStock' => $this->getLowStockProducts($empresa->id),
        ];
    }

    private function getRepartidorData($repartidor)
    {
        $empresa = $repartidor->empresa;
        if (!$empresa) {
            return ['view_type' => 'no_empresa'];
        }
        
        $repartidorStatuses = ['enviado', 'entregado'];
        $statusCounts = $empresa->pedidos()->whereIn('estado', $repartidorStatuses)
            ->select('estado', DB::raw('count(*) as total'))->groupBy('estado')->pluck('total', 'estado')->all();
        foreach ($repartidorStatuses as $status) { if (!isset($statusCounts[$status])) $statusCounts[$status] = 0; }
        
        return [
            'view_type' => 'repartidor',
            'kpi' => $statusCounts,
            'pedidosParaEntregar' => $empresa->pedidos()->with('cliente.user')->where('estado', 'enviado')->latest()->get(),
            'pedidosEntregadosHoy' => $empresa->pedidos()->where('estado', 'entregado')->whereDate('updated_at', Carbon::today())->count(),
        ];
    }

    private function getSparklineData($empresaId = null)
    {
        $sparklineData = [];
        for ($i = 14; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $sparklineData[$date] = 0;
        }
        
        $query = Pedido::where('estado', 'entregado')->where('created_at', '>=', Carbon::now()->subDays(15));
        if ($empresaId) $query->where('empresa_id', $empresaId);
        $ingresosDiarios = $query->groupBy('date')->orderBy('date')->get([DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total')])->pluck('total', 'date');
            
        foreach($ingresosDiarios as $date => $total) {
            if(isset($sparklineData[$date])) $sparklineData[$date] = $total;
        }
        return ['labels' => array_keys($sparklineData), 'series' => array_values($sparklineData)];
    }
    
    private function getTopProductos($empresaId = null, $limit = 5)
    {
        $query = DB::table('detalle_pedido')->join('productos', 'detalle_pedido.producto_id', '=', 'productos.id')->select('productos.nombre', DB::raw('SUM(detalle_pedido.cantidad) as total_vendido'))->groupBy('productos.id', 'productos.nombre')->orderByDesc('total_vendido')->limit($limit);
        if ($empresaId) $query->where('productos.empresa_id', $empresaId);
        return $query->get();
    }

    private function getTopCategorias($empresaId = null, $limit = 5)
    {
        $query = DB::table('detalle_pedido')->join('productos', 'detalle_pedido.producto_id', '=', 'productos.id')->join('categorias', 'productos.categoria_id', '=', 'categorias.id')->select('categorias.nombre', DB::raw('SUM(detalle_pedido.cantidad) as total_vendido'))->groupBy('categorias.id', 'categorias.nombre')->orderByDesc('total_vendido')->limit($limit);
        if ($empresaId) $query->where('productos.empresa_id', $empresaId);
        return $query->get();
    }

    private function getOldestPendingOrders($empresaId = null, $limit = 3)
    {
        $query = Pedido::with('cliente.user', 'empresa')
                       ->where('estado', 'pendiente')
                       ->where('created_at', '<', now()->subDays(2))
                       ->orderBy('created_at', 'asc')
                       ->limit($limit);
        if ($empresaId) $query->where('empresa_id', $empresaId);
        return $query->get();
    }
    
    private function getLowStockProducts($empresaId = null, $limit = 3, $threshold = 5)
    {
        $query = Producto::where('stock', '<=', $threshold)
                         ->where('stock', '>', 0)
                         ->orderBy('stock', 'asc')
                         ->limit($limit);
        if ($empresaId) $query->where('empresa_id', $empresaId);
        return $query->get();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}