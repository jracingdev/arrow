param(
    [string]$EnPath = "D:\arrow\web\admin\resources\lang\en\lang.php",
    [string]$PtPath = "D:\arrow\web\admin\resources\lang\pt_br\lang.php",
    [string]$OutPath = "D:\arrow\scripts\untranslated.json"
)

$patterns = @(
    [regex]"^(\s*)'((?:[^'\\]|\\.)*)'\s*=>\s*'((?:[^'\\]|\\.)*)'(,?\s*(?://.*)?)$",
    [regex]('^(\s*)''((?:[^''\\]|\\.)*)''\s*=>\s*"((?:[^"\\]|\\.)*)"(,?\s*(?://.*)?)$'),
    [regex]('^(\s*)"((?:[^"\\]|\\.)*)"\s*=>\s*''((?:[^''\\]|\\.)*)''(,?\s*(?://.*)?)$'),
    [regex]'^(\s*)"((?:[^"\\]|\\.)*)"\s*=>\s*"((?:[^"\\]|\\.)*)"(,?\s*(?://.*)?)$'
)
$quoteForPattern = @("'", '"', "'", '"')

function Parse-LangFile($path) {
    $lines = Get-Content -Path $path -Encoding UTF8
    $entries = @{}
    for ($i = 0; $i -lt $lines.Count; $i++) {
        $line = $lines[$i]
        $matched = $false
        for ($p = 0; $p -lt $patterns.Count; $p++) {
            $m = $patterns[$p].Match($line)
            if ($m.Success) {
                $key = $m.Groups[2].Value
                $val = $m.Groups[3].Value
                $quote = $quoteForPattern[$p]
                if (-not $entries.ContainsKey($key)) {
                    $entries[$key] = New-Object System.Collections.Generic.List[object]
                }
                $entries[$key].Add(@{ LineIndex = $i; Value = $val; Quote = $quote; Occurrence = $entries[$key].Count })
                $matched = $true
                break
            }
        }
    }
    return @{ Lines = $lines; Entries = $entries }
}

Write-Host "Parsing EN..."
$en = Parse-LangFile $EnPath
Write-Host "Parsing PT..."
$pt = Parse-LangFile $PtPath

Write-Host ("EN keys: {0}" -f $en.Entries.Count)
Write-Host ("PT keys: {0}" -f $pt.Entries.Count)

$untranslated = New-Object System.Collections.Generic.List[object]
$totalPt = 0
foreach ($key in $pt.Entries.Keys) {
    $ptList = $pt.Entries[$key]
    foreach ($ptItem in $ptList) {
        $totalPt++
        if ($en.Entries.ContainsKey($key)) {
            $enList = $en.Entries[$key]
            $idx = [Math]::Min($ptItem.Occurrence, $enList.Count - 1)
            $enVal = $enList[$idx].Value
            if ([string]::Equals($ptItem.Value, $enVal, [System.StringComparison]::Ordinal)) {
                $untranslated.Add([PSCustomObject]@{
                    key = $key
                    occurrence = $ptItem.Occurrence
                    lineIndex = $ptItem.LineIndex
                    quote = $ptItem.Quote
                    value = $enVal
                })
            }
        }
    }
}

Write-Host ("Total PT entries: {0}" -f $totalPt)
Write-Host ("Untranslated (identical to EN): {0}" -f $untranslated.Count)

$untranslated | ConvertTo-Json -Depth 5 | Out-File -FilePath $OutPath -Encoding UTF8
Write-Host ("Written to {0}" -f $OutPath)
