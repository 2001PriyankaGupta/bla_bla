@extends('admin.layouts.master')
@section('title')
    Dashboard
@endsection

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #fff;
        }

        .dashboard-header {
            padding: 30px 15px 5px 15px;
        }

        .dashboard-header h2 {
            font-weight: 700;
        }

        .dashboard-header p {
            margin-bottom: 0;
            color: #777;
        }

        .quick-actions .btn {
            min-width: 293px;
            font-weight: 600;
            border-radius: 7px;
            margin-right: 10px;
            margin-bottom: 15px;
        }

        .quick-actions .btn-primary {
            background: #19b61e;
            border-color: #19b61e;
        }

        .dashboard-card {
            border: 1px solid #eee;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 25px 18px 18px 18px;
            margin-bottom: 15px;
        }

        .dashboard-card .value {
            font-size: 2.1rem;
            font-weight: 600;
            color: #222;
        }

        .dashboard-card .badge {
            font-size: 14px;
            margin-left: 6px;
        }

        .dashboard-card .label {
            color: #424242;
            font-weight: 600;
            font-size: 1rem;
        }

        .dashboard-card.negative .value {
            color: #e84118;
        }

        .dashboard-card .sub-label {
            color: #17a256;
            font-size: 13px;
            font-weight: 500;
        }

        .dashboard-card.negative .sub-label {
            color: #f44336;
        }

        .mini-card {
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 18px 15px;
            min-height: 150px;
            margin-bottom: 15px;
        }

        .mini-chart-title {
            font-weight: 700;
            color: #19b61e;
            font-size: 20px;
            margin-bottom: 6px;
        }

        .mini-chart-trend {
            font-size: 16px;
            font-weight: 500;
            color: #19b61e;
        }

        .mini-chart-trend.negative {
            color: #e84118;
        }
    </style>


@section('content')
    <div class="container-fluid mt-3">
        <!-- Header -->
        <div class="dashboard-header">
            <h1 style="color: black!important;">Dashboard Overview</h1>
            <p>Welcome back, bla bla! Here's a snapshot of your platform's performance.</p>
            <h6 style="margin-top: 17px;color:black;font-size:20px;">Quick Actions</h6>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions mb-4">
            <a href="{{ route('admin.rides.index') }}" class="btn btn-primary">View Ride</a>
            <a href="{{ route('admin.support.index') }}" class="btn btn-outline-success">View Pending Tickets</a>
            <!-- <a href="{{ route('admin.fare-promo.index') }}" class="btn btn-outline-success">Adjust Fare</a> -->
        </div>

        <!-- Metrics -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-6">
                <div class="dashboard-card ">
                    <div class="value">{{ number_format($totalUsers) }}</div>
                    <div class="label">Total Users</div>
                    <div class="sub-label {{ $userGrowth < 0 ? 'text-danger' : '' }}">{{ $userGrowth >= 0 ? '+' : '' }}{{ number_format($userGrowth, 1) }}%</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="dashboard-card ">
                    <div class="value">{{ number_format($totalRides) }}</div>
                    <div class="label">Total Rides</div>
                    <div class="sub-label {{ $rideGrowth < 0 ? 'text-danger' : '' }}">{{ $rideGrowth >= 0 ? '+' : '' }}{{ number_format($rideGrowth, 1) }}%</div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="dashboard-card ">
                    <div class="value">Rs {{ number_format($totalRevenue, 0) }}</div>
                    <div class="label">Total Revenue</div>
                    <div class="sub-label {{ $revenueGrowth < 0 ? 'text-danger' : '' }}">{{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}%</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <div class="dashboard-card negative ">
                    <div class="value">Rs {{ number_format($totalRefunds, 0) }}</div>
                    <div class="label">Total Refunds</div>
                    <div class="sub-label">Verified</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-4 col-6">
                <div class="dashboard-card ">
                    <div class="value">{{ $pendingComplaints }}</div>
                    <div class="label">Pending Complaints</div>
                    <div class="sub-label text-warning">Needs Action</div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Ride Trends -->
            <div class="col-lg-4 col-md-6">
                <div class="mini-card">
                    <div class="mini-chart-title">Ride Trends</div>
                    <div class="mini-chart-trend">+12%</div>
                    <div class="text-muted mb-1" style="font-size:13px">Last 30 Days +12%</div>
                    <canvas id="rideTrendsChart" height="80"></canvas>
                </div>
            </div>
            <!-- Peak Hours -->
            <div class="col-lg-4 col-md-6">
                <div class="mini-card">
                    <div class="mini-chart-title">Peak Hours</div>
                    <div class="mini-chart-trend">+8%</div>
                    <div class="text-muted mb-1" style="font-size:13px">Last 30 Days +8%</div>
                    <canvas id="peakHoursChart" height="80"></canvas>
                </div>
            </div>
            <!-- User Growth -->
            <div class="col-lg-4 col-md-12">
                <div class="mini-card">
                    <div class="mini-chart-title">User Growth</div>
                    <div class="mini-chart-trend">+15%</div>
                    <div class="text-muted mb-1" style="font-size:13px">Last 30 Days +15%</div>
                    <canvas id="userGrowthChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Fake Data: Replace with your backend data as needed
        var rideTrendsLabels = {!! json_encode($chartLabels) !!};
        var rideTrendsData = {!! json_encode($chartData) !!};

        var peakHoursLabels = ['12AM', '3AM', '6AM', '9AM', '12PM'];
        var peakHoursData = [30, 45, 80, 60, 100];

        var userGrowthLabels = {!! json_encode($userGrowthLabels) !!};
        var userGrowthData = {!! json_encode($userGrowthData) !!};

        // Ride Trends Line Chart
        new Chart(document.getElementById('rideTrendsChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: rideTrendsLabels,
                datasets: [{
                    data: rideTrendsData,
                    borderColor: '#19b61e',
                    backgroundColor: 'rgba(25,182,30,0.10)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#19b61e'
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Peak Hours Bar Chart
        new Chart(document.getElementById('peakHoursChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: peakHoursLabels,
                datasets: [{
                    data: peakHoursData,
                    backgroundColor: '#19b61e'
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // User Growth Bar Chart
        new Chart(document.getElementById('userGrowthChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: userGrowthLabels,
                datasets: [{
                    data: userGrowthData,
                    backgroundColor: '#19b61e'
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
