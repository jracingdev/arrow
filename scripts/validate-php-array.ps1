param([string]$Path)
$lines = Get-Content -Path $Path -Encoding UTF8
$kvRegex = [regex]'^(\s*)''((?:[^''\\]|\\.)*)''\s*=>\s*([''"])((?:[^''"\\]|\\.)*)\3(,?\s*(?://.*)?)$'
$arrOpenRegex = [regex]'^\s*(?:''[^'']*''|"[^"]*")\s*=>\s*\[\s*$'
$arrCloseRegex = [regex]'^\s*\],?\s*$'
$badLines = New-Object System.Collections.Generic.List[string]
$openCount = 0
$closeCount = 0
$totalKV = 0
for ($i = 0; $i -lt $lines.Count; $i++) {
    $line = $lines[$i]
    if ($line -match '^\s*$' -or $line -match '^\s*//' -or $line -match '^\s*/\*' -or $line -match '^\s*\*' -or $line -match '^<\?php' -or $line -match '^return \[' -or $line -match '^\];?$') {
        continue
    }
    if ($kvRegex.IsMatch($line)) { $totalKV++; continue }
    if ($arrOpenRegex.IsMatch($line)) { $openCount++; continue }
    if ($arrCloseRegex.IsMatch($line)) { $closeCount++; continue }
    $badLines.Add("Line $($i+1): $line")
}
Write-Host ("Total key-value lines matched cleanly: {0}" -f $totalKV)
Write-Host ("Nested array opens: {0}, closes: {1}" -f $openCount, $closeCount)
Write-Host ("Unrecognized lines: {0}" -f $badLines.Count)
$badLines | Select-Object -First 50 | ForEach-Object { Write-Host $_ }

$singleQuoteCount = ([regex]::Matches((Get-Content $Path -Raw), "(?<!\\)'")).Count
$doubleQuoteCount = ([regex]::Matches((Get-Content $Path -Raw), '(?<!\\)"')).Count
Write-Host ("Unescaped single-quote count (should be even): {0}" -f $singleQuoteCount)
Write-Host ("Unescaped double-quote count (should be even): {0}" -f $doubleQuoteCount)
