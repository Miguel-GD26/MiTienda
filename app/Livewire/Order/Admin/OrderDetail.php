<?php

namespace App\Livewire\Order\Admin;

use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use chillerlan\QRCode\QRCode; // <-- NUEVA LIBRERÍA
use chillerlan\QRCode\QROptions;

class OrderDetail extends Component
{
    public Pedido $pedido;
    public $estadoSeleccionado;

    public function mount(Pedido $pedido)
    {
        $this->authorize('pedido-view', $this->pedido);
        $this->pedido->load('detalles.producto', 'empresa', 'cliente');
        $this->estadoSeleccionado = $this->pedido->estado;
    }

    private function devolverStockDePedido()
    {
        // Usamos una nueva consulta para evitar problemas de estado del modelo
        $pedidoParaStock = Pedido::with('detalles.producto')->find($this->pedido->id);
        foreach ($pedidoParaStock->detalles as $detalle) {
            if ($detalle->producto) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }
        }
    }

    public function updateStatus()
    {
        $this->authorize('pedido-update-status', $this->pedido);
        if ($this->estadoSeleccionado === $this->pedido->estado || in_array($this->pedido->estado, ['entregado', 'cancelado'])) return;

        DB::transaction(function () {
            if ($this->pedido->estado !== 'cancelado' && $this->estadoSeleccionado === 'cancelado') {
                $this->devolverStockDePedido();
            }
            $this->pedido->update(['estado' => $this->estadoSeleccionado]);
        });

        $this->pedido->refresh();
        
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Estado actualizado a "' . ucfirst($this->estadoSeleccionado) . '".']);
        $this->dispatch('$refresh');
    }

    public function cancelOrder()
    {
        $this->authorize('pedido-cancel', $this->pedido);
        if (!in_array($this->pedido->fresh()->estado, ['pendiente', 'atendido'])) {
            $this->dispatch('alert', ['type' => 'info', 'message' => 'Este pedido ya no se puede cancelar.']);
            return;
        }

        DB::transaction(function () {
            $this->devolverStockDePedido();
            $this->pedido->update(['estado' => 'cancelado']);
        });
        
        $this->pedido->refresh();
        $this->estadoSeleccionado = $this->pedido->estado; 
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Pedido cancelado y stock devuelto.']);

        $this->dispatch('close-cancel-modal');
        $this->dispatch('$refresh');
    }

    public function downloadPdf()
    {
        $this->authorize('pedido-view', $this->pedido);
        $this->pedido->loadMissing('detalles.producto', 'empresa.usuarios', 'cliente.user');

        $logoBase64 = null;
        try {
            if ($this->pedido->empresa && $this->pedido->empresa->logo_url) {
                $imageUrl = cloudinary()->image($this->pedido->empresa->logo_url)->toUrl();
                $imageData = @file_get_contents($imageUrl);
                if ($imageData) {
                    $logoBase64 = 'data:image/' . pathinfo($imageUrl, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imageData);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al obtener logo para PDF: ' . $e->getMessage());
        }

        if (!$logoBase64) {
            $defaultLogoPath = public_path('images/logo.png');
            if (file_exists($defaultLogoPath)) {
                $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($defaultLogoPath));
            }
        }

        // --- GENERACIÓN DE QR CON FORMATO ESTRUCTURADO (MECARD) ---
        $qrCodeBase64 = null;
        try {
            $clienteNombre = $this->pedido->cliente?->nombre ?? 'N/A';
            $empresaNombre = $this->pedido->empresa?->nombre ?? 'Mi Empresa';
            $totalFormateado = number_format($this->pedido->total, 2);

            // --- ¡AQUÍ ESTÁ EL CAMBIO! ---
            // Usamos el formato MECARD para que los datos aparezcan ordenados al escanear.
            $qrText = "MECARD:N:{$clienteNombre};ORG:{$empresaNombre};NOTE:Pedido #{$this->pedido->id}, Total S/.{$totalFormateado};;";
            
            // Opciones para el QR: le decimos que la salida sea una imagen PNG en base64
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_IMAGE_PNG,
                'imageBase64' => true,
                'eccLevel' => QRCode::ECC_L, // Nivel de corrección de error bajo, para más datos
                'scale' => 5,
            ]);

            // Generamos el QR
            $qrCodeBase64 = (new QRCode($options))->render($qrText);

        } catch (\Exception $e) {
            Log::error('ERROR CON LA NUEVA LIBRERÍA QR: ' . $e->getMessage());
        }

        $data = [
            'pedido' => $this->pedido,
            'logoData' => $logoBase64,
            'qrCode' => $qrCodeBase64,
        ];

        $pdf = Pdf::loadView('pdf.order-pdf', $data);
        $fileName = 'comprobante-pedido-' . $this->pedido->id . '.pdf';

        return response()->streamDownload(fn() => print($pdf->output()), $fileName);
    }

    public function render()
    {
        return view('livewire.order.admin.order-detail');
    }
}