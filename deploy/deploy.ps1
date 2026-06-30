<#
.SYNOPSIS
    Sincroniza o monorepo Arrow para as pastas de produção (aaPanel).
.PARAMETER ServerRoot
    Raiz dos sites no servidor. Padrão: D:\wwwroot (ajuste conforme ambiente).
.PARAMETER RepoRoot
    Raiz do monorepo. Padrão: pasta pai de deploy/.
#>
param(
    [string]$ServerRoot = "D:\wwwroot",
    [string]$RepoRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
)

$ErrorActionPreference = "Stop"

$maps = @(
    @{ Source = "web\website"; Dest = "arrow_app_br" },
    @{ Source = "web\landing"; Dest = "lp_arrow_app_br" },
    @{ Source = "web\store";   Dest = "store_arrow_app_br" },
    @{ Source = "web\admin";   Dest = "admin_arrow_app_br" }
)

$excludeDirs = @("vendor", "node_modules", ".git")
$excludeFiles = @(".env")

function Sync-Folder {
    param([string]$Source, [string]$Dest)

    if (-not (Test-Path $Source)) {
        Write-Warning "Origem não encontrada: $Source"
        return
    }

    if (-not (Test-Path $Dest)) {
        New-Item -ItemType Directory -Path $Dest -Force | Out-Null
    }

    robocopy $Source $Dest /MIR /XD $excludeDirs /XF $excludeFiles `
        "storage\logs" "bootstrap\cache" `
        /NFL /NDL /NJH /NJS /nc /ns /np | Out-Null

    if ($LASTEXITCODE -ge 8) { throw "Robocopy falhou com código $LASTEXITCODE" }
    Write-Host "OK: $Source -> $Dest"
}

Write-Host "Repo: $RepoRoot"
Write-Host "Destino: $ServerRoot"
Write-Host ""

foreach ($map in $maps) {
    $src = Join-Path $RepoRoot $map.Source
    $dst = Join-Path $ServerRoot $map.Dest
    Sync-Folder -Source $src -Dest $dst
}

Write-Host ""
Write-Host "Deploy de arquivos concluído."
Write-Host "Próximo passo: composer install e php artisan config:cache em cada painel Laravel."
