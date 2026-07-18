param(
    [string]$PtPath = "D:\arrow\web\admin\resources\lang\pt_br\lang.php",
    [string]$UntranslatedPath = "D:\arrow\scripts\untranslated.json",
    [string[]]$GlossaryPaths = @(
        "D:\arrow\scripts\pt-br-exact.json",
        "D:\arrow\scripts\critical-ui-ptbr.json"
    )
)

$glossary = @{}
foreach ($gp in $GlossaryPaths) {
    if (Test-Path $gp) {
        $obj = Get-Content -Path $gp -Raw -Encoding UTF8 | ConvertFrom-Json
        foreach ($prop in $obj.PSObject.Properties) {
            $glossary[$prop.Name] = $prop.Value
        }
    }
}
Write-Host ("Glossary size: {0}" -f $glossary.Count)

$untranslated = Get-Content -Path $UntranslatedPath -Raw -Encoding UTF8 | ConvertFrom-Json
Write-Host ("Untranslated entries: {0}" -f $untranslated.Count)

$lines = Get-Content -Path $PtPath -Encoding UTF8

$lineRegex2 = [regex]'^(\s*)''((?:[^''\\]|\\.)*)''\s*=>\s*([''"])((?:[^''"\\]|\\.)*)\3(,?\s*(?://.*)?)$'

$applied = 0
$missing = 0
$missingValues = New-Object System.Collections.Generic.List[string]

foreach ($item in $untranslated) {
    $enVal = $item.value
    if ($glossary.ContainsKey($enVal)) {
        $newVal = [string]$glossary[$enVal]
        $lineIdx = $item.lineIndex
        $line = $lines[$lineIdx]
        $quote = $item.quote
        if ($quote -eq "'") {
            $escaped = $newVal.Replace('\', '\\').Replace("'", "\'")
        } else {
            $escaped = $newVal.Replace('\', '\\').Replace('"', '\"')
        }
        $m = $lineRegex2.Match($line)
        if ($m.Success) {
            $indent = $m.Groups[1].Value
            $key = $m.Groups[2].Value
            $trail = $m.Groups[5].Value
            $newLine = "$indent'$key' => $quote$escaped$quote$trail"
            $lines[$lineIdx] = $newLine
            $applied++
        } else {
            $missing++
            $missingValues.Add($enVal)
        }
    } else {
        $missing++
        $missingValues.Add($enVal)
    }
}

Write-Host ("Applied: {0}" -f $applied)
Write-Host ("Missing (no glossary entry or regex mismatch): {0}" -f $missing)

$utf8NoBom = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllLines($PtPath, $lines, $utf8NoBom)
$missingValues | Sort-Object -Unique | Out-File -FilePath "D:\arrow\scripts\missing-after-apply.txt" -Encoding UTF8
Write-Host "Done."
