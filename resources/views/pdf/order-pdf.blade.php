<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Comprobante de Pedido #{{ $pedido->id }}</title>
    <style>
    @page {
        margin: 0;
    }

    body {
        font-family: 'Helvetica', 'Arial', sans-serif;
        font-size: 10px;
        color: #34495e;
        /* Un gris azulado más suave */
    }

    .invoice-box {
        position: relative;
        width: 100%;
        height: 100%;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    p {
        margin: 0;
        padding: 0;
    }

    .content-wrapper {
        padding: 40px 50px;
    }

    /* --- Paleta y Formas Vibrantes --- */
    :root {
        --color-primary: #007BFF;
        /* Azul Eléctrico y Vibrante */
        --color-heading: #2c3e50;
        /* Mantenemos el gris oscuro para texto */
        --color-text: #7f8c8d;
        /* Gris sutil */
        --color-border: #ecf0f1;
        /* Gris muy claro */
        --color-bg-light: #f8f9f9;
    }

    .header-shape {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 10px;
        background-color: var(--color-primary);
    }

    .footer-shape {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 10px;
        background-color: var(--color-primary);
    }

    /* --- Contenido Principal --- */
    .header-section {
        padding-bottom: 20px;
        border-bottom: 2px solid var(--color-border);
    }

    .header-section .company-logo {
        width: 80px;
        height: auto;
        vertical-align: middle;
    }

    .header-section .company-name {
        font-size: 20px;
        font-weight: bold;
        color: var(--color-heading);
        vertical-align: middle;
        padding-left: 15px;
    }

    .header-section .document-title {
        text-align: right;
    }

    .header-section h1 {
        font-size: 32px;
        color: var(--color-heading);
        font-weight: bold;
    }

    .header-section p {
        font-size: 10px;
        color: var(--color-text);
    }

    /* --- Información de Contacto --- */
    .contact-info-section {
        margin: 25px 0;
    }

    .contact-info-section td {
        width: 50%;
        vertical-align: top;
    }

    .contact-info-section h3 {
        font-size: 10px;
        color: var(--color-text);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        border-bottom: 1px solid var(--color-border);
        padding-bottom: 4px;
    }

    .contact-info-section p {
        line-height: 1.6;
        color: var(--color-heading);
    }

    .contact-info-section p.name {
        font-weight: bold;
        font-size: 12px;
    }

    /* --- Tabla de Items --- */
    .items-table th {
        background-color: var(--color-primary);
        color: #ffffff;
        padding: 10px;
        text-align: center;
        font-size: 9px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .items-table td {
        padding: 12px 10px;
        border-bottom: 1px solid var(--color-border);
        text-align: center;
        vertical-align: middle;
    }

    .items-table tr:nth-child(even) {
        background-color: var(--color-bg-light);
    }

    .items-table .item-description {
        text-align: left;
    }

    .items-table .item-description p {
        font-weight: bold;
    }

    .items-table .item-number {
        color: var(--color-text);
    }

    /* --- Sección de Totales --- */
    .totals-section {
        margin-top: 25px;
    }

    .verification-info h5 {
        font-size: 11px;
        color: var(--color-heading);
        margin-bottom: 8px;
    }

    .totals-table {
        width: 60%;
        float: right;
    }

    .totals-table td {
        padding: 8px 0;
        font-weight: bold;
    }

    .totals-table .label {
        text-align: right;
        padding-right: 20px;
        color: var(--color-text);
        font-weight: normal;
    }

    .totals-table .value {
        text-align: right;
    }

    .totals-table tr.grand-total td {
        padding: 15px 0;
        font-size: 14px;
        border-top: 3px double var(--color-primary);
        color: var(--color-heading);
        font-weight: bold;
    }

    .clearfix::after {
        content: "";
        display: table;
        clear: both;
    }

    /* --- Pie de Página --- */
    .footer {
        position: fixed;
        bottom: 40px;
        left: 50px;
        right: 50px;
    }

    .footer p {
        text-align: center;
        font-size: 9px;
        color: var(--color-text);
    }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- Barras de color superior e inferior -->
        <div class="header-shape"></div>
        <div class="footer-shape"></div>

        <div class="content-wrapper">
            <!-- SECCIÓN 1: ENCABEZADO CON INFO DE EMPRESA Y PEDIDO -->
            <table class="header-section">
                <tr>
                    <td>
                        @if(isset($logoData) && $logoData)
                        <img src="{{ $logoData }}" alt="Logo" class="company-logo">
                        @endif
                        <span class="company-name">{{ $pedido->empresa->nombre ?? 'Mi Empresa' }}</span>
                    </td>
                    <td class="document-title">
                        <h1>COMPROBANTE</h1>
                        <p>Pedido No. #{{ $pedido->id }}</p>
                        <p>Fecha: {{ $pedido->created_at->format('d/m/Y') }}</p>
                    </td>
                </tr>
            </table>

            <!-- SECCIÓN 2: INFORMACIÓN DE CONTACTO -->
            <table class="contact-info-section">
                <tr>
                    <td>
                        <h3>Proveedor</h3>
                        <p>{{ $pedido->empresa->nombre ?? 'Mi Empresa' }}</p>
                        <p>{{ $pedido->empresa->usuarios->first()?->email ?? 'correo@empresa.com' }}</p>
                        <p>Tel: {{ $pedido->empresa->telefono_whatsapp ?? 'N/A' }}</p>
                    </td>
                    <td>
                        <h3>Cliente</h3>
                        <p class="name">{{ $pedido->cliente->nombre ?? 'N/A' }}</p>
                        <p>{{ $pedido->cliente->user->email ?? 'N/A' }}</p>
                        <p>Tel: {{ $pedido->cliente->telefono ?? 'N/A' }}</p>
                    </td>
                </tr>
            </table>

            <!-- SECCIÓN 3: TABLA DE ITEMS -->
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">#</th>
                        <th style="text-align: left; width: 45%;">Descripción</th>
                        <th>P. Unitario</th>
                        <th>Cantidad</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedido->detalles as $index => $detalle)
                    <tr>
                        <td class="item-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td class="item-description">
                            <p>{{ $detalle->producto->nombre ?? 'Producto no disponible' }}</p>
                        </td>
                        <td>S/.{{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td style="text-align: right;">S/.{{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- SECCIÓN 4: TOTALES Y VERIFICACIÓN -->
            <div class="clearfix" style="margin-top: 30px;">
                <div style="width: 40%; float: left;" class="verification-info">
                    <h5>Verificación Digital</h5>
                    @if(isset($qrCode) && $qrCode)
                    <img src="{{ $qrCode }}" alt="Código QR" style="width: 90px; height: 90px;">
                    @endif
                </div>
                <div style="width: 60%; float: right;">
                    @php
                    $subtotal = $pedido->total / 1.18;
                    $igv = $pedido->total - $subtotal;
                    @endphp
                    <table class="totals-table">
                        <tr>
                            <td class="label">SUB TOTAL</td>
                            <td class="value">S/.{{ number_format($subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="label">IGV (18%)</td>
                            <td class="value">S/.{{ number_format($igv, 2) }}</td>
                        </tr>
                        <tr class="grand-total">
                            <td class="label">TOTAL A PAGAR</td>
                            <td class="value">S/.{{ number_format($pedido->total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- SECCIÓN 5: PIE DE PÁGINA -->
            <div class="footer">
                <p>Gracias por su compra. Este documento es un comprobante de pedido.</p>
            </div>
        </div>
    </div>
</body>

</html>