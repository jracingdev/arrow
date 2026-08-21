$ErrorActionPreference = 'Stop'
$root = 'D:\arrow'
$utf8 = New-Object System.Text.UTF8Encoding $false

function Unescape-Php([string]$s) {
  return (($s -replace "\\'", "'") -replace '\\"', '"') -replace '\\\\', '\'
}

function Parse-PhpLang([string]$file) {
  $map = @{}
  $txt = [IO.File]::ReadAllText($file, $utf8)
  foreach ($m in [regex]::Matches($txt, "['""]([^'""]+)['""]\s*=>\s*(['""])((?:\\.|(?!\2).)*)\2")) {
    $map[$m.Groups[1].Value] = (Unescape-Php $m.Groups[3].Value)
  }
  return $map
}

function Parse-DartMap([string]$file) {
  $map = @{}
  $txt = [IO.File]::ReadAllText($file, $utf8)
  foreach ($m in [regex]::Matches($txt, "['""]((?:\\.|[^'""\\])*)['""]\s*:\s*['""]((?:\\.|[^'""\\])*)['""]")) {
    $k = (($m.Groups[1].Value -replace "\\'", "'") -replace '\\"', '"')
    $v = (($m.Groups[2].Value -replace "\\'", "'") -replace '\\"', '"')
    $map[$k] = $v
  }
  return $map
}

function Test-RealEnglish([string]$s) {
  if ([string]::IsNullOrWhiteSpace($s)) { return $false }
  # intentional keep
  if ($s -match '^(CPF|CNPJ|CEP|OTP|SMS|API|URL|ID|GPS|PDF|CSV|SKU|QR|PIN|HTML|JSON|USD|BRL|INR|VAT|GST|Arrow|Arrow|OK|SOS|iOS|Android|SSL|TLS|SMTP|MB|Km|mph|Total|Subtotal|Status|Menu|Chat|Manual|Individual|Extras|Item|JAN|MAR|JUN|JUL|NOV|PM|V|km|JPEG, PNG)$') { return $false }
  if ($s -match '^(PayPal|Stripe|RazorPay|Razorpay|Paytm|Xendit|MidTrans|MercadoPago|FlutterWave|Payfast|OrangePay|Apple Pay|Google Maps|OpenStreetMap|Foodie|Waze Map|Google Go)$') { return $false }
  if ($s -match 'Vandex|MapsWithMe|Google Play|App Store') { return $false }
  if ($s -match 'dd-MM-yyyy|HH:mm|MMM dd') { return $false }
  if ($s -notmatch '[A-Za-z]{3,}') { return $false }
  foreach ($ch in $s.ToCharArray()) {
    $code = [int][char]$ch
    if ($code -ge 192 -and $code -le 255) { return $false }
  }
  # common PT without accents still in BR UI
  if ($s -match '(?i)\b(pedido|salvar|celular|fatura|entrega|senha|usuario|configuracoes|obrigado|nao|sim|loja|motorista|cliente|imposto|endereco|telefone)\b') { return $false }
  # looks like English sentence/phrase if has common EN words
  if ($s -match '(?i)\b(the|and|please|your|order|save|delete|edit|create|update|search|loading|error|success|settings|delivery|driver|customer|invoice|payment|wallet|dashboard|profile|login|logout|password|email|phone|address|user|users|store|stores|total tax|vat|welcome|enter|select|manage|view|add|remove|confirm|cancel|submit|back|next|previous|home|cart|checkout|booking|available|unavailable|not found|try again)\b') { return $true }
  # Title Case multi-word English-looking
  if ($s -match '^[A-Z][a-z]+(\s+[A-Za-z]+){1,}$' -and $s -match '(?i)\b(Please|Order|Save|Delete|Edit|Settings|Delivery|Manage|View|Add|Remove|Confirm|Cancel|Welcome|Enter|Select)\b') { return $true }
  return $false
}

function Count-Identical($enMap, $ptMap, $label) {
  $identical = @()
  foreach ($k in $enMap.Keys) {
    if (-not $ptMap.ContainsKey($k)) { continue }
    if ($ptMap[$k] -eq $enMap[$k] -and (Test-RealEnglish $enMap[$k])) {
      $identical += $enMap[$k]
    }
  }
  Write-Host ("$label : identical-EN-looking = " + $identical.Count)
  if ($identical.Count -gt 0 -and $identical.Count -le 40) {
    $identical | Select-Object -First 40 | ForEach-Object { Write-Host ("  - " + $_) }
  } elseif ($identical.Count -gt 40) {
    $identical | Select-Object -First 25 | ForEach-Object { Write-Host ("  - " + $_) }
    Write-Host ("  ... +" + ($identical.Count - 25) + " more")
  }
  return $identical.Count
}

$total = 0
foreach ($p in @('admin','store','website')) {
  $en = Join-Path $root "web\$p\resources\lang\en\lang.php"
  $pt = Join-Path $root "web\$p\resources\lang\pt_br\lang.php"
  $total += Count-Identical (Parse-PhpLang $en) (Parse-PhpLang $pt) "web/$p/lang.php"
  $enV = Join-Path $root "web\$p\resources\lang\en\validation.php"
  $ptV = Join-Path $root "web\$p\resources\lang\pt_br\validation.php"
  if ((Test-Path $enV) -and (Test-Path $ptV)) {
    $total += Count-Identical (Parse-PhpLang $enV) (Parse-PhpLang $ptV) "web/$p/validation.php"
  }
}

$apps = @(
  @('customer','lib\lang\app_en.dart','lib\lang\app_pt.dart'),
  @('store','lib\lang\app_en.dart','lib\lang\app_pt.dart'),
  @('driver','lib\lang\app_en.dart','lib\lang\app_pt.dart')
)
foreach ($a in $apps) {
  $en = Join-Path $root ("apps\$($a[0])\$($a[1])")
  $pt = Join-Path $root ("apps\$($a[0])\$($a[2])")
  $total += Count-Identical (Parse-DartMap $en) (Parse-DartMap $pt) "flutter/$($a[0])"
}

Write-Host ("TOTAL real-EN remaining: " + $total)
