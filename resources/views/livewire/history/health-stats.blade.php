<x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
        📈 Estadísticas de Salud
    </h2>
</x-slot>

<div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

    <div class="mb-6 overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
            <div class="p-6">
                <h3 class="mb-4 text-lg font-bold text-gray-700 dark:text-gray-200">
                    ⚖️ Evolución de Peso
                </h3>

                <div class="relative w-full h-72">
                    <canvas id="weightChart"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('weightChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($weightLabels),
                datasets: [{
                    label: 'Peso (Kg)',
                    data: @json($weightData),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(156, 163, 175, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
