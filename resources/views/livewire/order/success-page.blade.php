<div>
    <input type="hidden" id="texto-para-copiar" value="{{ $whatsappMessage }}">
    <div class="container-contenido hero-section">
        <div class="container text-center hero-content">
            <div class="success-icon">
                <i class="fa-solid fa-check"></i>
            </div>
            <h1 class="hero-title">¡Pedido Realizado!</h1>
            <h2 class="hero-subtitle">Gracias por tu compra, {{ $pedido->cliente->nombre }}</h2>
            <p class="hero-description">Hemos recibido tu pedido #{{ $pedido->id }} y está siendo procesado.</p>

            {{-- Estado del pedido --}}
            <div class="order-status-badge">
                <i class="fa-solid fa-clock me-2"></i>
                <span class="status-text">Estado: {{ ucfirst($pedido->estado) }}</span>
            </div>
        </div>

        {{-- Elementos flotantes decorativos --}}
        <div class="floating-element floating-1"><i class="fa-solid fa-receipt"></i></div>
        <div class="floating-element floating-2"><i class="fa-solid fa-box-open"></i></div>
        <div class="floating-element floating-3"><i class="fa-solid fa-star"></i></div>
        <div class="floating-element floating-4"><i class="fa-solid fa-heart"></i></div>
        <div class="floating-element floating-5"><i class="fa-solid fa-gift"></i></div>

        {{-- Partículas decorativas --}}
        <div class="particles-container">
            @for($i = 0; $i < 20; $i++) <div class="particle particle-{{ $i + 1 }}">
        </div>@endfor
    </div>
</div>

{{-- Contenido principal de la página --}}
<div class="container-contenido" style="margin-top: -2rem;">

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="premium-card">
                <div class="premium-card-header p-4 text-center">
                    <h5 class="mb-0"><i class="fa-solid fa-wand-magic-sparkles me-2"></i>¿Qué sigue ahora?</h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    <p class="text-center text-muted mb-4">
                        Para agilizar el proceso, puedes enviar un resumen de tu compra directamente al WhatsApp de la tienda.
                    </p>

                    <div id="resumen-pedido" class="mb-4">

                        <div class="ticket-header">
                            <div class="ticket-icon">
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            {{-- TAMAÑOS DE FUENTE MÁS GRANDES EN ESCRITORIO --}}
                            <h1 class="ticket-title display-6">¡Nuevo Pedido Realizado! 🛍️</h1>
                            <p class="ticket-subtitle fs-5">Tu compra ha sido procesada exitosamente</p>

                            {{-- ¡CORRECCIÓN AQUÍ! Añadimos clases de Flexbox --}}
                            <div class="ticket-info d-md-flex justify-content-around">
                                <div class="info-item mb-3 mb-md-0">
                                    <div class="info-label">Pedido</div>
                                    <div class="info-value fs-5">{{ $pedido->id }}</div>
                                </div>
                                <div class="info-item mb-3 mb-md-0">
                                    <div class="info-label">Cliente</div>
                                    <div class="info-value fs-5">{{ $pedido->cliente->nombre }}</div>
                                </div>
                                <div class="info-item mb-3 mb-md-0">
                                    <div class="info-label">Fecha</div>
                                    <div class="info-value fs-5">{{ $pedido->created_at->format('d/m/Y') }}</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Hora</div>
                                    <div class="info-value fs-5">{{ $pedido->created_at->format('H:i') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="ticket-divider"></div>
                        <div class="ticket-body">
                            <div class="products-section">
                                @foreach($pedido->detalles as $detalle)
                                <div class="product-item">
                                    <div class="product-icon"><i class="fa-solid fa-box"></i></div>
                                    <div class="product-details">
                                        <div class="product-name">{{ $detalle->producto->nombre ?? 'Producto desconocido' }}</div>
                                        <div class="product-quantity-price">
                                            <div class="quantity-info"><span class="quantity-badge">{{ $detalle->cantidad }}x</span><span class="unit-price">Precio unitario: S/. {{ number_format($detalle->precio_unitario, 2) }}</span></div>
                                            <div class="product-price">S/. {{ number_format($detalle->subtotal, 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @if($pedido->notas)
                            <div class="notes-section">
                                <div class="notes-title"><i class="fa-solid fa-sticky-note"></i><span>Notas Especiales:</span></div>
                                <div class="notes-content">"{{ $pedido->notas }}"</div>
                            </div>
                            @endif
                            <div class="total-section">
                                <div class="total-content">
                                    <div class="total-label">
                                        <div class="total-icon"><i class="fa-solid fa-calculator"></i></div><span>TOTAL A PAGAR:</span>
                                    </div>
                                    <div class="total-amount">S/. {{ number_format($pedido->total, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Contenedor de botones de acción --}}
                    <div class="action-buttons">
                        @if($tiendaWhatsapp)
                        @php
                        $urlWhatsapp = "https://wa.me/" . $tiendaWhatsapp . "?text=" . rawurlencode($whatsappMessage);
                        @endphp
                        <a href="{{ $urlWhatsapp }}" target="_blank" id="whatsapp-btn" class="btn-action btn-accion-primaria btn-pulse">
                            <i class="fab fa-whatsapp me-2"></i><span>Enviar a la Tienda</span>
                            <div class="btn-shine"></div>
                        </a>
                        @endif

                        <button id="copy-btn" class="btn-action btn-accion-secundaria">
                            <i class="fa-solid fa-copy me-2"></i><span>Copiar Resumen</span>
                            <div class="btn-shine"></div>
                        </button>

                        <a href="{{ route('cliente.pedidos.show', $pedido) }}" class="btn-action btn-accion-terciaria" wire:navigate>
                            <i class="fa-solid fa-receipt me-2"></i><span>Ver Detalle Completo</span>
                            <div class="btn-shine"></div>
                        </a>

                        <button id="print-btn" class="btn-action btn-accion-cuaternaria">
                            <i class="fa-solid fa-print me-2"></i><span>Imprimir</span>
                            <div class="btn-shine"></div>
                        </button>
                    </div>

                    {{-- Información adicional --}}
                    <div class="additional-info mt-5">
                        <div class="row">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="info-card">
                                    <div class="info-icon"><i class="fa-solid fa-clock"></i></div>
                                    <div class="info-content">
                                        <h6>Tiempo Estimado</h6>
                                        <p>30-45 minutos</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="info-card">
                                    <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                                    <div class="info-content">
                                        <h6>¿Necesitas ayuda?</h6>
                                        <p>Contáctanos al WhatsApp</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="info-card">
                                    <div class="info-icon"><i class="fa-solid fa-star"></i></div>
                                    <div class="info-content">
                                        <h6>¡Califícanos!</h6>
                                        <p>Tu opinión es importante</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>