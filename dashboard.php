<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'User';

// Get all habits for the user
$stmt = $conn->prepare("SELECT * FROM habits WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$habits_result = $stmt->get_result();
$habits = $habits_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get completion stats for each habit
$habits_with_stats = [];
foreach ($habits as $habit) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total, 
                           SUM(CASE WHEN log_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as week_count,
                           SUM(CASE WHEN log_date = CURDATE() THEN 1 ELSE 0 END) as today_done
                           FROM habit_logs WHERE habit_id = ?");
    $stmt->bind_param("i", $habit['id']);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $habit['stats'] = $stats;
    $habits_with_stats[] = $habit;
}

// Get today's completion count
$today_stmt = $conn->prepare("SELECT COUNT(DISTINCT habit_id) as today_count 
                              FROM habit_logs 
                              WHERE habit_id IN (SELECT id FROM habits WHERE user_id = ?) 
                              AND log_date = CURDATE()");
$today_stmt->bind_param("i", $user_id);
$today_stmt->execute();
$today_stats = $today_stmt->get_result()->fetch_assoc();
$today_stmt->close();

// Get data for charts
$chart_habits = [];
$chart_completions = [];
$total_completions = 0;

foreach ($habits_with_stats as $habit) {
    $chart_habits[] = htmlspecialchars($habit['habit_name']);
    $completion_count = intval($habit['stats']['total'] ?? 0);
    $chart_completions[] = $completion_count;
    $total_completions += $completion_count;
}

// Get last 7 days completion data for line chart
$last_7_days = [];
$last_7_days_labels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D', strtotime("-$i days"));
    $last_7_days_labels[] = $day_name;
    
    $day_stmt = $conn->prepare("SELECT COUNT(*) as count 
                                FROM habit_logs 
                                WHERE habit_id IN (SELECT id FROM habits WHERE user_id = ?) 
                                AND log_date = ?");
    $day_stmt->bind_param("is", $user_id, $date);
    $day_stmt->execute();
    $day_result = $day_stmt->get_result()->fetch_assoc();
    $last_7_days[] = intval($day_result['count'] ?? 0);
    $day_stmt->close();
}

// Display messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success']);
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Habit Tracker</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <div class="container">
        <header class="dashboard-header">
            <div>
                <h1>Welcome, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
                <p class="subtitle">Track your progress and build better habits</p>
            </div>
            <a href="logout.php" class="btn btn-secondary">Logout</a>
        </header>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="stats-card">
            <div class="stat-item">
                <div class="stat-number"><?php echo count($habits); ?></div>
                <div class="stat-label">Total Habits</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $today_stats['today_count'] ?? 0; ?></div>
                <div class="stat-label">Completed Today</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $total_completions; ?></div>
                <div class="stat-label">Total Completions</div>
            </div>
        </div>

        <?php if (!empty($habits_with_stats)): ?>
        <div class="charts-section">
            <div class="charts-grid">
                <div class="card chart-card">
                    <h2>Habit Completions (Bar Chart)</h2>
                    <canvas id="barChart"></canvas>
                </div>
                <div class="card chart-card">
                    <h2>Completion Distribution (Pie Chart)</h2>
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
            <div class="card chart-card">
                <h2>Last 7 Days Activity</h2>
                <canvas id="lineChart"></canvas>
            </div>
        </div>
        <?php endif; ?>

        <div class="card">
            <h2>Add New Habit</h2>
            <form action="add_habit.php" method="POST" class="add-habit-form">
                <div class="form-group">
                    <input type="text" name="habit" placeholder="Enter habit name (e.g., Drink 8 glasses of water)" required maxlength="100">
                    <button type="submit" name="add" class="btn btn-primary">+ Add Habit</button>
                </div>
            </form>
        </div>

        <div class="habits-section">
            <h2>Your Habits</h2>
            
            <?php if (empty($habits_with_stats)): ?>
                <div class="empty-state">
                    <p>No habits yet. Start by adding your first habit above! 🎯</p>
                </div>
            <?php else: ?>
                <div class="habits-grid">
                    <?php foreach ($habits_with_stats as $habit): ?>
                        <div class="habit-card">
                            <div class="habit-header">
                                <h3><?php echo htmlspecialchars($habit['habit_name']); ?></h3>
                                <div class="habit-header-actions">
                                    <a href="edit_habit.php?id=<?php echo $habit['id']; ?>" 
                                       class="edit-btn" 
                                       title="Edit habit">✏️</a>
                                    <a href="delete_habit.php?id=<?php echo $habit['id']; ?>" 
                                       class="delete-btn" 
                                       onclick="return confirm('Are you sure you want to delete this habit?');"
                                       title="Delete habit">🗑️</a>
                                </div>
                            </div>
                            
                            <div class="habit-stats">
                                <div class="stat-mini">
                                    <span class="stat-label-mini">Total Completions</span>
                                    <span class="stat-value"><?php echo $habit['stats']['total'] ?? 0; ?></span>
                                </div>
                                <div class="stat-mini">
                                    <span class="stat-label-mini">This Week</span>
                                    <span class="stat-value"><?php echo $habit['stats']['week_count'] ?? 0; ?></span>
                                </div>
                            </div>
                            
                            <div class="habit-actions">
                                <?php if ($habit['stats']['today_done'] > 0): ?>
                                    <span class="btn btn-success btn-disabled">✓ Done Today</span>
                                <?php else: ?>
                                    <a href="mark_done.php?id=<?php echo $habit['id']; ?>" class="btn btn-success">Mark as Done</a>
                                <?php endif; ?>
                                <button type="button" class="btn btn-secondary btn-small" onclick="openDatePicker(<?php echo $habit['id']; ?>, '<?php echo htmlspecialchars($habit['habit_name']); ?>', '<?php echo $habit['created_at']; ?>')">
                                    📅 Mark for Date
                                </button>
                            </div>
                            
                            <div class="habit-date">
                                Started: <?php echo date('M d, Y', strtotime($habit['created_at'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Date Picker Modal -->
    <div id="datePickerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Mark Habit for Date</h2>
                <span class="modal-close" onclick="closeDatePicker()">&times;</span>
            </div>
            <div class="modal-body">
                <p>Select a date to mark <strong id="selectedHabitName"></strong> as done. You can backfill historical data by selecting any past date (up to 1 year ago).</p>
                <input type="hidden" id="selectedHabitId" value="">
                <div class="form-group">
                    <label for="selectedDate">Select Date</label>
                    <input type="date" id="selectedDate" class="date-input" required>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeDatePicker()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitDateMark()">Mark as Done</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prepare data from PHP
        const habitNames = <?php echo json_encode($chart_habits); ?>;
        const habitCompletions = <?php echo json_encode($chart_completions); ?>;
        const last7DaysLabels = <?php echo json_encode($last_7_days_labels); ?>;
        const last7DaysData = <?php echo json_encode($last_7_days); ?>;

        // Generate colors for charts
        const colors = [
            'rgba(99, 102, 241, 0.8)',   // indigo
            'rgba(139, 92, 246, 0.8)',   // purple
            'rgba(16, 185, 129, 0.8)',   // green
            'rgba(245, 158, 11, 0.8)',   // amber
            'rgba(239, 68, 68, 0.8)',    // red
            'rgba(59, 130, 246, 0.8)',   // blue
            'rgba(236, 72, 153, 0.8)',   // pink
            'rgba(34, 197, 94, 0.8)',    // emerald
            'rgba(251, 146, 60, 0.8)',   // orange
            'rgba(168, 85, 247, 0.8)'    // violet
        ];

        const borderColors = colors.map(color => color.replace('0.8', '1'));

        // Bar Chart
        if (habitNames.length > 0) {
            const barCtx = document.getElementById('barChart');
            if (barCtx) {
                new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: habitNames,
                        datasets: [{
                            label: 'Total Completions',
                            data: habitCompletions,
                            backgroundColor: colors.slice(0, habitNames.length),
                            borderColor: borderColors.slice(0, habitNames.length),
                            borderWidth: 2,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 14, weight: 'bold' },
                                bodyFont: { size: 13 }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
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
            }

            // Pie Chart
            const pieCtx = document.getElementById('pieChart');
            if (pieCtx) {
                new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: habitNames,
                        datasets: [{
                            data: habitCompletions,
                            backgroundColor: colors.slice(0, habitNames.length),
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { size: 12 },
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${label}: ${value} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Line Chart (Last 7 Days)
            const lineCtx = document.getElementById('lineChart');
            if (lineCtx) {
                new Chart(lineCtx, {
                    type: 'line',
                    data: {
                        labels: last7DaysLabels,
                        datasets: [{
                            label: 'Daily Completions',
                            data: last7DaysData,
                            borderColor: 'rgba(99, 102, 241, 1)',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleFont: { size: 14, weight: 'bold' },
                                bodyFont: { size: 13 }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
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
            }
        }

        // Date Picker Modal Functions
        function openDatePicker(habitId, habitName, createdDate) {
            const modal = document.getElementById('datePickerModal');
            const habitNameSpan = document.getElementById('selectedHabitName');
            const dateInput = document.getElementById('selectedDate');
            const habitIdInput = document.getElementById('selectedHabitId');
            
            habitNameSpan.textContent = habitName;
            habitIdInput.value = habitId;
            
            // Set max date to today
            const today = new Date().toISOString().split('T')[0];
            dateInput.max = today;
            
            // Allow dates up to 1 year ago (for backfilling historical data)
            const oneYearAgo = new Date();
            oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1);
            dateInput.min = oneYearAgo.toISOString().split('T')[0];
            
            // Set default to today
            dateInput.value = today;
            
            modal.style.display = 'flex';
        }

        function closeDatePicker() {
            const modal = document.getElementById('datePickerModal');
            modal.style.display = 'none';
        }

        function submitDateMark() {
            const habitId = document.getElementById('selectedHabitId').value;
            const selectedDate = document.getElementById('selectedDate').value;
            
            if (selectedDate && habitId) {
                window.location.href = `mark_done.php?id=${habitId}&date=${selectedDate}`;
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('datePickerModal');
            if (event.target == modal) {
                closeDatePicker();
            }
        }
    </script>
</body>
</html>
