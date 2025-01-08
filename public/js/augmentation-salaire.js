jQuery(document).ready(function ($) {
    let salaryChart = null;

    // Supprimer le graphique existant si présent
    function destroyChart() {
        if (salaryChart) {
            salaryChart.destroy();
            salaryChart = null;
        }
    }

    $('#augmentation-salaire-form').on('submit', function (e) {
        e.preventDefault();
        destroyChart(); // Détruire le graphique existant

        var amount = $('#amount').val();
        var year = $('#year').val();
        var nonce = $('#augmentation_salaire_nonce_field').val();

        $.ajax({
            url: augmentation_salaire_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'augmentation_salaire',
                amount: amount,
                year: year,
                security: nonce
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Afficher le résultat avec le lien "En savoir plus"
                    const resultHtml = `
                        <div class="result-text">
                            Votre salaire actuel devrait être ${response.data.adjustedAmount} €
                        </div>
                        <div class="show-more-link">
                            <a href="#" id="show-more-salary">En savoir plus</a>
                        </div>
                        <div id="salary-chart-container" style="display:none;">
                            <canvas id="salary-chart"></canvas>
                        </div>
                    `;
                    
                    $('#augmentation-salaire-result').html(resultHtml);

                    // Gestionnaire de clic pour "En savoir plus"
                    $('#show-more-salary').off('click').on('click', function(e) {
                        e.preventDefault();
                        $('#salary-chart-container').slideToggle();
                        
                        if (!salaryChart) {
                            createSalaryChart(response.data);
                        }
                    });
                } else {
                    $('#augmentation-salaire-result').html(response.data);
                }
            },
            error: function () {
                $('#augmentation-salaire-result').html('Erreur lors de l\'appel à l\'API.');
            }
        });
    });

    function createSalaryChart(data) {
        const ctx = document.getElementById('salary-chart').getContext('2d');
        
        salaryChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.years,
                datasets: [
                    {
                        label: 'SMIC',
                        data: data.SmicOverTime,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        fill: false
                    },
                    {
                        label: 'Salaire Moyen',
                        data: data.SalaryAverageOverTime,
                        borderColor: 'rgb(255, 99, 132)',
                        tension: 0.1,
                        fill: false
                    },
                    {
                        label: 'Votre Salaire Ajusté',
                        data: data.salaryAdjustedOverTime,
                        borderColor: 'rgb(54, 162, 235)',
                        tension: 0.1,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Évolution des salaires'
                    },
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Salaire (€)'
                        }
                    }
                }
            }
        });
    }
});
