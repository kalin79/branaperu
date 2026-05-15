<x-filament-panels::page>
    <style>
        .section-container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .detail-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .dark .detail-card {
            background: #1f2937;
            border-color: #4b5563;
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

        .section-heading {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
        }

        .dark .section-heading {
            color: #f3f4f6;
        }
    </style>

    <div class="section-container">

        {{-- ============ FORMULARIO PRINCIPAL ============ --}}
        <x-filament::section heading="🌿 Información de la Sección">
            <div class="detail-card">
                {{ $this->form }}

                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <x-filament::button
                        wire:click="save"
                        color="primary"
                        icon="heroicon-o-check"
                        style="padding: 0.75rem 2rem; font-size: 1.05rem; font-weight: 600;">
                        💾 Guardar cambios
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- ============ SUBMÓDULOS ============ --}}
        <x-filament::section heading="✨ Gestión de Características">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">

                <div onclick="window.location.href='{{ \App\Filament\Resources\PersonalCareSections\PersonalCareSectionResource::getUrl('manage-features', ['record' => $record]) }}'"
                    class="relation-card">
                    <div class="icon-circle" style="background: #d1fae5;">
                        <x-heroicon-o-star style="width: 48px; height: 48px; color: #10b981;" />
                    </div>
                    <h3 class="card-title">Características</h3>
                    <p class="card-description">Iconos, títulos y descripciones</p>
                </div>

            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>