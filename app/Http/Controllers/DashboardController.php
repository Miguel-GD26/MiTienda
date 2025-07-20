<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private $relevantStatuses = ['pendiente', 'atendido', 'enviado', 'entregado', 'cancelado'];

    public function index()
    {
        $user = Auth::user();
        $data = [];

        if ($user->hasRole('super_admin')) {
            $data = $this->getSuperAdminData();
        } elseif ($user->hasRole('admin')) {
            $data = $this->getAdminData($user->empresa);
        } else {
            return view('welcome');
        }
        
        return view('dashboard', $data);
    }
    
    private function getSuperAdminData()
    {
        $statusCounts = Pedido::whereIn('estado', $this->relevantStatuses)
            ->select('estado', DB::raw('count(*) as total'))->groupBy('estado')->pluck('total', 'estado')->all();
        foreach ($this->relevantStatuses as $status) { if (!isset($statusCounts[$status])) $statusCounts[$status] = 0; }
        
        $totalPedidos = Pedido::count();
        $totalRelevantPedidos = array_sum($statusCounts);
        $statusBarData = [];
        if ($totalRelevantPedidos > 0) {
            foreach ($statusCounts as $status => $count) {
                $statusBarData[$status] = round(($count / $totalRelevantPedidos) * 100, 2);
            }
        }
        
        $ultimasEmpresas = Empresa::latest()->take(5)->get();
        $ultimosClientes = User::role('cliente')->latest()->take(5)->get();

        return [
            'kpi' => $statusCounts,
            'totalPedidos' => $totalPedidos,
            'ingresosTotales' => Pedido::where('estado', 'entregado')->sum('total'),
            'totalEmpresas' => Empresa::count(),
            'pedidosRecientes' => Pedido::with('empresa', 'cliente.user')->latest()->take(7)->get(),
            'statusBar' => $statusBarData,
            'sparkline' => $this->getSparklineData(),
            'totalProductos' => Producto::count(),
            'totalCategorias' => Categoria::count(),
            'ultimasEmpresas' => $ultimasEmpresas,
            'ultimosClientes' => $ultimosClientes,
        ];
    }
    
    private function getAdminData($empresa)
    {
        if (!$empresa) {
            return [ ];
        }

        $statusCounts = $empresa->pedidos()->whereIn('estado', $this->relevantStatuses)
            ->select('estado', DB::raw('count(*) as total'))->groupBy('estado')->pluck('total', 'estado')->all();
        foreach ($this->relevantStatuses as $status) { if (!isset($statusCounts[$status])) $statusCounts[$status] = 0; }
        
        $totalPedidos = $empresa->pedidos()->count();
        $totalRelevantPedidos = array_sum($statusCounts);
        $statusBarData = [];
        if ($totalRelevantPedidos > 0) {
            foreach ($statusCounts as $status => $count) {
                $statusBarData[$status] = round(($count / $totalRelevantPedidos) * 100, 2);
            }
        }
        
        $sparklineData = $this->getSparklineData($empresa->id);

        $ultimosClientesModels = $empresa->clientes()
            ->with('user') 
            ->latest('cliente_empresa.created_at') 
            ->take(5)
            ->get();

        $ultimosClientes = $ultimosClientesModels->map(function ($cliente) {
            if ($cliente->user) {
                $cliente->user->asociado_hace = $cliente->pivot->created_at->diffForHumans();
                return $cliente->user;
            }
            return null;
        })->filter(); 
        
        return [
            'kpi' => $statusCounts,
            'totalPedidos' => $totalPedidos,
            'ingresosTotales' => $empresa->pedidos()->where('estado', 'entregado')->sum('total'),
            'totalProductos' => $empresa->productos()->count(),
            'pedidosRecientes' => $empresa->pedidos()->with('cliente.user')->latest()->take(7)->get(),
            'statusBar' => $statusBarData,
            'sparkline' => $sparklineData,
            'totalCategorias' => $empresa->categorias()->count(),
            'totalClientes' => $empresa->clientes()->count(),
            'ultimosClientes' => $ultimosClientes, 
        ];
    }

    private function getSparklineData($empresaId = null)
    {
        $sparklineData = [];
        for ($i = 14; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $sparklineData[$date] = 0;
        }
        
        $query = Pedido::where('estado', 'entregado')
            ->where('created_at', '>=', Carbon::now()->subDays(14));

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        $ingresosDiarios = $query->groupBy('date')
            ->orderBy('date')
            ->get([DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as total')])
            ->pluck('total', 'date');
            
        foreach($ingresosDiarios as $date => $total) {
            if(isset($sparklineData[$date])) {
                $sparklineData[$date] = $total;
            }
        }

        return [
            'labels' => array_keys($sparklineData),
            'series' => array_values($sparklineData)
        ];
    }
}