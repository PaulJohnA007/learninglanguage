function printReportWithStats() {
    const reportContentElement = document.getElementById('reportContent');
    const printButtonContainer = document.querySelector('.print-button-container');
    // Select elements to potentially hide *before* print (though @media print should handle this)
    const headerElement = document.querySelector('.dashboard-header');
    const sidebarElement = document.querySelector('aside'); // Assuming your sidebar has an <aside> tag
    const timePeriod = document.getElementById('reportTimePeriod').value;

    // --- Create stats section (will be added later) ---
    const userStatsSection = document.createElement('div');
    userStatsSection.id = 'user-completion-stats-for-print'; // Use a unique ID
    // Add classes needed for styling, matching your @media print styles if necessary
    userStatsSection.className = 'dashboard-card p-6 mb-8 print-only-stats'; // Add a class to target if needed

    // Generate title based on time period
    let timePeriodTitle = "All Time";
    switch(timePeriod) {
        case 'day': timePeriodTitle = "Today"; break;
        case 'week': timePeriodTitle = "This Week"; break;
        case 'month': timePeriodTitle = "This Month"; break;
    }

    // --- Function to clean up after printing ---
    const cleanupAfterPrint = () => {
        const addedStatsSection = document.getElementById('user-completion-stats-for-print');
        if (addedStatsSection) {
            addedStatsSection.remove();
        }
        // Restore visibility if you were manually hiding elements (optional if @media print works)
        // if (printButtonContainer) printButtonContainer.style.display = 'flex';
        // if (headerElement) headerElement.style.display = 'block';
        // if (sidebarElement) sidebarElement.style.display = 'block'; // Or its original display value
    };

    // --- Fetch user completion data ---
    $.ajax({
        url: 'php-functions/get_user_completion_stats.php',
        type: 'GET',
        data: { timePeriod: timePeriod },
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                console.error(data.error);
                userStatsSection.innerHTML = `
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">User Completion Statistics (${timePeriodTitle})</h3>
                    <p>Error loading user data.</p>
                `;
            } else {
                userStatsSection.innerHTML = `
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">User Completion Statistics (${timePeriodTitle})</h3>
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-white p-4 rounded-lg border border-green-200">
                            <p class="text-sm font-medium text-gray-600">Completed All Words</p>
                            <h4 class="text-xl font-bold  mt-1">${data.completedUsers || 0}</h4>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-yellow-200">
                            <p class="text-sm font-medium text-gray-600">In Progress</p>
                            <h4 class="text-xl font-bold mt-1">${data.inProgressUsers || 0}</h4>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-gray-200">
                            <p class="text-sm font-medium text-gray-600">Not Started</p>
                            <h4 class="text-xl font-bold  mt-1">${data.notStartedUsers || 0}</h4>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                    <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Words Completed</th>
                                    <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Words Remaining</th>
                                    <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion %</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.userData && data.userData.length > 0
                                    ? data.userData.map(user => `
                                        <tr>
                                            <td class="py-2 px-4">${user.username}</td>
                                            <td class="py-2 px-4">${user.completed_words}</td>
                                            <td class="py-2 px-4">${user.remaining_words}</td>
                                            <td class="py-2 px-4">${user.completion_percentage}%</td>
                                        </tr>
                                    `).join('')
                                    : '<tr><td colspan="4" class="py-4 px-4 text-center text-gray-500">No user data available</td></tr>'
                                }
                            </tbody>
                        </table>
                    </div>
                `;
            }

            // --- Add the stats section to the DOM ---
            // Append it to the main content area that should be printed
            reportContentElement.appendChild(userStatsSection);

            // --- Trigger browser print ---
            // Hide elements manually just before print (optional redundancy for @media print)
            // if (printButtonContainer) printButtonContainer.style.display = 'none';
            // if (headerElement) headerElement.style.display = 'none';
            // if (sidebarElement) sidebarElement.style.display = 'none';

            window.print(); // Open the print dialog

            // --- Clean up after print dialog is closed ---
            cleanupAfterPrint();

        },
        error: function(xhr, status, error) {
            console.error('Error fetching user completion statistics:', error);
            userStatsSection.innerHTML = `
                <h3 class="text-lg font-semibold text-gray-800 mb-4">User Completion Statistics (${timePeriodTitle})</h3>
                <p>Error loading user data: ${error}</p>
            `;
            // Add section to DOM even on error, so user sees the error message
            reportContentElement.appendChild(userStatsSection);

            // Trigger print even on error to show the error message
            window.print();

            // Clean up
            cleanupAfterPrint();
        }
    });
}

// Remove the old function if it's no longer needed
// function printReportAsPdf() { ... }