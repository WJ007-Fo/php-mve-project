<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>🗃️ Database Schema</h1>
        <span class="badge bg-primary fs-5">DB: <?= $dbName ?></span>
    </div>

    <?php foreach ($tables as $table): ?>
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">Table: <strong><?= $table['name'] ?></strong></h4>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Field</th>
                            <th>Type</th>
                            <th>Null</th>
                            <th>Key</th>
                            <th>Default</th>
                            <th>Extra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($table['columns'] as $col): ?>
                            <tr>
                                <td class="fw-bold <?= $col['Key'] === 'PRI' ? 'text-danger' : '' ?>">
                                    <?= $col['Field'] ?>
                                    <?= $col['Key'] === 'PRI' ? ' 🔑' : '' ?>
                                </td>
                                <td><code><?= $col['Type'] ?></code></td>
                                <td><span class="badge <?= $col['Null'] === 'YES' ? 'bg-success' : 'bg-secondary' ?>"><?= $col['Null'] ?></span></td>
                                <td class="text-warning fw-bold"><?= $col['Key'] ?></td>
                                <td class="text-muted"><?= $col['Default'] ?? 'NULL' ?></td>
                                <td class="small text-secondary"><?= $col['Extra'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>