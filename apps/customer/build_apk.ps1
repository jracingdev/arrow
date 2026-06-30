# Script PowerShell para compilar o APK usando Java 21 do Android Studio
# Este script configura o JAVA_HOME antes de compilar

$env:JAVA_HOME = "C:\Program Files\Android\Android Studio\jbr"
$env:PATH = "$env:JAVA_HOME\bin;$env:PATH"

Write-Host "Configurando Java 21 do Android Studio..." -ForegroundColor Green
java -version

Write-Host "`nLimpando projeto..." -ForegroundColor Yellow
flutter clean

Write-Host "`nObtendo dependencias..." -ForegroundColor Yellow
flutter pub get

Write-Host "`nCompilando APK..." -ForegroundColor Yellow
flutter build apk --debug

if ($LASTEXITCODE -eq 0) {
    Write-Host "`n========================================" -ForegroundColor Green
    Write-Host "APK compilado com sucesso!" -ForegroundColor Green
    Write-Host "Localizacao: build\app\outputs\flutter-apk\app-debug.apk" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
} else {
    Write-Host "`n========================================" -ForegroundColor Red
    Write-Host "Erro ao compilar APK" -ForegroundColor Red
    Write-Host "========================================" -ForegroundColor Red
}

Read-Host "`nPressione Enter para continuar"

