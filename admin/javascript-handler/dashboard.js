$(document).ready(function () {
    // AJAX request to fetch login time distribution
    $.ajax({
        url: 'php-functions/user-activity.php', // PHP script to fetch data
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            // Process data for Chart.js
            const labels = response.map(item => item.range); // X-axis labels (time ranges)
            const data = response.map(item => item.count);  // Y-axis values (user counts)

            // Render the chart
            const ctx = document.getElementById('loginTimeChart').getContext('2d');
            new Chart(ctx, {
                type: 'line', // Line chart type
                data: {
                    labels: labels, // X-axis labels
                    datasets: [{
                        label: 'Users',
                        data: data, // Y-axis data
                        borderColor: 'rgba(54, 162, 235, 1)', // Line color
                        backgroundColor: 'rgba(54, 162, 235, 0.2)', // Fill under the line
                        borderWidth: 2, // Line thickness
                        tension: 0.4 // Curved line effect
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true }
                    },
                    scales: {
                        x: { // X-axis configuration
                            title: {
                                display: true,
                                text: 'Login Time Ranges'
                            }
                        },
                        y: { // Y-axis configuration
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Users'
                            }
                        }
                    }
                }
            });
        },
        error: function () {
            alert('Error fetching login time data');
        }
    });
});

$(document).ready(function () {
    // AJAX request to fetch learning progress data
    $.ajax({
        url: 'php-functions/learning-progress-chart.php', // PHP script to fetch data
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            // Process data for Chart.js
            const labels = response.map(item => item.subject); // X-axis labels (Subject Types)
            const data = response.map(item => item.percentage); // Y-axis values (progress percentages)

            // Render the chart
            const ctx = document.getElementById('learningProgressChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar', // Or any chart type you prefer
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Learning Progress (%)',
                        data: data,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Subjects'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Progress Percentage (%)'
                            }
                        }
                    }
                }
            });
        },
        error: function () {
            alert('Error fetching learning progress data');
        }
    });
});
