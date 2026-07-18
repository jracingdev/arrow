$ErrorActionPreference = 'Stop'
$root = 'D:\arrow'
$utf8 = New-Object System.Text.UTF8Encoding $false

function Load-JsonMap([string]$path) {
  $json = [IO.File]::ReadAllText($path, $utf8)
  $map = New-Object 'System.Collections.Generic.Dictionary[string,string]'
  foreach ($m in [regex]::Matches($json, '"((?:\\.|[^"\\])*)"\s*:\s*"((?:\\.|[^"\\])*)"')) {
    $k = [regex]::Unescape($m.Groups[1].Value)
    $v = [regex]::Unescape($m.Groups[2].Value)
    if (-not $map.ContainsKey($k)) { $map[$k] = $v }
  }
  return $map
}

$menu = Load-JsonMap (Join-Path $root 'scripts\menu-keys-ptbr.json')
$onboard = Load-JsonMap (Join-Path $root 'scripts\onboarding-ptbr.json')
Write-Host ("menu keys=" + $menu.Count + " onboard=" + $onboard.Count)

foreach ($panel in @('admin','store','website')) {
  $path = Join-Path $root ("web\" + $panel + "\resources\lang\pt_br\lang.php")
  if (-not (Test-Path $path)) { continue }
  $txt = [IO.File]::ReadAllText($path, $utf8)
  foreach ($key in $menu.Keys) {
    $val = $menu[$key]
    $escaped = $val.Replace('\','\\').Replace("'","\'")
    $pat = "(?m)('" + [regex]::Escape($key) + "'\s*=>\s*)(['""])(.*?)(\2)"
    $txt = [regex]::Replace($txt, $pat, {
      param($m)
      return ($m.Groups[1].Value + "'" + $escaped + "'")
    })
  }
  [IO.File]::WriteAllText($path, $txt, $utf8)
  $alias = Join-Path $root ("web\" + $panel + "\resources\lang\pt_BR")
  New-Item -ItemType Directory -Path $alias -Force | Out-Null
  Copy-Item ((Join-Path $root ("web\" + $panel + "\resources\lang\pt_br")) + "\*") $alias -Force
  Write-Host ("OK " + $panel)
}

$colPath = Join-Path $root 'firebase\import-export\collections.json'
$col = [IO.File]::ReadAllText($colPath, $utf8)
$replaced = 0
foreach ($k in $onboard.Keys) {
  if ($col.Contains($k)) {
    $col = $col.Replace($k, $onboard[$k])
    $replaced++
    Write-Host ("ONBOARD hit: " + $k.Substring(0, [Math]::Min(40, $k.Length)))
  } else {
    Write-Host ("MISS: " + $k.Substring(0, [Math]::Min(40, $k.Length)))
  }
}
# Fix driver setup description by prefix match (handles curly/mojibake apostrophe)
$ptSetup = 'Informe seus dados, verifique os documentos e esteja pronto para comecar.'
if ($onboard.ContainsKey('Provide your details, verify documents, and you''re ready to hit the road.')) {
  $ptSetup = $onboard['Provide your details, verify documents, and you''re ready to hit the road.']
}
# Try without doubled quote key - load from known PT in menu is wrong; use literal from onboard values if present
foreach ($k in $onboard.Keys) {
  if ($k.StartsWith('Provide your details')) { $ptSetup = $onboard[$k]; break }
}
$col2 = [regex]::Replace($col,
  'Provide your details, verify documents, and you.+?ready to hit the road\.',
  [System.Text.RegularExpressions.MatchEvaluator]{ param($m) return $ptSetup })
if ($col2 -ne $col) { $replaced++; $col = $col2; Write-Host 'ONBOARD regex setup desc' }
[IO.File]::WriteAllText($colPath, $col, $utf8)
Write-Host ("onboard replaced count=" + $replaced)
Write-Host DONE
