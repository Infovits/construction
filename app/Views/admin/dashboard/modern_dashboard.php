<!-- app/Views/admin/dashboard/modern_dashboard.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Construction Dashboard</title>
    <link href="<?= base_url('assets/vendors/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
            font-family: 'Inter', 'Roboto', Arial, sans-serif;
        }
        .brand-bg {
            background: #1e293b;
            color: #fff;
        }
        .card-highlight {
            background: linear-gradient(135deg, #f59e42 0%, #fbbf24 100%);
            color: #fff;
        }
        .icon-bg {
            background: #f1f5f9;
            border-radius: 50%;
            padding: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .shadow-lg {
            box-shadow: 0 10px 20px rgba(30,41,59,0.08), 0 2px 4px rgba(30,41,59,0.06);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg brand-bg mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="bi bi-building"></i> ConstructPro
            </a>
        </div>
    </nav>
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card card-highlight shadow-lg border-0">
                    <div class="card-body d-flex align-items-center">
                        <span class="icon-bg me-3"><i class="bi bi-people-fill fs-3"></i></span>
                        <div>
                            <h6 class="mb-0">Active Workers</h6>
                            <h3 class="fw-bold mb-0">128</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-lg border-0">
                    <div class="card-body d-flex align-items-center">
                        <span class="icon-bg me-3"><i class="bi bi-hammer fs-3 text-warning"></i></span>
                        <div>
                            <h6 class="mb-0">Ongoing Projects</h6>
                            <h3 class="fw-bold mb-0">12</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-lg border-0">
                    <div class="card-body d-flex align-items-center">
                        <span class="icon-bg me-3"><i class="bi bi-clipboard-data fs-3 text-primary"></i></span>
                        <div>
                            <h6 class="mb-0">Tasks Today</h6>
                            <h3 class="fw-bold mb-0">54</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-lg border-0">
                    <div class="card-body d-flex align-items-center">
                        <span class="icon-bg me-3"><i class="bi bi-exclamation-triangle-fill fs-3 text-danger"></i></span>
                        <div>
                            <h6 class="mb-0">Incidents</h6>
                            <h3 class="fw-bold mb-0">2</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-white border-0 fw-bold fs-5">Project Progress</div>
                    <div class="card-body">
                        <!-- Example progress bars -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Site A</span>
                                <span>80%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: 80%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Site B</span>
                                <span>60%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: 60%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>Site C</span>
                                <span>35%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: 35%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-white border-0 fw-bold fs-5">Recent Incidents</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="bi bi-exclamation-circle-fill text-danger me-2"></i>
                                Worker injury at Site A <span class="badge bg-danger ms-auto">Critical</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                Equipment malfunction at Site B <span class="badge bg-warning ms-auto">Moderate</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-header bg-white border-0 fw-bold fs-5">Quick Actions</div>
                    <div class="card-body d-grid gap-2">
                        <a href="#" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-plus-circle me-2"></i>New Task</a>
                        <a href="#" class="btn btn-warning btn-lg rounded-pill"><i class="bi bi-upload me-2"></i>Upload Report</a>
                        <a href="#" class="btn btn-success btn-lg rounded-pill"><i class="bi bi-person-plus me-2"></i>Add Worker</a>
                    </div>
                </div>
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-white border-0 fw-bold fs-5">Upcoming Deadlines</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="bi bi-calendar-event text-primary me-2"></i>
                                Pour concrete at Site A <span class="badge bg-primary ms-auto">Tomorrow</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="bi bi-calendar-event text-primary me-2"></i>
                                Safety audit at Site B <span class="badge bg-secondary ms-auto">2 days</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="text-center py-4 mt-5 text-muted small">
        &copy; 2025 ConstructPro. All rights reserved.
    </footer>
</body>
</html>

