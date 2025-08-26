@extends('welcome.app')
@section('title', '¡Pedido Realizado con Éxito!')
@push('estilos')
<style>
    /* ==================== VARIABLES CSS ==================== */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --neutral-gradient: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    --danger-gradient: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%);

    --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.15);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);

    --border-radius-md: 0.75rem;
    --border-radius-lg: 1rem;
    --border-radius-xl: 1.5rem;

    --transition-normal: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

    --color-text-primary: #1a202c;
    --color-text-secondary: #4a5568;
    --color-text-muted: #6c757d;
}

/* ==================== BASE STYLES ==================== */
body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}

/* ==================== HERO SECTION ==================== */
.hero-section {
    background: var(--primary-gradient);
    position: relative;
    overflow: hidden;
    padding: 3rem 0;
    margin-bottom: 2rem;
    border-radius: 0 0 2rem 2rem;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="2"/></g></svg>');
    animation: float 20s linear infinite;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.success-icon {
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    animation: successPulse 2s infinite;
}

.success-icon i {
    font-size: 2.5rem;
    color: white;
    animation: checkBounce 1.5s ease-out;
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: white;
    margin-bottom: 1rem;
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    animation: slideInUp 1s ease-out 0.3s both;
}

.hero-subtitle {
    font-size: 1.15rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 0.5rem;
    animation: slideInUp 1s ease-out 0.5s both;
}

.hero-description {
    font-size: 1rem;
    color: rgba(255, 255, 255, 0.8);
    animation: slideInUp 1s ease-out 0.7s both;
}

.order-status-badge {
    display: inline-flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 2rem;
    padding: 0.75rem 1.5rem;
    margin-top: 1rem;
    color: white;
    font-weight: 600;
}

/* ==================== FLOATING ELEMENTS ==================== */
.floating-element {
    position: absolute;
    opacity: 0.1;
    animation: floatingRotate 15s linear infinite;
    color: white;
    pointer-events: none;
}

.floating-1 { top: 20%; left: 10%; animation-delay: -2s; font-size: 1rem !important; }
.floating-2 { top: 60%; right: 10%; animation-delay: -8s; font-size: 1.5rem !important; }
.floating-3 { bottom: 20%; left: 20%; animation-delay: -14s; font-size: 2rem !important; }

/* ==================== CARD STYLES ==================== */
.premium-card {
    background: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-xl);
    border: none;
    overflow: hidden;
    animation: fadeInScale 1s ease-out 0.9s both;
}

.premium-card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 3px solid transparent;
    background-clip: padding-box;
    position: relative;
}

.premium-card-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--success-gradient);
}

.premium-card-header h5 {
    font-weight: 700;
    color: var(--color-text-primary);
    font-size: 1.3rem;
}

/* ==================== TICKET STYLES ==================== */
#resumen-pedido {
    background: #ffffff;
    border: none;
    border-radius: var(--border-radius-xl);
    padding: 0;
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-xl);
    margin: 2rem 0;
}

/* Header del ticket */
.ticket-header {
    background: var(--primary-gradient);
    color: white;
    padding: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.ticket-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="30" height="30" viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="15" cy="15" r="1.5"/></g></svg>');
    animation: ticketPattern 20s linear infinite;
}

.ticket-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    position: relative;
    z-index: 2;
}

.ticket-icon i {
    font-size: 1.8rem;
    color: white;
}

.ticket-title {
    font-size: 1.3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 2;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.ticket-subtitle {
    font-size: 0.9rem;
    opacity: 0.9;
    margin-bottom: 1.5rem;
    position: relative;
    z-index: 2;
}

/* Información básica del pedido */
.ticket-info {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    background: rgba(255, 255, 255, 0.15);
    border-radius: var(--border-radius-lg);
    padding: 1rem;
    backdrop-filter: blur(10px);
    position: relative;
    z-index: 2;
}

.info-item {
    text-align: center;
}

.info-label {
    font-size: 0.7rem;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
    font-weight: 600;
}

.info-value {
    font-size: 0.85rem;
    font-weight: 700;
}

/* Separador con estilo de ticket */
.ticket-divider {
    height: 2px;
    background: linear-gradient(90deg, transparent 0%, #e2e8f0 20%, #e2e8f0 80%, transparent 100%);
    background-size: 20px 2px;
    background-repeat: repeat-x;
    margin: 0;
    position: relative;
}

.ticket-divider::before,
.ticket-divider::after {
    content: '';
    position: absolute;
    top: -8px;
    width: 16px;
    height: 16px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 50%;
}

.ticket-divider::before { left: -8px; }
.ticket-divider::after { right: -8px; }

/* Cuerpo del ticket */
.ticket-body {
    padding: 2rem;
    background: #ffffff;
}

/* Items del pedido */
.product-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: var(--border-radius-lg);
    border: 1px solid #e2e8f0;
    margin-bottom: 1rem;
}

.product-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-gradient);
    border-radius: var(--border-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.product-details {
    flex: 1;
    min-width: 0;
}

.product-name {
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
    line-height: 1.3;
}

.product-quantity-price {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: space-between;
}

.quantity-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: var(--color-text-secondary);
}

.quantity-badge {
    background: var(--success-gradient);
    color: white;
    padding: 0.2rem 0.6rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    box-shadow: 0 2px 8px rgba(17, 153, 142, 0.3);
}

.unit-price {
    font-size: 0.8rem;
}

.product-price {
    font-weight: 800;
    font-size: 1rem;
    color: var(--color-text-primary);
}

/* Sección de notas */
.notes-section {
    background: linear-gradient(135deg, #fef5e7 0%, #fed7aa 100%);
    border: 2px dashed #f59e0b;
    border-radius: var(--border-radius-lg);
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.notes-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 700;
    color: #92400e;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.notes-content {
    color: #78350f;
    font-style: italic;
    line-height: 1.6;
    font-size: 0.85rem;
}

/* Sección total */
.total-section {
    background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
    color: white;
    padding: 1.5rem;
    border-radius: var(--border-radius-lg);
    position: relative;
    overflow: hidden;
    margin-top: 1rem;
}

.total-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="20" cy="20" r="2"/></g></svg>');
    animation: totalPattern 25s linear infinite;
}

.total-content {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.total-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.1rem;
    font-weight: 700;
}

.total-icon {
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: var(--border-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.total-amount {
    font-size: 1.5rem;
    font-weight: 900;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    letter-spacing: 1px;
}

/* ==================== BUTTON STYLES ==================== */
.btn-action {
    padding: 0.875rem 1.75rem;
    font-weight: 600;
    font-size: 0.9rem;
    border-radius: var(--border-radius-md);
    border: none;
    text-decoration: none;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    min-width: 180px;
    margin: 0.4rem;
    transition: var(--transition-normal);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    cursor: pointer;
}

.btn-accion-primaria {
    background: var(--success-gradient);
    color: white;
}

.btn-accion-secundaria {
    background: var(--info-gradient);
    color: white;
}

.btn-accion-terciaria {
    background: var(--neutral-gradient);
    color: white;
}

.btn-accion-cuaternaria {
    background: var(--danger-gradient);
    color: white;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15);
    text-decoration: none;
    color: white;
}

.btn-action:active {
    transform: translateY(0);
}

.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    justify-content: center;
    margin-top: 2rem;
}

.btn-pulse {
    animation: buttonPulse 2s infinite;
}

/* ==================== INFORMACIÓN ADICIONAL ==================== */
.additional-info {
    margin-top: 3rem !important;
}

.info-card {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    text-align: center;
    border: 1px solid #e2e8f0;
    transition: var(--transition-normal);
    height: 100%;
    margin-bottom: 1rem;
}

.info-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.2rem;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.info-content h6 {
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.info-content p {
    color: var(--color-text-secondary);
    margin-bottom: 0;
    font-size: 0.85rem;
    line-height: 1.4;
}

/* ==================== TOAST NOTIFICATION ==================== */
.toast-notification {
    position: fixed;
    top: 2rem;
    right: 2rem;
    background: white;
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-xl);
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    z-index: 9999;
    transform: translateX(100%);
    transition: var(--transition-normal);
    border-left: 4px solid #10b981;
    min-width: 280px;
}

.toast-notification.show {
    transform: translateX(0);
}

.toast-icon {
    width: 36px;
    height: 36px;
    background: var(--success-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    flex-shrink: 0;
}

.toast-content {
    flex: 1;
}

.toast-title {
    font-weight: 700;
    color: var(--color-text-primary);
    margin-bottom: 0.2rem;
    font-size: 0.9rem;
}

.toast-message {
    color: var(--color-text-secondary);
    font-size: 0.8rem;
}

/* ==================== ANIMACIONES ==================== */
@keyframes float {
    0% { transform: translateX(0) translateY(0); }
    100% { transform: translateX(-60px) translateY(-60px); }
}

@keyframes successPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

@keyframes checkBounce {
    0% { transform: scale(0) rotate(-180deg); opacity: 0; }
    50% { transform: scale(1.2) rotate(-10deg); opacity: 1; }
    100% { transform: scale(1) rotate(0deg); opacity: 1; }
}

@keyframes slideInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes floatingRotate {
    0% { transform: rotate(0deg) translateX(50px) rotate(0deg); }
    100% { transform: rotate(360deg) translateX(50px) rotate(-360deg); }
}

@keyframes fadeInScale {
    from { opacity: 0; transform: scale(0.95) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

@keyframes ticketPattern {
    0% { transform: translateX(0) translateY(0); }
    100% { transform: translateX(-30px) translateY(-30px); }
}

@keyframes totalPattern {
    0% { transform: translateX(0) translateY(0); }
    100% { transform: translateX(-40px) translateY(-40px); }
}

@keyframes confettiFall {
    to { transform: translateY(100vh) rotate(720deg); opacity: 0; }
}

@keyframes buttonPulse {
    0%, 100% { box-shadow: var(--shadow-lg); }
    50% { box-shadow: 0 12px 25px rgba(17, 153, 142, 0.3); }
}

/* ==================== RESPONSIVE DESIGN ==================== */
@media (max-width: 768px) {
    .hero-section {
        padding: 2rem 0;
        margin-bottom: 1rem;
    }

    .hero-title {
        font-size: 2rem;
    }

    .hero-subtitle {
        font-size: 1rem;
    }

    .hero-description {
        font-size: 0.9rem;
    }

    .success-icon {
        width: 80px;
        height: 80px;
    }

    .success-icon i {
        font-size: 2rem;
    }

    .order-status-badge {
        padding: 0.6rem 1.2rem;
        font-size: 0.85rem;
    }

    #resumen-pedido {
        margin: 1rem 0;
    }

    .ticket-header {
        padding: 1.5rem 1rem;
    }

    .ticket-body {
        padding: 1.5rem 1rem;
    }

    .ticket-info {
        gap: 0.75rem;
        padding: 0.75rem;
    }

    .info-label {
        font-size: 0.65rem;
    }

    .info-value {
        font-size: 0.8rem;
    }

    .ticket-icon {
        width: 50px;
        height: 50px;
    }

    .ticket-icon i {
        font-size: 1.5rem;
    }

    .ticket-title {
        font-size: 1.15rem;
    }

    .ticket-subtitle {
        font-size: 0.85rem;
    }

    .product-item {
        padding: 1rem 0.75rem;
        gap: 0.75rem;
    }

    .product-icon {
        width: 36px;
        height: 36px;
        font-size: 1rem;
    }

    .product-name {
        font-size: 0.9rem;
    }

    .product-quantity-price {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .quantity-info {
        font-size: 0.8rem;
    }

    .quantity-badge {
        font-size: 0.7rem;
        padding: 0.15rem 0.5rem;
    }

    .unit-price {
        font-size: 0.75rem;
    }

    .product-price {
        font-size: 0.95rem;
        align-self: flex-end;
    }

    .notes-section {
        padding: 0.75rem;
        margin-bottom: 1rem;
    }

    .notes-title {
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
    }

    .notes-content {
        font-size: 0.8rem;
    }

    .total-section {
        padding: 1.25rem;
    }

    .total-content {
        flex-direction: column;
        gap: 0.75rem;
        text-align: center;
    }

    .total-label {
        font-size: 1rem;
        gap: 0.5rem;
    }

    .total-icon {
        width: 32px;
        height: 32px;
    }

    .total-amount {
        font-size: 1.3rem;
    }

    .btn-action {
        min-width: 100%;
        margin: 0.25rem 0;
        padding: 0.75rem 1.5rem;
        font-size: 0.85rem;
    }

    .action-buttons {
        flex-direction: column;
        gap: 0.5rem;
        margin-top: 1.5rem;
    }

    .additional-info {
        margin-top: 2rem !important;
    }

    .info-card {
        padding: 1.25rem;
    }

    .info-icon {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
    }

    .info-content h6 {
        font-size: 0.9rem;
    }

    .info-content p {
        font-size: 0.8rem;
    }

    .toast-notification {
        top: 1rem;
        right: 1rem;
        left: 1rem;
        min-width: auto;
    }

    .floating-element {
        display: none;
    }

    .premium-card-header h5 {
        font-size: 1.15rem;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 1.5rem 0;
    }

    .hero-title {
        font-size: 1.75rem;
    }

    .hero-subtitle {
        font-size: 0.95rem;
    }

    .hero-description {
        font-size: 0.85rem;
    }

    .success-icon {
        width: 70px;
        height: 70px;
    }

    .success-icon i {
        font-size: 1.75rem;
    }

    .ticket-header {
        padding: 1.25rem 0.75rem;
    }

    .ticket-body {
        padding: 1.25rem 0.75rem;
    }

    .ticket-info {
        padding: 0.6rem;
        gap: 0.5rem;
    }

    .product-item {
        padding: 0.875rem 0.6rem;
    }

    .product-icon {
        width: 32px;
        height: 32px;
        font-size: 0.9rem;
    }

    .total-section {
        padding: 1rem;
    }

    .total-amount {
        font-size: 1.2rem;
    }

    .additional-info {
        margin-top: 1.5rem !important;
    }

    .info-card {
        padding: 1rem;
    }
}

/* ==================== UTILITY CLASSES ==================== */
.text-muted { color: var(--color-text-muted) !important; }
.mb-0 { margin-bottom: 0 !important; }
.mb-4 { margin-bottom: 1.5rem !important; }
.p-4 { padding: 1.5rem !important; }
.p-5 { padding: 3rem !important; }
.p-md-5 { padding: 3rem !important; }
.me-2 { margin-right: 0.5rem !important; }
.me-1 { margin-right: 0.25rem !important; }
.text-center { text-align: center !important; }

/* ==================== PRINT STYLES ==================== */
@media print {
    .hero-section,
    .action-buttons,
    .additional-info,
    .floating-element {
        display: none !important;
    }
    
    #resumen-pedido {
        box-shadow: none !important;
        border: 1px solid #000 !important;
        margin: 0 !important;
    }
    
    .ticket-header {
        background: #f5f5f5 !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .premium-card {
        box-shadow: none !important;
        border: none !important;
    }
    
    body {
        background: white !important;
    }
}
</style>
@endpush

@section('contenido')
@livewire('order.success-page', ['pedido' => $pedido])
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        const copyBtn = document.getElementById('copy-btn');
        const printBtn = document.getElementById('print-btn');
        const whatsappBtn = document.getElementById('whatsapp-btn');
        const resumenPedidoContainer = document.getElementById('resumen-pedido');
        const textoParaCopiarEl = document.getElementById('texto-para-copiar');

        // --- Funcionalidad del Botón Copiar ---
        if (copyBtn && textoParaCopiarEl) {
            copyBtn.addEventListener('click', function() {
                const textoParaCopiar = textoParaCopiarEl.value;

                navigator.clipboard.writeText(textoParaCopiar).then(() => {
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<i class="fa-solid fa-check me-2"></i>¡Copiado!';
                    this.classList.add('btn-copied');
                    createMiniConfetti();

                    setTimeout(() => {
                        this.innerHTML = originalContent;
                        this.classList.remove('btn-copied');
                    }, 2500);
                }).catch(err => {
                    console.error('Error al copiar: ', err);
                    alert('No se pudo copiar el texto. Por favor, hazlo manualmente.');
                });
            });
        }
        
        // --- Funcionalidad del Botón Imprimir ---
        if (printBtn && resumenPedidoContainer) {
            printBtn.addEventListener('click', function () {
                const printContent = resumenPedidoContainer.innerHTML;
                const printWindow = window.open('', '', 'height=800,width=800');
                printWindow.document.write(`<html><head><title>Resumen Pedido #${'{{ $pedido->id }}'}</title>`);
                printWindow.document.write('<style>body{font-family:sans-serif;padding:20px;} .ticket-header{text-align:center;} .product-item{margin-bottom:10px;}</style>'); 
                printWindow.document.write('</head><body>');
                printWindow.document.write(printContent);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            });
        }
        
        // --- Funcionalidad del Botón WhatsApp (Efecto Ripple) ---
        if (whatsappBtn) {
            whatsappBtn.addEventListener('click', (e) => createRippleEffect(e, whatsappBtn));
        }

        // --- Efectos visuales al cargar la página ---
        setTimeout(() => {
            createConfetti();
        }, 1000);
    });

    // --- FUNCIONES DE AYUDA ---

    function createConfetti() {
        const colors = ['#11998e', '#38ef7d', '#4facfe', '#00f2fe', '#667eea'];
        const confettiCount = 30;
        const container = document.querySelector('.hero-section') || document.body;
        
        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.style.cssText = `
                position: absolute;
                top: -20px;
                left: ${Math.random() * 100}%;
                width: 8px; height: 8px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                border-radius: 50%; pointer-events: none; z-index: 1000;
                animation: confettiFall ${2 + Math.random() * 3}s linear forwards;
            `;
            container.appendChild(confetti);
            setTimeout(() => { confetti.remove(); }, 5000);
        }
    }

    function createMiniConfetti() {
        const copyBtn = document.getElementById('copy-btn');
        if (!copyBtn) return;
        const rect = copyBtn.getBoundingClientRect();
        const colors = ['#11998e', '#38ef7d', '#4facfe'];
        const confettiCount = 15;
        
        for (let i = 0; i < confettiCount; i++) {
            const confetti = document.createElement('div');
            confetti.style.cssText = `
                position: fixed;
                top: ${rect.top + rect.height / 2}px;
                left: ${rect.left + rect.width / 2}px;
                width: 6px; height: 6px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                border-radius: 50%; pointer-events: none; z-index: 1001;
                animation: miniConfettiBurst 1s ease-out forwards;
            `;
            document.body.appendChild(confetti);
            setTimeout(() => { confetti.remove(); }, 1000);
        }
    }

    function createRippleEffect(e, element) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        `;
        
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }
    
    // Inyectar estilos para las animaciones
    (function() {
        const dynamicStyles = document.createElement('style');
        dynamicStyles.textContent = `
            @keyframes confettiFall {
                to { transform: translateY(100vh) rotate(720deg); opacity: 0; }
            }
            @keyframes miniConfettiBurst {
                0% { opacity: 1; transform: scale(1); }
                100% { opacity: 0; transform: scale(0) translate(${(Math.random() - 0.5) * 200}px, ${(Math.random() - 0.5) * 200}px); }
            }
            @keyframes ripple {
                to { transform: scale(4); opacity: 0; }
            }
        `;
        document.head.appendChild(dynamicStyles);
    })();
    
</script>
@endpush