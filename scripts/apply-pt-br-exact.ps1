$ErrorActionPreference = 'Stop'
$root = 'D:\arrow'
$dictPath = Join-Path $root 'scripts\pt-br-exact.json'
$json = [System.IO.File]::ReadAllText($dictPath, [System.Text.UTF8Encoding]::new($false))

$map = New-Object 'System.Collections.Generic.Dictionary[string,string]'
$rx = [regex] '"((?:\\.|[^"\\])*)"\s*:\s*"((?:\\.|[^"\\])*)"'
foreach ($m in $rx.Matches($json)) {
  $k = [regex]::Unescape($m.Groups[1].Value)
  $v = [regex]::Unescape($m.Groups[2].Value)
  if (-not $map.ContainsKey($k)) { $map[$k] = $v }
}
Write-Host ("Loaded " + $map.Count + " phrases")

function Apply-Exact([string]$content) {
  $keys = New-Object System.Collections.Generic.List[string]
  foreach ($k in $map.Keys) { [void]$keys.Add($k) }
  $sorted = $keys | Sort-Object { -$_.Length }
  foreach ($k in $sorted) {
    if ($content.Contains($k)) {
      $content = $content.Replace($k, $map[$k])
    }
  }
  return $content
}

function Process-File([string]$path) {
  $raw = [System.IO.File]::ReadAllText($path, [System.Text.UTF8Encoding]::new($false))
  $out = Apply-Exact $raw
  [System.IO.File]::WriteAllText($path, $out, [System.Text.UTF8Encoding]::new($false))
  Write-Host ("OK " + $path)
}

$panels = @('admin','store','website')
foreach ($p in $panels) {
  $pt = Join-Path $root ("web\$p\resources\lang\pt_br")
  if (-not (Test-Path $pt)) { New-Item -ItemType Directory -Path $pt -Force | Out-Null }
  Get-ChildItem $pt -File -Filter *.php | ForEach-Object { Process-File $_.FullName }
}

function Ensure-FlutterPt([string]$enPath, [string]$ptPath, [string]$fromName, [string]$toName) {
  $src = [System.IO.File]::ReadAllText($enPath, [System.Text.UTF8Encoding]::new($false))
  $src = $src.Replace(("const Map<String, String> " + $fromName), ("const Map<String, String> " + $toName))
  $src = Apply-Exact $src
  [System.IO.File]::WriteAllText($ptPath, $src, [System.Text.UTF8Encoding]::new($false))
  Write-Host ("OK " + $ptPath)
}

Ensure-FlutterPt (Join-Path $root 'apps\customer\lib\lang\app_en.dart') (Join-Path $root 'apps\customer\lib\lang\app_pt.dart') 'enUS' 'ptBR'
Ensure-FlutterPt (Join-Path $root 'apps\store\lib\lang\app_en.dart') (Join-Path $root 'apps\store\lib\lang\app_pt.dart') 'enUS' 'ptPO'
Ensure-FlutterPt (Join-Path $root 'apps\driver\lib\lang\app_en.dart') (Join-Path $root 'apps\driver\lib\lang\app_pt.dart') 'enUS' 'ptPO'

Write-Host 'DONE exact overlay'
