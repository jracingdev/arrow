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

function Force-KeyValues([string]$langPath, $keyMap) {
  $txt = [IO.File]::ReadAllText($langPath, $utf8)
  $added = New-Object System.Collections.Generic.List[string]
  foreach ($key in $keyMap.Keys) {
    $val = $keyMap[$key]
    $escaped = $val.Replace('\', '\\').Replace("'", "\'")
    $pat = "(?m)('" + [regex]::Escape($key) + "'\s*=>\s*)(['""])(.*?)(\2)"
    if ([regex]::IsMatch($txt, $pat)) {
      $txt = [regex]::Replace($txt, $pat, { param($m) return ($m.Groups[1].Value + "'" + $escaped + "'") })
    } else {
      # insert before closing ];
      $insert = "    '" + $key + "' => '" + $escaped + "'," + [Environment]::NewLine
      $txt = [regex]::Replace($txt, '(?m)^(\];\s*)$', ($insert + '$1'), 1)
      [void]$added.Add($key)
    }
  }
  [IO.File]::WriteAllText($langPath, $txt, $utf8)
  return $added
}

$critical = Load-JsonMap (Join-Path $root 'scripts\critical-ui-ptbr.json')
# also merge previous glossaries by VALUE for leftover English strings
$valueMap = New-Object 'System.Collections.Generic.Dictionary[string,string]'
foreach ($p in @('scripts\pt-br-exact.json','scripts\onboarding-ptbr.json','scripts\menu-keys-ptbr.json')) {
  $full = Join-Path $root $p
  if (-not (Test-Path $full)) { continue }
  $m = Load-JsonMap $full
  foreach ($k in $m.Keys) {
    # treat as EN value => PT when key looks like English sentence/title
    if (-not $valueMap.ContainsKey($k)) { $valueMap[$k] = $m[$k] }
  }
}
# critical values also as value map
foreach ($k in $critical.Keys) {
  # pull EN original from en file for value mapping later
}

Write-Host ("critical keys=" + $critical.Count + " value phrases=" + $valueMap.Count)

$panels = @('admin','store','website')
foreach ($panel in $panels) {
  $ptPath = Join-Path $root ("web\" + $panel + "\resources\lang\pt_br\lang.php")
  $enPath = Join-Path $root ("web\" + $panel + "\resources\lang\en\lang.php")
  if (-not (Test-Path $ptPath)) { continue }

  $added = Force-KeyValues $ptPath $critical
  Write-Host ($panel + " forced critical; newly added=" + $added.Count)

  # Translate remaining values that still equal EN
  if (Test-Path $enPath) {
    $enT = [IO.File]::ReadAllText($enPath, $utf8)
    $ptT = [IO.File]::ReadAllText($ptPath, $utf8)
    $enVals = @{}
    foreach ($m in [regex]::Matches($enT, "(?m)^\s*['""]([^'""]+)['""]\s*=>\s*(['""])((?:\\.|(?!\2).)*)(\2)")) {
      $enVals[$m.Groups[1].Value] = $m.Groups[3].Value
    }
    $changed = 0
    $ptT2 = [regex]::Replace($ptT, "(?m)^(\s*)(['""])([^'""]+)\2(\s*=>\s*)(['""])((?:\\.|(?!\5).)*)(\5)", {
      param($m)
      $indent = $m.Groups[1].Value
      $kq = $m.Groups[2].Value
      $key = $m.Groups[3].Value
      $arrow = $m.Groups[4].Value
      $vq = $m.Groups[5].Value
      $val = $m.Groups[6].Value
      $plain = (($val -replace "\\'", "'") -replace '\\"', '"')
      $newVal = $plain
      if ($enVals.ContainsKey($key) -and $enVals[$key] -eq $plain) {
        if ($valueMap.ContainsKey($plain)) {
          $newVal = $valueMap[$plain]
          $script:changed++
        }
      }
      if ($vq -eq "'") {
        $esc = $newVal.Replace('\','\\').Replace("'","\'")
      } else {
        $esc = $newVal.Replace('\','\\').Replace('"','\"')
      }
      return ($indent + $kq + $key + $kq + $arrow + $vq + $esc + $vq)
    })
    # fix changed counter - use outer
    [IO.File]::WriteAllText($ptPath, $ptT2, $utf8)
    Write-Host ($panel + " value-pass done")
  }

  # Ensure order_cancelled alias exists
  $aliasMap = New-Object 'System.Collections.Generic.Dictionary[string,string]'
  $aliasMap['order_cancelled'] = 'Pedido cancelado'
  [void](Force-KeyValues $ptPath $aliasMap)
}

Write-Host DONE
