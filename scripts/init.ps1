param(
    [string]$PhpPath = "php"
)

$ErrorActionPreference = "Stop"

function Run-Step {
    param(
        [string]$Title,
        [scriptblock]$Action
    )

    Write-Host "==> $Title"
    & $Action
    Write-Host "OK: $Title" -ForegroundColor Green
}

try {
    # Inicializa rutas de trabajo.
    $scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
    $root = Split-Path -Parent $scriptDir
    Set-Location $root

    # Verifica PHP.
    Run-Step "Checking PHP availability" {
        & $PhpPath -v | Select-Object -First 1 | Out-Host
    }

    # Ejecuta migracion de BD.
    Run-Step "Running DB migration" {
        & $PhpPath ".\api\tools\migrate.php"
        if ($LASTEXITCODE -ne 0) {
            throw "Migration failed with exit code $LASTEXITCODE"
        }
    }

    # Verifica estado de BD.
    Run-Step "Running DB health check" {
        & $PhpPath ".\api\tools\check_db.php"
        if ($LASTEXITCODE -ne 0) {
            throw "Health check failed with exit code $LASTEXITCODE"
        }
    }

    Write-Host "`nInit completed successfully." -ForegroundColor Green
    exit 0
} catch {
    Write-Error $_
    exit 1
}
