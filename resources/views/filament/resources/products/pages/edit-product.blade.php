<x-filament-panels::page>
    <style>
        .fi-section{
            margin-bottom: 2rem;
        }
        .product-edit-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .product-edit-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }

        .dark .product-edit-title {
            color: #f3f4f6;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: #1f2937;
        }

        .dark .section-title {
            color: #f3f4f6;
        }

        .relation-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            padding: 2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            height: 100%;
        }

        .dark .relation-card {
            background: #1f2937;
            border-color: #374151;
        }

        .relation-card:hover {
            border-color: #3b82f6;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            transform: translateY(-4px);
        }

        .dark .relation-card:hover {
            border-color: #60a5fa;
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            transition: transform 0.3s ease;
        }

        .relation-card:hover .icon-circle {
            transform: scale(1.1);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }

        .dark .card-title {
            color: #f3f4f6;
        }

        .card-description {
            color: #6b7280;
            font-size: 0.95rem;
        }

        .dark .card-description {
            color: #9ca3af;
        }
    </style>

    <div class="space-y-8">

        <!-- Formulario Principal -->
        <x-filament::section>
            <div class="product-edit-header">
                <h2 class="product-edit-title">Información del Producto</h2>
            </div>

            {{ $this->form }}

            <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                <x-filament::button 
                    wire:click="save"
                    color="primary"
                    icon="heroicon-o-check"
                    style="padding: 0.75rem 2rem; font-size: 1.05rem; font-weight: 600;">
                    💾 Guardar cambios
                </x-filament::button>
                
                <x-filament::button 
                    icon="heroicon-o-x-mark"
                    color="gray"
                    wire:click="cancel"
                    style="padding: 0.75rem 2rem; font-size: 1.05rem; font-weight: 600;">
                    Cancelar
                </x-filament::button>
            </div>
        </x-filament::section>

        <!-- Tarjetas -->
        <x-filament::section>
            <h2 class="section-title">Gestión Avanzada del Producto</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">

                <!-- Galería de Multimedia - Página Completa -->
                <div onclick="window.location.href = '{{ \App\Filament\Resources\Products\ProductResource::getUrl('manage-media', ['record' => $record]) }}'"
                    class="relation-card">
                    <div class="icon-circle" style="background: #dbeafe;">
                        <x-heroicon-o-photo style="width: 48px; height: 48px; color: #2563eb;" />
                    </div>
                    <h3 class="card-title">Galería de Multimedia</h3>
                    <p class="card-description">Imágenes, videos, YouTube y Vimeo</p>
                </div>

                <!-- Características -->
                <div onclick="window.location.href = '{{ \App\Filament\Resources\Products\ProductResource::getUrl('manage-features', ['record' => $record]) }}'"
                    class="relation-card">
                    <div class="icon-circle" style="background: #fef3c7;">
                        <x-heroicon-o-star style="width: 48px; height: 48px; color: #d97706;" />
                    </div>
                    <h3 class="card-title">Beneficios de tu compra</h3>
                    <p class="card-description">Compra con confianza, rapidez y responsabilidad en cada pedido.</p>
                </div>

                <!-- Bloques de Contenido - Página Completa -->
                <div onclick="window.location.href = '{{ \App\Filament\Resources\Products\ProductResource::getUrl('manage-sections', ['record' => $record]) }}'"
                    class="relation-card">
                    <div class="icon-circle" style="background: #d1fae5;">
                        <x-heroicon-o-document-text style="width: 48px; height: 48px; color: #10b981;" />
                    </div>
                    <h3 class="card-title">Bloques de Contenido</h3>
                    <p class="card-description">Secciones personalizadas</p>
                </div>

                <!-- Productos Relacionados - Página Completa -->
                <div onclick="window.location.href = '{{ \App\Filament\Resources\Products\ProductResource::getUrl('manage-related-products', ['record' => $record]) }}'"
                    class="relation-card">
                    <div class="icon-circle" style="background: #f3e8ff;">
                        <x-heroicon-o-link style="width: 48px; height: 48px; color: #8b5cf6;" />
                    </div>
                    <h3 class="card-title">Productos Relacionados</h3>
                    <p class="card-description">Recomendaciones cruzadas</p>
                </div>

            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>