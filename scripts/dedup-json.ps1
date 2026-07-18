param([string]$Path)
$content = Get-Content -Path $Path -Raw -Encoding UTF8
$lineArr = $content -split "`r?`n"
$seen = New-Object System.Collections.Generic.HashSet[string]([System.StringComparer]::OrdinalIgnoreCase)
$out = New-Object System.Collections.Generic.List[string]
$keyRegex = [regex]'^\s*"((?:[^"\\]|\\.)*)"\s*:'
foreach ($line in $lineArr) {
    $m = $keyRegex.Match($line)
    if ($m.Success) {
        $k = $m.Groups[1].Value
        if ($seen.Contains($k)) {
            continue
        }
        [void]$seen.Add($k)
    }
    $out.Add($line)
}
$utf8NoBom = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllText($Path, ($out -join "`n"), $utf8NoBom)
Write-Host ("Deduped {0}. Lines: {1} -> {2}" -f $Path, $lineArr.Count, $out.Count)
