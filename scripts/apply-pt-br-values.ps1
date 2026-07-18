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
# Remover chaves curtas que quebram substrings (Tax em Taxa)
foreach ($bad in @('Tax','Save','Edit','Add','Open','Close','To','of','or','No','All','New','Off','Pay','Call','Chat','Date','Time','Rate','Give','Both','Done','Back','Next','Skip','Home','Menu','Name','Item','Items','User','Order')) {
  if ($map.ContainsKey($bad)) { [void]$map.Remove($bad) }
}
Write-Host ("Loaded " + $map.Count)

function Translate-Value([string]$val) {
  if ($map.ContainsKey($val)) { return $map[$val] }
  return $val
}

function Convert-Php([string]$src, [string]$dst) {
  $content = [System.IO.File]::ReadAllText($src, [System.Text.UTF8Encoding]::new($false))
  $pattern = "(?s)(=>\s*)(['""])((?:\\.|(?!\2).)*)(\2)"
  $evaluator = {
    param($m)
    $prefix = $m.Groups[1].Value
    $q = $m.Groups[2].Value
    $val = $m.Groups[3].Value
    $plain = (($val -replace "\\'", "'") -replace '\\"', '"')
    $tr = Translate-Value $plain
    if ($q -eq "'") {
      $escaped = (($tr -replace '\\', '\\') -replace "'", "\'")
      return ($prefix + "'" + $escaped + "'")
    } else {
      $escaped = (($tr -replace '\\', '\\') -replace '"', '\"')
      return ($prefix + '"' + $escaped + '"')
    }
  }
  $out = [regex]::Replace($content, $pattern, $evaluator)
  [System.IO.File]::WriteAllText($dst, $out, [System.Text.UTF8Encoding]::new($false))
  Write-Host ("PHP " + $dst)
}

function Convert-Dart([string]$src, [string]$dst, [string]$mapName) {
  $content = [System.IO.File]::ReadAllText($src, [System.Text.UTF8Encoding]::new($false))
  $content = $content.Replace('const Map<String, String> enUS', ("const Map<String, String> " + $mapName))
  # Só substitui o VALOR após ": " — chave permanece em inglês
  $pattern = "((['""])(?:\\.|(?!\2).)*\2\s*:\s*)(['""])((?:\\.|(?!\3).)*)(\3)"
  $evaluator = {
    param($m)
    $left = $m.Groups[1].Value
    $vq = $m.Groups[3].Value
    $val = $m.Groups[4].Value
    $plain = (($val -replace "\\'", "'") -replace '\\"', '"')
    $tr = Translate-Value $plain
    if ($vq -eq "'") {
      $escaped = (($tr -replace '\\', '\\') -replace "'", "\'")
    } else {
      $escaped = (($tr -replace '\\', '\\') -replace '"', '\"')
    }
    return ($left + $vq + $escaped + $vq)
  }
  $out = [regex]::Replace($content, $pattern, $evaluator)
  [System.IO.File]::WriteAllText($dst, $out, [System.Text.UTF8Encoding]::new($false))
  Write-Host ("DART " + $dst)
}

foreach ($p in @('admin','store','website')) {
  $en = Join-Path $root ("web\$p\resources\lang\en")
  $pt = Join-Path $root ("web\$p\resources\lang\pt_br")
  New-Item -ItemType Directory -Path $pt -Force | Out-Null
  Get-ChildItem $en -File -Filter *.php | ForEach-Object {
    Convert-Php $_.FullName (Join-Path $pt $_.Name)
  }
  Get-ChildItem $en -File | Where-Object { $_.Extension -ne '.php' } | ForEach-Object {
    Copy-Item $_.FullName (Join-Path $pt $_.Name) -Force
  }
}

Convert-Dart (Join-Path $root 'apps\customer\lib\lang\app_en.dart') (Join-Path $root 'apps\customer\lib\lang\app_pt.dart') 'ptBR'
Convert-Dart (Join-Path $root 'apps\store\lib\lang\app_en.dart') (Join-Path $root 'apps\store\lib\lang\app_pt.dart') 'ptPO'
Convert-Dart (Join-Path $root 'apps\driver\lib\lang\app_en.dart') (Join-Path $root 'apps\driver\lib\lang\app_pt.dart') 'ptPO'
Write-Host DONE
